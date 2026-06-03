<?php

namespace App\Http\Controllers\Audit\PelaksanaanAudit;

use App\Http\Controllers\Controller;
use App\Models\WalkthroughAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreWalkthroughRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateWalkthroughRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Services\Audit\WalkthroughService;
use Carbon\Carbon;

class WalkthroughAuditController extends Controller
{
    protected $walkthroughService;

    public function __construct(WalkthroughService $walkthroughService)
    {
        $this->walkthroughService = $walkthroughService;
    }

    public function index(Request $request)
    {
        // Pindahkan filter ke DB-level dengan scope forCurrentAuditee
        $query = WalkthroughAudit::with(['perencanaanAudit.auditee', 'programKerjaAudit.perencanaanAudit'])
            ->forCurrentAuditee('perencanaanAudit');

        // Filter by specific ID from details page
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $query->whereHas('perencanaanAudit', function($q) use ($selectedMonth) {
                $q->whereYear('tanggal_audit_mulai', $selectedMonth->year)
                  ->whereMonth('tanggal_audit_mulai', $selectedMonth->month);
            });
        }

        $data = $query->get();

        return view('audit.walkthrough.index', compact('data'));
    }

    public function create()
    {
        // Ambil Program Kerja Audit yang memiliki milestone 'Walkthrough'
        // Exclude yang sudah approved atau pending, tapi include yang rejected
        try {
            $programKerjaAudit = ProgramKerjaAudit::whereHas('milestones', function($query) {
                $query->where('nama_milestone', 'Walkthrough');
            })
            ->whereDoesntHave('walkthroughAudit', function($query) {
                $query->whereIn('status_approval', ['approved', 'pending']);
            })
            ->with(['perencanaanAudit.auditee', 'milestones' => function($query) {
                $query->where('nama_milestone', 'Walkthrough');
            }, 'walkthroughAudit' => function($query) {
                $query->where('status_approval', 'rejected');
            }])
            ->get();
        } catch (\Exception $e) {
            // Fallback jika query kompleks gagal
            $programKerjaAudit = ProgramKerjaAudit::whereHas('milestones', function($query) {
                $query->where('nama_milestone', 'Walkthrough');
            })
            ->with(['perencanaanAudit.auditee', 'milestones' => function($query) {
                $query->where('nama_milestone', 'Walkthrough');
            }])
            ->get();
        }
        
        // Ambil data master auditee
        $auditees = MasterAuditee::all();
        
        return view('audit.walkthrough.create', compact('programKerjaAudit', 'auditees'));
    }

    public function store(StoreWalkthroughRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('file_bpm')) {
            $data['file_bpm_file'] = $request->file('file_bpm');
        }

        $this->walkthroughService->create($data);

        return redirect()->route('audit.walkthrough.index')->with('success', 'Hasil walkthrough berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = WalkthroughAudit::with(['programKerjaAudit.perencanaanAudit'])->findOrFail($id);
        
        // Ambil data master auditee
        $auditees = MasterAuditee::all();
        
        return view('audit.walkthrough.edit', compact('item', 'auditees'));
    }

    public function update(UpdateWalkthroughRequest $request, $id)
    {
        $item = WalkthroughAudit::findOrFail($id);

        $data = $request->validated();
        if ($request->hasFile('file_bpm')) {
            $data['file_bpm_file'] = $request->file('file_bpm');
        }

        $this->walkthroughService->update($item, $data);

        return redirect()->route('audit.walkthrough.index')->with('success', 'Hasil walkthrough berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = WalkthroughAudit::findOrFail($id);
        $this->walkthroughService->delete($item);
        return redirect()->route('audit.walkthrough.index')->with('success', 'Data walkthrough berhasil dihapus!');
    }

    public function approval(ApprovalRequest $request, $id)
    {
        $item = WalkthroughAudit::findOrFail($id);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->input('action'),
            $request->rejection_reason ?? null
        );

        if ($result['success']) {
            return redirect()->route('audit.walkthrough.index')->with('success', $result['message']);
        }

        return redirect()->route('audit.walkthrough.index')->with('error', $result['message']);
    }
}
