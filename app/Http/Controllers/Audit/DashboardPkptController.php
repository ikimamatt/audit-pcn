<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\JadwalPkptAudit;
use App\Models\RealisasiAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\EntryMeeting;
use App\Models\ExitMeetingUpload;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardPkptController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all Entry Meeting data (not just approved)
        $entryMeetingData = EntryMeeting::with([
            'auditee',
            'programKerjaAudit.perencanaanAudit'
        ]);

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $entryMeetingData->where(function($query) use ($selectedMonth) {
                $query->whereYear('tanggal', $selectedMonth->year)
                      ->whereMonth('tanggal', $selectedMonth->month);
            });
        }

        $entryMeetingData = $entryMeetingData->get();

        // Fetch all Exit Meeting data (not just approved)
        $exitMeetingData = ExitMeetingUpload::with('auditee');

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $exitMeetingData->where(function($query) use ($selectedMonth) {
                $query->whereYear('tanggal_exit_meeting', $selectedMonth->year)
                      ->whereMonth('tanggal_exit_meeting', $selectedMonth->month);
            });
        }

        $exitMeetingData = $exitMeetingData->get();

        $dashboardData = [];
        $months = [];

        // Generate months for the current year
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M'); // e.g., Jan, Feb
            $months[] = $monthName;
        }

        // Process Entry Meeting data
        \Log::info('Starting Entry Meeting processing, total items: ' . $entryMeetingData->count());
        $processedEntryMeetings = 0;
        foreach ($entryMeetingData as $item) {
            $this->processEntryMeetingData($item, $dashboardData, $months);
            $processedEntryMeetings++;
        }
        \Log::info('Entry Meeting processing completed, processed: ' . $processedEntryMeetings . ', dashboard items: ' . count($dashboardData));

        // Process Exit Meeting data
        foreach ($exitMeetingData as $item) {
            $this->processExitMeetingData($item, $dashboardData, $months);
        }

        // Debug information
        \Log::info('Dashboard PKPT Data Counts:', [
            'entry_meeting_count' => $entryMeetingData->count(),
            'exit_meeting_count' => $exitMeetingData->count(),
            'total_dashboard_items' => count($dashboardData)
        ]);

        // Convert associative array to indexed array for easier iteration in Blade
        $dashboardData = array_values($dashboardData);

        return view('audit.dashboard-pkpt.index', compact('dashboardData', 'months', 'entryMeetingData', 'exitMeetingData'));
    }

    /**
     * Process Entry Meeting data
     */
    private function processEntryMeetingData($item, &$dashboardData, $months)
    {
        try {
            // Skip only if absolutely no auditee data
            if (!$item->auditee) {
                \Log::warning('Entry Meeting without auditee, skipping: ' . $item->id);
                return;
            }

            $auditee = $item->auditee;
            $direktorat = data_get($auditee, 'direktorat');
            $divisiCabang = data_get($auditee, 'divisi_cabang');
            $divisi = data_get($auditee, 'divisi');

            // Build auditee name
            if (!empty($direktorat) || !empty($divisiCabang)) {
                $auditeeName = trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                $auditeeName = trim($auditeeName, '- ');
            } elseif (!empty($divisi)) {
                $auditeeName = $divisi;
            } else {
                $auditeeName = 'Unknown Auditee';
            }

            // Get audit type - use fallback if no program kerja audit
            $jenisAudit = 'Audit Operasional'; // Default fallback
            if ($item->programKerjaAudit && $item->programKerjaAudit->perencanaanAudit) {
                $jenisAudit = $item->programKerjaAudit->perencanaanAudit->jenis_audit ?? $jenisAudit;
            }
            
            // Create unique key for each entry meeting item
            $key = 'entry_meeting_' . $item->id;

            // Get planning dates from milestones if available
            $planningStart = '-';
            $planningEnd = '-';
            if ($item->programKerjaAudit) {
                $planningStart = $this->getPlanningStartDateFromMilestones($item->programKerjaAudit);
                $planningEnd = $this->getPlanningEndDateFromMilestones($item->programKerjaAudit);
            }

            // Count PKA for this period
            $jumlahPka = 1; // Default value
            if ($item->programKerjaAudit && $item->programKerjaAudit->perencanaan_audit_id) {
                $jumlahPka = ProgramKerjaAudit::where('perencanaan_audit_id', $item->programKerjaAudit->perencanaan_audit_id)->count();
            }

            // Get auditor count
            $jumlahAuditor = 1; // Default value
            if ($item->programKerjaAudit && $item->programKerjaAudit->perencanaan_audit_id) {
                $jumlahAuditor = $this->getAuditorCountFromPerencanaan($item->programKerjaAudit->perencanaan_audit_id);
            }

            // Create new entry for each item (no grouping, no filtering)
            $dashboardData[$key] = [
                'auditee' => $auditeeName,
                'jenis_audit' => $jenisAudit,
                'jumlah_auditor' => $jumlahAuditor,
                'jumlah_pka' => $jumlahPka,
                'rencana_audit_mulai' => $planningStart,
                'rencana_audit_selesai' => $planningEnd,
                'realisasi_audit_mulai' => $item->actual_meeting_date ? Carbon::parse($item->actual_meeting_date)->format('d M Y') : '-',
                'realisasi_audit_selesai' => '-',
                'status_realisasi' => $this->translateStatus('on progress'),
                'schedule' => array_fill_keys($months, []),
                'source' => 'entry_meeting'
            ];

            // Populate months with audit schedule based on actual meeting date
            if ($item->actual_meeting_date) {
                $meetingDate = Carbon::parse($item->actual_meeting_date);
                $monthName = $meetingDate->translatedFormat('M');
                
                if (in_array($monthName, $months)) {
                    $dashboardData[$key]['schedule'][$monthName][] = $item->id;
                }
            }

            \Log::info('Entry Meeting processed successfully', [
                'id' => $item->id,
                'auditee' => $auditeeName,
                'jenis_audit' => $jenisAudit,
                'key' => $key
            ]);

        } catch (\Exception $e) {
            // Log error but continue processing other items
            \Log::error('Error processing Entry Meeting data: ' . $e->getMessage(), [
                'item_id' => $item->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Process Exit Meeting data
     */
    private function processExitMeetingData($item, &$dashboardData, $months)
    {
        try {
            if (!$item->auditee) {
                return;
            }

            $auditee = $item->auditee;
            $direktorat = data_get($auditee, 'direktorat');
            $divisiCabang = data_get($auditee, 'divisi_cabang');
            $divisi = data_get($auditee, 'divisi');

            if (!empty($direktorat) || !empty($divisiCabang)) {
                $auditeeName = trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                $auditeeName = trim($auditeeName, '- ');
            } elseif (!empty($divisi)) {
                $auditeeName = $divisi;
            } else {
                $auditeeName = 'Unknown';
            }

            // For Exit Meeting, we need to find the corresponding audit type
            // Since ExitMeetingUpload doesn't have direct audit type, we'll use a generic one
            $jenisAudit = 'Audit Operasional'; // Default value
            
            // Create unique key for each exit meeting item to avoid grouping
            $key = 'exit_meeting_' . $item->id;

            // Create new entry for each item (no grouping)
            $dashboardData[$key] = [
                'auditee' => $auditeeName,
                'jenis_audit' => $jenisAudit,
                'jumlah_auditor' => 1, // Default value
                'jumlah_pka' => 1, // Default value
                'rencana_audit_mulai' => '-',
                'rencana_audit_selesai' => '-',
                'realisasi_audit_mulai' => $item->tanggal_exit_meeting ? Carbon::parse($item->tanggal_exit_meeting)->format('d M Y') : '-',
                'realisasi_audit_selesai' => $item->tanggal_exit_meeting ? Carbon::parse($item->tanggal_exit_meeting)->format('d M Y') : '-',
                'status_realisasi' => $this->translateStatus('selesai'),
                'schedule' => array_fill_keys($months, []),
                'source' => 'exit_meeting'
            ];

            // Populate months with audit schedule based on exit meeting date
            if ($item->tanggal_exit_meeting) {
                $meetingDate = Carbon::parse($item->tanggal_exit_meeting);
                $monthName = $meetingDate->translatedFormat('M');
                
                if (in_array($monthName, $months)) {
                    $dashboardData[$key]['schedule'][$monthName][] = $item->id;
                }
            }
        } catch (\Exception $e) {
            // Log error but continue processing other items
            \Log::error('Error processing Exit Meeting data: ' . $e->getMessage(), [
                'item_id' => $item->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get planning start date from milestones (Entry Meeting and Exit Meeting)
     */
    private function getPlanningStartDateFromMilestones($programKerjaAudit)
    {
        try {
            if ($programKerjaAudit && $programKerjaAudit->milestones && $programKerjaAudit->milestones->count() > 0) {
                // Look for Entry Meeting milestone first
                $entryMeetingMilestone = $programKerjaAudit->milestones->where('nama_milestone', 'Entry Meeting')->first();
                if ($entryMeetingMilestone && $entryMeetingMilestone->tanggal_mulai) {
                    return Carbon::parse($entryMeetingMilestone->tanggal_mulai)->format('d M Y');
                }
                
                // If no Entry Meeting milestone, get the earliest milestone
                $firstMilestone = $programKerjaAudit->milestones->sortBy('tanggal_mulai')->first();
                if ($firstMilestone && $firstMilestone->tanggal_mulai) {
                    return Carbon::parse($firstMilestone->tanggal_mulai)->format('d M Y');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error getting planning start date from milestones: ' . $e->getMessage(), [
                'error' => $e->getMessage()
            ]);
        }
        return '-';
    }

    /**
     * Get planning end date from milestones (Entry Meeting and Exit Meeting)
     */
    private function getPlanningEndDateFromMilestones($programKerjaAudit)
    {
        try {
            if ($programKerjaAudit && $programKerjaAudit->milestones && $programKerjaAudit->milestones->count() > 0) {
                // Look for Exit Meeting milestone first
                $exitMeetingMilestone = $programKerjaAudit->milestones->where('nama_milestone', 'Exit Meeting')->first();
                if ($exitMeetingMilestone && $exitMeetingMilestone->tanggal_selesai) {
                    return Carbon::parse($exitMeetingMilestone->tanggal_selesai)->format('d M Y');
                }
                
                // If no Exit Meeting milestone, get the latest milestone
                $lastMilestone = $programKerjaAudit->milestones->sortByDesc('tanggal_selesai')->first();
                if ($lastMilestone && $lastMilestone->tanggal_selesai) {
                    return Carbon::parse($lastMilestone->tanggal_selesai)->format('d M Y');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error getting planning end date from milestones: ' . $e->getMessage(), [
                'error' => $e->getMessage()
            ]);
        }
        return '-';
    }

    /**
     * Get auditor count from perencanaan audit
     */
    private function getAuditorCountFromPerencanaan($perencanaanAuditId)
    {
        try {
            $perencanaanAudit = PerencanaanAudit::find($perencanaanAuditId);
            if ($perencanaanAudit && $perencanaanAudit->auditor) {
                $auditors = $perencanaanAudit->auditor;
                
                // Check if it's already an array or JSON string
                if (is_array($auditors)) {
                    return count($auditors);
                } elseif (is_string($auditors)) {
                    $decoded = json_decode($auditors, true);
                    return is_array($decoded) ? count($decoded) : 1;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error getting auditor count: ' . $e->getMessage());
        }
        return 1;
    }

    /**
     * Translate status to Indonesian
     */
    private function translateStatus($status)
    {
        switch (strtolower($status)) {
            case 'selesai':
                return 'Selesai';
            case 'on progress':
                return 'Sedang Berlangsung';
            case 'belum':
                return 'Belum Dimulai';
            default:
                return ucfirst($status);
        }
    }

    private function determineStatus($pkptItem, $realisasiAudit)
    {
        $today = Carbon::now();
        $plannedStart = Carbon::parse($pkptItem->tanggal_mulai);
        $plannedEnd = Carbon::parse($pkptItem->tanggal_selesai);

        if ($realisasiAudit) {
            // If there's realization data, use its status
            $status = $realisasiAudit->status;
            
            // Translate status to Indonesian
            switch (strtolower($status)) {
                case 'selesai':
                    return 'Selesai';
                case 'on progress':
                    return 'Sedang Berlangsung';
                case 'belum':
                    return 'Belum Dimulai';
                default:
                    return ucfirst($status);
            }
        } else {
            // If no realization data, determine status based on dates
            if ($today->lt($plannedStart)) {
                return 'Belum Dimulai';
            } elseif ($today->between($plannedStart, $plannedEnd)) {
                return 'Sedang Berlangsung';
            } elseif ($today->gt($plannedEnd)) {
                return 'Terlambat';
            } else {
                return 'Belum';
            }
        }
    }
}
