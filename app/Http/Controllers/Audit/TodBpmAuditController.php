<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TodBpmAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\TodBpmEvaluasi;
use App\Models\WalkthroughAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TodBpmAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = TodBpmAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko.kontrolList', 'pkaKontrol'])->get();

        // Filter by specific ID from details page
        if ($request->filled('id')) {
            $data = $data->filter(function($item) use ($request) {
                return $item->id == $request->id;
            });
        }

        $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
        if ($userAuditeeId !== null) {
            $data = $data->filter(fn($item) =>
                $item->perencanaanAudit && $item->perencanaanAudit->auditee_id == $userAuditeeId
            );
        }

        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $data = $data->filter(function ($item) use ($selectedMonth) {
                if (!$item->perencanaanAudit) return false;
                $start = Carbon::parse($item->perencanaanAudit->tanggal_audit_mulai);
                return $start->year == $selectedMonth->year && $start->month == $selectedMonth->month;
            });
        }

        return view('audit.tod-bpm.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suratTugas  = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $walkthroughs = WalkthroughAudit::whereNotNull('file_bpm')
            ->where('status_approval', 'approved')
            ->with('perencanaanAudit')
            ->get()
            ->groupBy('perencanaan_audit_id');

        return view('audit.tod-bpm.create', compact('suratTugas', 'walkthroughs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'judul_bpm'            => 'required|string',
            'nama_bpo'             => 'required|string',
            'walkthrough_id'       => 'required|exists:walkthrough_audit,id',
            'pka_risiko_ids'       => 'nullable|array',
            'pka_risiko_ids.*'     => 'exists:pka_risiko,id',
            'pka_kontrol_ids'      => 'nullable|array',
            'pka_kontrol_ids.*'    => 'exists:pka_kontrol,id',
            'file_kka_tod'         => 'nullable|file|mimes:pdf|max:5120',
            'hasil_evaluasi'       => 'required|string|in:Sesuai,Tidak Sesuai',
        ]);

        // Ambil file BPM dari walkthrough
        $walkthrough = WalkthroughAudit::findOrFail($request->walkthrough_id);
        if (!$walkthrough->file_bpm) {
            return redirect()->back()
                ->with('error', 'File BPM dari walkthrough tidak ditemukan!')
                ->withInput();
        }

        $fileKkaTodPath = null;
        if ($request->hasFile('file_kka_tod')) {
            $fileKkaTodPath = $request->file('file_kka_tod')->store('tod-bpm/kka-tod', 'public');
        }

        DB::transaction(function () use ($request, $walkthrough, $fileKkaTodPath) {
            $bpm = TodBpmAudit::create([
                'perencanaan_audit_id' => $request->perencanaan_audit_id,
                'judul_bpm'            => $request->judul_bpm,
                'nama_bpo'             => $request->nama_bpo,
                'file_bpm'             => $walkthrough->file_bpm,
                'file_kka_tod'         => $fileKkaTodPath,
                'resiko'               => null,
                'kontrol'              => null,
            ]);

            // Simpan pivot risiko
            if ($request->filled('pka_risiko_ids')) {
                foreach ($request->pka_risiko_ids as $risikoId) {
                    DB::table('tod_bpm_risiko')->insert([
                        'tod_bpm_audit_id' => $bpm->id,
                        'pka_risiko_id'    => $risikoId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            // Simpan pivot kontrol
            if ($request->filled('pka_kontrol_ids')) {
                foreach ($request->pka_kontrol_ids as $kontrolId) {
                    DB::table('tod_bpm_kontrol')->insert([
                        'tod_bpm_audit_id' => $bpm->id,
                        'pka_kontrol_id'   => $kontrolId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            TodBpmEvaluasi::create([
                'tod_bpm_audit_id' => $bpm->id,
                'hasil_evaluasi'   => $request->hasil_evaluasi,
            ]);
        });

        return redirect()->route('audit.tod-bpm.index')->with('success', 'TOD berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = TodBpmAudit::with([
            'perencanaanAudit',
            'evaluasi',
            'pkaRisiko.kontrolList',
            'pkaKontrol',
        ])->findOrFail($id);

        // Bangun struktur: risiko → kontrol yang dipilih
        $selectedKontrolIds = $item->pkaKontrol->pluck('id')->toArray();
        $risikoData = $item->pkaRisiko->map(function ($risiko) use ($selectedKontrolIds) {
            return [
                'risiko'          => $risiko,
                'kontrolDipilih'  => $risiko->kontrolList->filter(
                    fn($k) => in_array($k->id, $selectedKontrolIds)
                )->values(),
            ];
        });

        return view('audit.tod-bpm.show', compact('item', 'risikoData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = TodBpmAudit::with([
            'perencanaanAudit',
            'pkaRisiko',
            'pkaKontrol',
        ])->findOrFail($id);

        $suratTugas  = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $walkthroughs = WalkthroughAudit::whereNotNull('file_bpm')
            ->where('status_approval', 'approved')
            ->with('perencanaanAudit')
            ->get()
            ->groupBy('perencanaan_audit_id');

        // ID yang sudah dipilih sebelumnya (untuk pre-check checkbox)
        $selectedRisikoIds  = $item->pkaRisiko->pluck('id')->toArray();
        $selectedKontrolIds = $item->pkaKontrol->pluck('id')->toArray();

        return view('audit.tod-bpm.edit', compact(
            'item', 'suratTugas', 'walkthroughs',
            'selectedRisikoIds', 'selectedKontrolIds'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = TodBpmAudit::findOrFail($id);

        $request->validate([
            'perencanaan_audit_id' => 'required|exists:perencanaan_audit,id',
            'judul_bpm'            => 'required|string',
            'nama_bpo'             => 'required|string',
            'walkthrough_id'       => 'nullable|exists:walkthrough_audit,id',
            'pka_risiko_ids'       => 'nullable|array',
            'pka_risiko_ids.*'     => 'exists:pka_risiko,id',
            'pka_kontrol_ids'      => 'nullable|array',
            'pka_kontrol_ids.*'    => 'exists:pka_kontrol,id',
            'file_kka_tod'         => 'nullable|file|mimes:pdf|max:5120',
        ]);

        DB::transaction(function () use ($request, $item) {
            $data = [
                'perencanaan_audit_id' => $request->perencanaan_audit_id,
                'judul_bpm'            => $request->judul_bpm,
                'nama_bpo'             => $request->nama_bpo,
                'resiko'               => null,
                'kontrol'              => null,
            ];

            if ($request->walkthrough_id) {
                $walkthrough = WalkthroughAudit::findOrFail($request->walkthrough_id);
                if ($walkthrough->file_bpm) {
                    $data['file_bpm'] = $walkthrough->file_bpm;
                }
            }

            if ($request->hasFile('file_kka_tod')) {
                if ($item->file_kka_tod && Storage::disk('public')->exists($item->file_kka_tod)) {
                    Storage::disk('public')->delete($item->file_kka_tod);
                }
                $data['file_kka_tod'] = $request->file('file_kka_tod')->store('tod-bpm/kka-tod', 'public');
            }

            $item->update($data);

            // Sync pivot risiko
            DB::table('tod_bpm_risiko')->where('tod_bpm_audit_id', $item->id)->delete();
            if ($request->filled('pka_risiko_ids')) {
                foreach ($request->pka_risiko_ids as $risikoId) {
                    DB::table('tod_bpm_risiko')->insert([
                        'tod_bpm_audit_id' => $item->id,
                        'pka_risiko_id'    => $risikoId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            // Sync pivot kontrol
            DB::table('tod_bpm_kontrol')->where('tod_bpm_audit_id', $item->id)->delete();
            if ($request->filled('pka_kontrol_ids')) {
                foreach ($request->pka_kontrol_ids as $kontrolId) {
                    DB::table('tod_bpm_kontrol')->insert([
                        'tod_bpm_audit_id' => $item->id,
                        'pka_kontrol_id'   => $kontrolId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }
        });

        return redirect()->route('audit.tod-bpm.index')->with('success', 'Data TOD berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = TodBpmAudit::findOrFail($id);
        $item->delete(); // cascade hapus pivot otomatis via FK
        return redirect()->route('audit.tod-bpm.index')->with('success', 'Data TOD berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = TodBpmAudit::findOrFail($id);

        if ($request->action == 'reject') {
            $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Alasan penolakan harus diisi',
                'rejection_reason.min'      => 'Alasan penolakan minimal 10 karakter',
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
