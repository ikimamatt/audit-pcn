<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\WalkthroughAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WalkthroughAuditController extends Controller
{
    public function index(Request $request)
    {
        // Simple approach - get all data and filter in memory
        $data = WalkthroughAudit::with(['perencanaanAudit', 'programKerjaAudit.perencanaanAudit'])->get();

        // Filter by month if provided
        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            
            $data = $data->filter(function($item) use ($selectedMonth) {
                if (!$item->perencanaanAudit) {
                    return false;
                }
                
                $auditStart = Carbon::parse($item->perencanaanAudit->tanggal_audit_mulai);
                return $auditStart->year == $selectedMonth->year && 
                       $auditStart->month == $selectedMonth->month;
            });
        }

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
            ->with(['perencanaanAudit', 'milestones' => function($query) {
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
            ->with(['perencanaanAudit', 'milestones' => function($query) {
                $query->where('nama_milestone', 'Walkthrough');
            }])
            ->get();
        }
        
        // Ambil data master auditee
        $auditees = MasterAuditee::all();
        
        return view('audit.walkthrough.create', compact('programKerjaAudit', 'auditees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_kerja_audit_id' => 'required|exists:program_kerja_audit,id',
            'planned_walkthrough_date' => 'required|date',
            'actual_walkthrough_date' => 'nullable|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'hasil_walkthrough' => 'required|string',
        ]);

        // Ambil data PKA untuk mendapatkan planned date dari milestone
        $pka = ProgramKerjaAudit::with(['milestones' => function($query) {
            $query->where('nama_milestone', 'Walkthrough');
        }])->findOrFail($request->program_kerja_audit_id);

        $plannedDate = $pka->milestones->first()->tanggal_mulai ?? $request->planned_walkthrough_date;

        // Ambil nama auditee dari master auditee
        $auditee = MasterAuditee::findOrFail($request->auditee_id);

        WalkthroughAudit::create([
            'perencanaan_audit_id' => $pka->perencanaan_audit_id,
            'program_kerja_audit_id' => $request->program_kerja_audit_id,
            'planned_walkthrough_date' => $plannedDate,
            'actual_walkthrough_date' => $request->actual_walkthrough_date,
            'tanggal_walkthrough' => $request->actual_walkthrough_date ?? $plannedDate,
            'auditee_nama' => $auditee->divisi,
            'hasil_walkthrough' => $request->hasil_walkthrough,
        ]);

        return redirect()->route('audit.walkthrough.index')->with('success', 'Hasil walkthrough berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = WalkthroughAudit::with(['programKerjaAudit.perencanaanAudit'])->findOrFail($id);
        
        // Ambil data master auditee
        $auditees = MasterAuditee::all();
        
        return view('audit.walkthrough.edit', compact('item', 'auditees'));
    }

    public function update(Request $request, $id)
    {
        $item = WalkthroughAudit::findOrFail($id);
        
        $request->validate([
            'program_kerja_audit_id' => 'required|exists:program_kerja_audit,id',
            'planned_walkthrough_date' => 'required|date',
            'actual_walkthrough_date' => 'nullable|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'hasil_walkthrough' => 'required|string',
        ]);

        // Ambil data PKA untuk mendapatkan planned date dari milestone
        $pka = ProgramKerjaAudit::with(['milestones' => function($query) {
            $query->where('nama_milestone', 'Walkthrough');
        }])->findOrFail($request->program_kerja_audit_id);

        $plannedDate = $pka->milestones->first()->tanggal_mulai ?? $request->planned_walkthrough_date;

        // Ambil nama auditee dari master auditee
        $auditee = MasterAuditee::findOrFail($request->auditee_id);

        $item->update([
            'perencanaan_audit_id' => $pka->perencanaan_audit_id,
            'program_kerja_audit_id' => $request->program_kerja_audit_id,
            'planned_walkthrough_date' => $plannedDate,
            'actual_walkthrough_date' => $request->actual_walkthrough_date,
            'tanggal_walkthrough' => $request->actual_walkthrough_date ?? $plannedDate,
            'auditee_nama' => $auditee->divisi,
            'hasil_walkthrough' => $request->hasil_walkthrough,
        ]);

        return redirect()->route('audit.walkthrough.index')->with('success', 'Hasil walkthrough berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = WalkthroughAudit::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.walkthrough.index')->with('success', 'Data walkthrough berhasil dihapus!');
    }

    public function approval(Request $request, $id)
    {
        $item = WalkthroughAudit::findOrFail($id);
        $action = $request->input('action');

        if ($action === 'approve') {
            $item->update([
                'status_approval' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            return redirect()->route('audit.walkthrough.index')->with('success', 'Walkthrough berhasil diapprove!');
        } elseif ($action === 'reject') {
            // Validasi alasan penolakan
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            ]);

            $item->update([
                'status_approval' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);
            return redirect()->route('audit.walkthrough.index')->with('success', 'Walkthrough berhasil ditolak dengan alasan: ' . $request->rejection_reason);
        }

        return redirect()->route('audit.walkthrough.index')->with('error', 'Aksi tidak valid!');
    }
}
