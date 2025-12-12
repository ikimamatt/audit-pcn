<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\WalkthroughAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'file_bpm' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        // Ambil data PKA untuk mendapatkan planned date dari milestone
        $pka = ProgramKerjaAudit::with(['milestones' => function($query) {
            $query->where('nama_milestone', 'Walkthrough');
        }])->findOrFail($request->program_kerja_audit_id);

        $plannedDate = $pka->milestones->first()->tanggal_mulai ?? $request->planned_walkthrough_date;

        // Ambil nama auditee dari master auditee
        $auditee = MasterAuditee::findOrFail($request->auditee_id);

        // Handle file upload
        $fileBpmPath = null;
        if ($request->hasFile('file_bpm')) {
            $fileBpmPath = $request->file('file_bpm')->store('walkthrough/bpm', 'public');
        }

        WalkthroughAudit::create([
            'perencanaan_audit_id' => $pka->perencanaan_audit_id,
            'program_kerja_audit_id' => $request->program_kerja_audit_id,
            'planned_walkthrough_date' => $plannedDate,
            'actual_walkthrough_date' => $request->actual_walkthrough_date,
            'tanggal_walkthrough' => $request->actual_walkthrough_date ?? $plannedDate,
            'auditee_nama' => $auditee->divisi,
            'hasil_walkthrough' => $request->hasil_walkthrough,
            'file_bpm' => $fileBpmPath,
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
            'file_bpm' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        // Ambil data PKA untuk mendapatkan planned date dari milestone
        $pka = ProgramKerjaAudit::with(['milestones' => function($query) {
            $query->where('nama_milestone', 'Walkthrough');
        }])->findOrFail($request->program_kerja_audit_id);

        $plannedDate = $pka->milestones->first()->tanggal_mulai ?? $request->planned_walkthrough_date;

        // Ambil nama auditee dari master auditee
        $auditee = MasterAuditee::findOrFail($request->auditee_id);

        // Handle file upload
        $updateData = [
            'perencanaan_audit_id' => $pka->perencanaan_audit_id,
            'program_kerja_audit_id' => $request->program_kerja_audit_id,
            'planned_walkthrough_date' => $plannedDate,
            'actual_walkthrough_date' => $request->actual_walkthrough_date,
            'tanggal_walkthrough' => $request->actual_walkthrough_date ?? $plannedDate,
            'auditee_nama' => $auditee->divisi,
            'hasil_walkthrough' => $request->hasil_walkthrough,
        ];

        if ($request->hasFile('file_bpm')) {
            // Hapus file lama jika ada
            if ($item->file_bpm && Storage::disk('public')->exists($item->file_bpm)) {
                Storage::disk('public')->delete($item->file_bpm);
            }
            $updateData['file_bpm'] = $request->file('file_bpm')->store('walkthrough/bpm', 'public');
        }

        $item->update($updateData);

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
        
        // Validasi alasan penolakan jika reject
        if ($request->input('action') === 'reject') {
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            ]);
        }

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
