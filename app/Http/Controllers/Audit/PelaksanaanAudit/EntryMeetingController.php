<?php

namespace App\Http\Controllers\Audit\PelaksanaanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreEntryMeetingRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateEntryMeetingRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Models\EntryMeeting;
use App\Models\MasterData\MasterAuditee;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Services\Audit\EntryMeetingService;
use Carbon\Carbon;

class EntryMeetingController extends Controller
{
    protected $entryMeetingService;

    public function __construct(EntryMeetingService $entryMeetingService)
    {
        $this->entryMeetingService = $entryMeetingService;
    }

    public function index(Request $request)
    {
        // Pindahkan filter ke DB-level dengan scope forCurrentAuditee
        $query = EntryMeeting::with(['auditee', 'programKerjaAudit.perencanaanAudit'])
            ->forCurrentAuditee('programKerjaAudit.perencanaanAudit');

        // Filter by specific ID from details page
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $query->whereHas('programKerjaAudit.perencanaanAudit', function($q) use ($selectedMonth) {
                $q->whereYear('tanggal_audit_mulai', $selectedMonth->year)
                  ->whereMonth('tanggal_audit_mulai', $selectedMonth->month);
            });
        }

        $data = $query->get();

        return view('audit.entry-meeting.index', compact('data'));
    }

    public function create(Request $request)
    {
        $auditees = MasterAuditee::all();
        $returnUrl = $request->query('return_url');
        
        try {
            // Ambil PKA yang memiliki milestone Entry Meeting dan belum memiliki Entry Meeting
            // atau Entry Meeting yang sudah di-reject (bisa diajukan ulang)
            $programKerjaAudit = ProgramKerjaAudit::whereHas('milestones', function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            })->whereDoesntHave('entryMeeting', function($query) {
                // Exclude PKA yang sudah memiliki Entry Meeting dengan status approved atau pending
                $query->whereIn('status_approval', ['approved', 'pending']);
            })->with(['perencanaanAudit.auditee', 'milestones' => function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            }, 'entryMeeting' => function($query) {
                // Include rejected Entry Meeting untuk ditampilkan sebagai "Reject - Ajukan Ulang"
                $query->where('status_approval', 'rejected');
            }])->get();
        } catch (\Exception $e) {
            // Fallback jika ada error dengan query yang kompleks
            $programKerjaAudit = ProgramKerjaAudit::whereHas('milestones', function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            })->with(['perencanaanAudit.auditee', 'milestones' => function($query) {
                $query->where('nama_milestone', 'Entry Meeting');
            }])->get();
        }
        
        return view('audit.entry-meeting.create', compact('auditees', 'programKerjaAudit', 'returnUrl'));
    }

    public function store(StoreEntryMeetingRequest $request)
    {
        $data = $request->validated();
        $data['file_undangan_file'] = $request->file('file_undangan');
        $data['file_absensi_file'] = $request->file('file_absensi');

        $this->entryMeetingService->create($data);

        return redirect()->route('audit.entry-meeting.index')->with('success', 'Entry Meeting berhasil disimpan!');
    }

    public function edit($id, Request $request)
    {
        $item = EntryMeeting::with(['programKerjaAudit.perencanaanAudit'])->findOrFail($id);
        $auditees = MasterAuditee::all();
        $returnUrl = $request->query('return_url');
        
        return view('audit.entry-meeting.edit', compact('item', 'auditees', 'returnUrl'));
    }

    public function update(UpdateEntryMeetingRequest $request, $id)
    {
        $item = EntryMeeting::findOrFail($id);

        $data = $request->validated();
        if ($request->hasFile('file_undangan')) {
            $data['file_undangan_file'] = $request->file('file_undangan');
        }
        if ($request->hasFile('file_absensi')) {
            $data['file_absensi_file'] = $request->file('file_absensi');
        }

        $this->entryMeetingService->update($item, $data);
        return redirect()->route('audit.entry-meeting.index')->with('success', 'Entry Meeting berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = EntryMeeting::findOrFail($id);
        $this->entryMeetingService->delete($item);
        return redirect()->route('audit.entry-meeting.index')->with('success', 'Entry Meeting berhasil dihapus!');
    }

    public function approval($id, ApprovalRequest $request)
    {
        $item = EntryMeeting::findOrFail($id);

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