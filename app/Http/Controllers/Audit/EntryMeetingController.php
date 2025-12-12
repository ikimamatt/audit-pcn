<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EntryMeeting;
use App\Models\MasterData\MasterAuditee;
use App\Models\Models\Audit\ProgramKerjaAudit;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EntryMeetingController extends Controller
{
    public function index(Request $request)
    {
        // Simple approach - get all data and filter in memory
        $data = EntryMeeting::with(['auditee', 'programKerjaAudit.perencanaanAudit'])->get();

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            
            $data = $data->filter(function($item) use ($selectedMonth) {
                if (!$item->programKerjaAudit || !$item->programKerjaAudit->perencanaanAudit) {
                    return false;
                }
                
                $auditStart = Carbon::parse($item->programKerjaAudit->perencanaanAudit->tanggal_audit_mulai);
                return $auditStart->year == $selectedMonth->year && 
                       $auditStart->month == $selectedMonth->month;
            });
        }

        return view('audit.entry-meeting.index', compact('data'));
    }

    public function create()
    {
        $auditees = MasterAuditee::all();
        
        try {
            // Ambil PKA yang memiliki milestone Entry Meeting dan belum memiliki Entry Meeting
            // atau Entry Meeting yang sudah di-reject (bisa diajukan ulang)
            $programKerjaAudit = ProgramKerjaAudit::whereHas('milestones', function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            })->whereDoesntHave('entryMeeting', function($query) {
                // Exclude PKA yang sudah memiliki Entry Meeting dengan status approved atau pending
                $query->whereIn('status_approval', ['approved', 'pending']);
            })->with(['perencanaanAudit', 'milestones' => function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            }, 'entryMeeting' => function($query) {
                // Include rejected Entry Meeting untuk ditampilkan sebagai "Reject - Ajukan Ulang"
                $query->where('status_approval', 'rejected');
            }])->get();
        } catch (\Exception $e) {
            // Fallback jika ada error dengan query yang kompleks
            $programKerjaAudit = ProgramKerjaAudit::whereHas('milestones', function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            })->with(['perencanaanAudit', 'milestones' => function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            }])->get();
        }
        
        return view('audit.entry-meeting.create', compact('auditees', 'programKerjaAudit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_kerja_audit_id' => 'required|exists:program_kerja_audit,id',
            'planned_meeting_date' => 'required|date',
            'actual_meeting_date' => 'nullable|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'file_undangan' => 'required|file',
            'file_absensi' => 'required|file',
        ]);

        $undanganPath = $request->file('file_undangan')->store('entry_meeting', 'public');
        $absensiPath = $request->file('file_absensi')->store('entry_meeting', 'public');
        
        EntryMeeting::create([
            'program_kerja_audit_id' => $request->program_kerja_audit_id,
            'tanggal' => $request->planned_meeting_date, // planned meeting date
            'actual_meeting_date' => $request->actual_meeting_date,
            'auditee_id' => $request->auditee_id,
            'file_undangan' => $undanganPath,
            'file_absensi' => $absensiPath,
        ]);

        return redirect()->route('audit.entry-meeting.index')->with('success', 'Entry Meeting berhasil disimpan!');
    }

    public function edit($id)
    {
        $item = EntryMeeting::with(['programKerjaAudit.perencanaanAudit'])->findOrFail($id);
        $auditees = MasterAuditee::all();
        
        return view('audit.entry-meeting.edit', compact('item', 'auditees'));
    }

    public function update(Request $request, $id)
    {
        $item = EntryMeeting::findOrFail($id);
        $request->validate([
            'program_kerja_audit_id' => 'required|exists:program_kerja_audit,id',
            'planned_meeting_date' => 'required|date',
            'actual_meeting_date' => 'nullable|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'file_undangan' => 'nullable|file',
            'file_absensi' => 'nullable|file',
        ]);

        $data = [
            'program_kerja_audit_id' => $request->program_kerja_audit_id,
            'tanggal' => $request->planned_meeting_date, // planned meeting date
            'actual_meeting_date' => $request->actual_meeting_date,
            'auditee_id' => $request->auditee_id,
        ];

        if ($request->hasFile('file_undangan')) {
            $data['file_undangan'] = $request->file('file_undangan')->store('entry_meeting', 'public');
        }
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi'] = $request->file('file_absensi')->store('entry_meeting', 'public');
        }

        $item->update($data);
        return redirect()->route('audit.entry-meeting.index')->with('success', 'Entry Meeting berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = EntryMeeting::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.entry-meeting.index')->with('success', 'Entry Meeting berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = EntryMeeting::findOrFail($id);
        
        // Validasi alasan penolakan jika reject
        if ($request->action == 'reject') {
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            ]);
        }

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->action,
            $request->rejection_reason ?? null
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
} 