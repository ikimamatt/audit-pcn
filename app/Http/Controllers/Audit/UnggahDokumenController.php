<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterAuditee;
use App\Models\Models\Audit\PelaporanHasilAudit;
use Illuminate\Http\Request;
use App\Models\ExitMeetingUpload;
use App\Models\LhaLhkUpload;
use App\Models\NotaDinasUpload;

class UnggahDokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data ExitMeetingUpload dan relasi ke LhaLhkUpload, NotaDinasUpload, Auditee
        $exitMeetings = ExitMeetingUpload::with(['auditee', 'lhaLhk', 'notaDinas'])->get();
        // Gabungkan data untuk DataTables
        $data = $exitMeetings->map(function($exitMeeting) {
            return (object) [
                'id' => $exitMeeting->id,
                'exitMeeting' => $exitMeeting,
                'auditee' => $exitMeeting->auditee,
                'lhaLhk' => $exitMeeting->lhaLhk,
                'notaDinas' => $exitMeeting->notaDinas,
            ];
        });
        return view('audit.unggah-dokumen.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $auditees = MasterAuditee::all();
        $lhaList = PelaporanHasilAudit::all();

        if ($request->ajax()) {
            return view('audit.unggah-dokumen._form', compact('auditees', 'lhaList'));
        }

        return view('audit.unggah-dokumen.index', compact('auditees', 'lhaList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_exit_meeting' => 'required|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'file_undangan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_absensi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'lha_lhk_id' => 'required|exists:pelaporan_hasil_audit,id',
            'file_lha_lhk' => 'required|file|mimes:pdf,doc,docx|max:4096',
            'tujuan_nota_dinas' => 'required|in:dirut,dekom,auditee',
            'file_nota_dinas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        // Simpan Exit Meeting
        $undanganPath = $request->file('file_undangan')->store('exit_meeting/undangan', 'public');
        $absensiPath = $request->file('file_absensi')->store('exit_meeting/absensi', 'public');
        $exitMeeting = \App\Models\ExitMeetingUpload::create([
            'tanggal_exit_meeting' => $request->tanggal_exit_meeting,
            'auditee_id' => $request->auditee_id,
            'file_undangan' => $undanganPath,
            'file_absensi' => $absensiPath,
        ]);

        // Simpan LHA/LHK
        $fileLhaLhkPath = $request->file('file_lha_lhk')->store('lha_lhk', 'public');
        $lhaLhk = \App\Models\LhaLhkUpload::create([
            'pelaporan_hasil_audit_id' => $request->lha_lhk_id,
            'file_lha_lhk' => $fileLhaLhkPath,
        ]);

        // Simpan Nota Dinas
        $fileNota = $request->file('file_nota_dinas')->store('nota_dinas/' . $request->tujuan_nota_dinas, 'public');
        $nota = \App\Models\NotaDinasUpload::create([
            'pelaporan_hasil_audit_id' => $request->lha_lhk_id,
            'file_nota_dinas' => $fileNota,
            'tujuan_nota_dinas' => $request->tujuan_nota_dinas,
        ]);

        return redirect()->route('audit.unggah-dokumen.index')->with('success', 'Dokumen berhasil diunggah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, Request $request)
    {
        $edit = ExitMeetingUpload::with(['auditee', 'lhaLhk', 'notaDinas'])->findOrFail($id);
        $auditees = MasterAuditee::all();
        $lhaList = PelaporanHasilAudit::all();

        if ($request->ajax()) {
            return view('audit.unggah-dokumen._form', compact('edit', 'auditees', 'lhaList'));
        }

        return view('audit.unggah-dokumen.edit', compact('edit', 'auditees', 'lhaList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_exit_meeting' => 'required|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'lha_lhk_id' => 'required|exists:pelaporan_hasil_audit,id',
            'tujuan_nota_dinas' => 'required|in:dirut,dekom,auditee',
        ]);
        $exitMeeting = \App\Models\ExitMeetingUpload::findOrFail($id);
        // Update data utama
        $exitMeeting->tanggal_exit_meeting = $request->tanggal_exit_meeting;
        $exitMeeting->auditee_id = $request->auditee_id;
        if ($request->hasFile('file_undangan')) {
            $exitMeeting->file_undangan = $request->file('file_undangan')->store('exit_meeting/undangan', 'public');
        }
        if ($request->hasFile('file_absensi')) {
            $exitMeeting->file_absensi = $request->file('file_absensi')->store('exit_meeting/absensi', 'public');
        }
        $exitMeeting->save();
        // Update LHA/LHK berdasarkan id relasi exit meeting
        $lhaLhk = \App\Models\LhaLhkUpload::where('id', $exitMeeting->id)->first();
        if ($lhaLhk) {
            $lhaLhk->pelaporan_hasil_audit_id = $request->lha_lhk_id;
            if ($request->hasFile('file_lha_lhk')) {
                $lhaLhk->file_lha_lhk = $request->file('file_lha_lhk')->store('lha_lhk', 'public');
            }
            $lhaLhk->save();
        }
        // Update Nota Dinas berdasarkan id relasi exit meeting
        $notaDinas = \App\Models\NotaDinasUpload::where('id', $exitMeeting->id)->first();
        if ($notaDinas) {
            $notaDinas->pelaporan_hasil_audit_id = $request->lha_lhk_id;
            $notaDinas->tujuan_nota_dinas = $request->tujuan_nota_dinas;
            if ($request->hasFile('file_nota_dinas')) {
                $notaDinas->file_nota_dinas = $request->file('file_nota_dinas')->store('nota_dinas/' . $request->tujuan_nota_dinas, 'public');
            }
            $notaDinas->save();
        }
        return redirect()->route('audit.unggah-dokumen.index')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $exitMeeting = \App\Models\ExitMeetingUpload::findOrFail($id);
        $lhaLhk = $exitMeeting->lhaLhk;
        $notaDinas = $exitMeeting->notaDinas;
        // Hapus file
        if ($exitMeeting->file_undangan) \Storage::disk('public')->delete($exitMeeting->file_undangan);
        if ($exitMeeting->file_absensi) \Storage::disk('public')->delete($exitMeeting->file_absensi);
        if ($lhaLhk && $lhaLhk->file_lha_lhk) \Storage::disk('public')->delete($lhaLhk->file_lha_lhk);
        if ($notaDinas) {
            if ($notaDinas->file_nota_dinas) \Storage::disk('public')->delete($notaDinas->file_nota_dinas);
        }
        // Hapus data
        if ($lhaLhk) $lhaLhk->delete();
        if ($notaDinas) $notaDinas->delete();
        $exitMeeting->delete();
        return redirect()->route('audit.unggah-dokumen.index')->with('success', 'Data berhasil dihapus!');
    }

    public function approve(Request $request, $id)
    {
        try {
            $type = $request->input('type'); // Ambil 'type' dari body request
            $userId = auth()->id() ?? 1; // fallback ke 1 jika belum login

            if ($type === 'undangan') {
                $exitMeeting = ExitMeetingUpload::findOrFail($id);
                $exitMeeting->status_approval_undangan = 'approved';
                $exitMeeting->approved_by_undangan = $userId;
                $exitMeeting->approved_at_undangan = now();
                $exitMeeting->save();
                return response()->json(['success' => true, 'message' => 'Undangan berhasil di-approve!']);
            } elseif ($type === 'absensi') {
                $exitMeeting = ExitMeetingUpload::findOrFail($id);
                $exitMeeting->status_approval_absensi = 'approved';
                $exitMeeting->approved_by_absensi = $userId;
                $exitMeeting->approved_at_absensi = now();
                $exitMeeting->save();
                return response()->json(['success' => true, 'message' => 'Absensi berhasil di-approve!']);
            } elseif ($type === 'exit_meeting') {
                $exitMeeting = ExitMeetingUpload::findOrFail($id);
                $exitMeeting->approve = true;
                $exitMeeting->approve_at = now();
                $exitMeeting->approved_by_undangan = $userId;
                $exitMeeting->save();
                return response()->json(['success' => true, 'message' => 'Exit Meeting berhasil di-approve!']);
            } elseif ($type === 'lha_lhk') {
                $exitMeeting = ExitMeetingUpload::with('lhaLhk')->findOrFail($id);
                if (!$exitMeeting->lhaLhk) {
                    return response()->json(['success' => false, 'message' => 'LHA/LHK tidak ditemukan untuk ID ini.'], 404);
                }
                $lhaLhk = $exitMeeting->lhaLhk;
                $lhaLhk->approve = true;
                $lhaLhk->approve_at = now();
                $lhaLhk->approved_by = $userId;
                $lhaLhk->save();
                return response()->json(['success' => true, 'message' => 'LHA/LHK berhasil di-approve!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Tipe approval tidak valid!'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal approve data: ' . $e->getMessage()], 500);
        }
    }
}
