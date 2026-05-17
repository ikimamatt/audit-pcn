<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ToeAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\ToeEvaluasi;
use App\Models\TodBpmAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ToeAuditController extends Controller
{
    public function index(Request $request)
    {
        $data = ToeAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko.kontrolList', 'pkaKontrol'])->get();

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

        return view('audit.toe.index', compact('data'));
    }

    public function create()
    {
        $suratTugas = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $bpmList    = TodBpmAudit::all();
        return view('audit.toe.create', compact('suratTugas', 'bpmList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id'   => 'required|exists:perencanaan_audit,id',
            'judul_bpm'              => 'required|string',
            'pemilihan_sampel_audit' => 'nullable|string',
            'pka_risiko_ids'         => 'nullable|array',
            'pka_risiko_ids.*'       => 'exists:pka_risiko,id',
            'pka_kontrol_ids'        => 'nullable|array',
            'pka_kontrol_ids.*'      => 'exists:pka_kontrol,id',
            'file_kka_toe'           => 'nullable|file|mimes:pdf|max:5120',
            'hasil_evaluasi'         => 'required|string|in:Efektif,Tidak Efektif,Efektif Sebagian',
        ]);

        $fileKkaToePath = null;
        if ($request->hasFile('file_kka_toe')) {
            $fileKkaToePath = $request->file('file_kka_toe')->store('toe/kka-toe', 'public');
        }

        DB::transaction(function () use ($request, $fileKkaToePath) {
            $toe = ToeAudit::create([
                'perencanaan_audit_id'   => $request->perencanaan_audit_id,
                'judul_bpm'              => $request->judul_bpm,
                'pengendalian_eksisting' => null,
                'pemilihan_sampel_audit' => $request->pemilihan_sampel_audit,
                'resiko'                 => null,
                'kontrol'                => null,
                'file_kka_toe'           => $fileKkaToePath,
            ]);

            // Simpan pivot risiko
            if ($request->filled('pka_risiko_ids')) {
                foreach ($request->pka_risiko_ids as $risikoId) {
                    DB::table('toe_risiko')->insert([
                        'toe_audit_id'  => $toe->id,
                        'pka_risiko_id' => $risikoId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // Simpan pivot kontrol
            if ($request->filled('pka_kontrol_ids')) {
                foreach ($request->pka_kontrol_ids as $kontrolId) {
                    DB::table('toe_kontrol')->insert([
                        'toe_audit_id'   => $toe->id,
                        'pka_kontrol_id' => $kontrolId,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }

            ToeEvaluasi::create([
                'toe_audit_id'   => $toe->id,
                'hasil_evaluasi' => $request->hasil_evaluasi,
            ]);
        });

        return redirect()->route('audit.toe.index')->with('success', 'TOE berhasil disimpan!');
    }

    public function show($id)
    {
        $item = ToeAudit::with([
            'perencanaanAudit',
            'evaluasi',
            'pkaRisiko.kontrolList',
            'pkaKontrol',
        ])->findOrFail($id);

        // Bangun struktur: risiko → kontrol yang dipilih
        $selectedKontrolIds = $item->pkaKontrol->pluck('id')->toArray();
        $risikoData = $item->pkaRisiko->map(function ($risiko) use ($selectedKontrolIds) {
            return [
                'risiko'         => $risiko,
                'kontrolDipilih' => $risiko->kontrolList->filter(
                    fn($k) => in_array($k->id, $selectedKontrolIds)
                )->values(),
            ];
        });

        return view('audit.toe.show', compact('item', 'risikoData'));
    }

    public function edit($id)
    {
        $item = ToeAudit::with([
            'perencanaanAudit',
            'pkaRisiko',
            'pkaKontrol',
        ])->findOrFail($id);

        $suratTugas = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $bpmList    = TodBpmAudit::all();

        $selectedRisikoIds  = $item->pkaRisiko->pluck('id')->toArray();
        $selectedKontrolIds = $item->pkaKontrol->pluck('id')->toArray();

        return view('audit.toe.edit', compact(
            'item', 'suratTugas', 'bpmList',
            'selectedRisikoIds', 'selectedKontrolIds'
        ));
    }

    public function update(Request $request, $id)
    {
        $item = ToeAudit::findOrFail($id);

        $request->validate([
            'perencanaan_audit_id'   => 'required|exists:perencanaan_audit,id',
            'judul_bpm'              => 'required|string',
            'pemilihan_sampel_audit' => 'nullable|string',
            'pka_risiko_ids'         => 'nullable|array',
            'pka_risiko_ids.*'       => 'exists:pka_risiko,id',
            'pka_kontrol_ids'        => 'nullable|array',
            'pka_kontrol_ids.*'      => 'exists:pka_kontrol,id',
            'file_kka_toe'           => 'nullable|file|mimes:pdf|max:5120',
        ]);

        DB::transaction(function () use ($request, $item) {
            $data = [
                'perencanaan_audit_id'   => $request->perencanaan_audit_id,
                'judul_bpm'              => $request->judul_bpm,
                'pemilihan_sampel_audit' => $request->pemilihan_sampel_audit,
                'pengendalian_eksisting' => null,
                'resiko'                 => null,
                'kontrol'                => null,
            ];

            if ($request->hasFile('file_kka_toe')) {
                if ($item->file_kka_toe && Storage::disk('public')->exists($item->file_kka_toe)) {
                    Storage::disk('public')->delete($item->file_kka_toe);
                }
                $data['file_kka_toe'] = $request->file('file_kka_toe')->store('toe/kka-toe', 'public');
            }

            $item->update($data);

            // Sync pivot risiko
            DB::table('toe_risiko')->where('toe_audit_id', $item->id)->delete();
            if ($request->filled('pka_risiko_ids')) {
                foreach ($request->pka_risiko_ids as $risikoId) {
                    DB::table('toe_risiko')->insert([
                        'toe_audit_id'  => $item->id,
                        'pka_risiko_id' => $risikoId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // Sync pivot kontrol
            DB::table('toe_kontrol')->where('toe_audit_id', $item->id)->delete();
            if ($request->filled('pka_kontrol_ids')) {
                foreach ($request->pka_kontrol_ids as $kontrolId) {
                    DB::table('toe_kontrol')->insert([
                        'toe_audit_id'   => $item->id,
                        'pka_kontrol_id' => $kontrolId,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }
        });

        return redirect()->route('audit.toe.index')->with('success', 'Data TOE berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = ToeAudit::findOrFail($id);
        $item->delete();
        return redirect()->route('audit.toe.index')->with('success', 'Data TOE berhasil dihapus!');
    }

    public function approval($id, Request $request)
    {
        $item = ToeAudit::findOrFail($id);

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