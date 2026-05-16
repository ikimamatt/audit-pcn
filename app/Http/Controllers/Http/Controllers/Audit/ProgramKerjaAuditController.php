<?php

namespace App\Http\Controllers\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PkaRiskBasedAudit;
use App\Models\Models\Audit\PkaProsesBisnis;
use App\Models\Models\Audit\PkaRisiko;
use App\Models\Models\Audit\PkaKontrol;
use App\Models\Models\Audit\PkaMilestone;
use App\Models\Models\Audit\PkaDokumen;
use App\Models\Audit\PerencanaanAudit;
use App\Models\EntryMeeting;
use App\Models\WalkthroughAudit;
use Illuminate\Http\Request;

class ProgramKerjaAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProgramKerjaAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.unit', 'risks', 'milestones', 'dokumen'])->get();
        return view('perencanaan-audit.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua surat tugas yang belum memiliki PKA
        $suratTugas = PerencanaanAudit::whereDoesntHave('programKerjaAudit')->with('auditee')->orderBy('nomor_surat_tugas')->get();

        return view('perencanaan-audit.create', compact('suratTugas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perencanaan_audit_id'        => 'required|exists:perencanaan_audit,id',
            'tanggal_pka'                 => 'required|date',
            'no_pka'                      => 'required',
            'judul_pka'                   => 'required|string',
            'proses_bisnis'               => 'required|array|min:1',
            'proses_bisnis.*.nama'        => 'required|string',
            'proses_bisnis.*.risiko'      => 'nullable|array',
        ]);

        // Kumpulkan nama proses bisnis untuk backward-compat kolom JSON lama
        $prosesBisnisNama = collect($request->proses_bisnis)
            ->pluck('nama')
            ->filter()
            ->values()
            ->toArray();

        // Simpan data utama PKA
        $pka = ProgramKerjaAudit::create([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'tanggal_pka'          => $request->tanggal_pka,
            'no_pka'               => $request->no_pka,
            'judul_pka'            => $request->judul_pka,
            'proses_bisnis'        => $prosesBisnisNama,
            'informasi_umum'       => $request->informasi_umum,
            'kpi_tidak_tercapai'   => $request->kpi_tidak_tercapai,
            'data_awal_dokumen'    => is_array($request->data_awal_dokumen)
                                        ? array_values($request->data_awal_dokumen)
                                        : [],
        ]);

        // Simpan hierarki Proses Bisnis → Risiko → Kontrol
        $this->storeHierarki($pka->id, $request->proses_bisnis ?? []);

        // Simpan milestone
        if ($request->has('milestone')) {
            foreach ($request->milestone as $nama => $ms) {
                PkaMilestone::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_milestone'         => $nama,
                    'tanggal_mulai'          => $ms['mulai'] ?? null,
                    'tanggal_selesai'        => $ms['selesai'] ?? null,
                ]);
            }
        }

        // Simpan dokumen upload
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $path = $file->store('dokumen_pka', 'public');
                PkaDokumen::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_dokumen'           => $file->getClientOriginalName(),
                    'file_path'              => $path,
                ]);
            }
        }

        return redirect()->route('audit.pka.index')->with('success', 'Program Kerja Audit berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = ProgramKerjaAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.koordinator',
            'perencanaanAudit.ketuaTim',
            'prosesBisnis.risikoList.kontrolList',
            'risks',
            'milestones',
            'dokumen',
        ])->findOrFail($id);

        return view('perencanaan-audit.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = ProgramKerjaAudit::with([
            'perencanaanAudit',
            'prosesBisnis.risikoList.kontrolList',
            'milestones',
            'dokumen',
        ])->findOrFail($id);

        $suratTugas = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        return view('perencanaan-audit.edit', compact('item', 'suratTugas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'perencanaan_audit_id'        => 'required|exists:perencanaan_audit,id',
            'tanggal_pka'                 => 'required|date',
            'no_pka'                      => 'required',
            'judul_pka'                   => 'required|string',
            'proses_bisnis'               => 'required|array|min:1',
            'proses_bisnis.*.nama'        => 'required|string',
        ]);

        $pka = ProgramKerjaAudit::findOrFail($id);

        // Kumpulkan nama untuk backward-compat JSON lama
        $prosesBisnisNama = collect($request->proses_bisnis)
            ->pluck('nama')
            ->filter()
            ->values()
            ->toArray();

        $pka->update([
            'perencanaan_audit_id' => $request->perencanaan_audit_id,
            'tanggal_pka'          => $request->tanggal_pka,
            'no_pka'               => $request->no_pka,
            'judul_pka'            => $request->judul_pka,
            'proses_bisnis'        => $prosesBisnisNama,
            'informasi_umum'       => $request->informasi_umum,
            'kpi_tidak_tercapai'   => $request->kpi_tidak_tercapai,
            'data_awal_dokumen'    => is_array($request->data_awal_dokumen)
                                        ? array_values($request->data_awal_dokumen)
                                        : [],
        ]);

        // Hapus semua hierarki lama lalu insert ulang (CASCADE ke risiko & kontrol otomatis)
        $pka->prosesBisnis()->delete();
        $this->storeHierarki($pka->id, $request->proses_bisnis ?? []);

        // Update milestone: hapus semua, insert ulang
        $pka->milestones()->delete();
        if ($request->has('milestone')) {
            foreach ($request->milestone as $nama => $ms) {
                PkaMilestone::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_milestone'         => $nama,
                    'tanggal_mulai'          => $ms['mulai'] ?? null,
                    'tanggal_selesai'        => $ms['selesai'] ?? null,
                ]);
            }
        }

        // Upload dokumen baru (dokumen lama tidak dihapus)
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $path = $file->store('dokumen_pka', 'public');
                PkaDokumen::create([
                    'program_kerja_audit_id' => $pka->id,
                    'nama_dokumen'           => $file->getClientOriginalName(),
                    'file_path'              => $path,
                ]);
            }
        }

        return redirect()->route('audit.pka.index')->with('success', 'Program Kerja Audit berhasil diupdate!');
    }

    /**
     * Simpan hierarki Proses Bisnis → Risiko → Kontrol.
     * Dipakai oleh store() dan update().
     */
    private function storeHierarki(int $pkaId, array $prosesBisnisList): void
    {
        foreach ($prosesBisnisList as $pbUrutan => $pbData) {
            $namaPb = trim($pbData['nama'] ?? '');
            if ($namaPb === '') {
                continue;
            }

            $pb = PkaProsesBisnis::create([
                'program_kerja_audit_id' => $pkaId,
                'nama_proses_bisnis'     => $namaPb,
                'urutan'                 => $pbUrutan + 1,
            ]);

            foreach ($pbData['risiko'] ?? [] as $risikoUrutan => $risikoData) {
                $deskripsi = trim($risikoData['deskripsi_risiko'] ?? '');
                if ($deskripsi === '') {
                    continue;
                }

                $risiko = PkaRisiko::create([
                    'pka_proses_bisnis_id' => $pb->id,
                    'deskripsi_risiko'     => $deskripsi,
                    'penyebab_risiko'      => $risikoData['penyebab_risiko'] ?? null,
                    'dampak_risiko'        => $risikoData['dampak_risiko'] ?? null,
                    'urutan'               => $risikoUrutan + 1,
                ]);

                foreach ($risikoData['kontrol'] ?? [] as $kontrolUrutan => $kontrolData) {
                    $deskKontrol = trim($kontrolData['deskripsi_kontrol'] ?? '');
                    if ($deskKontrol === '') {
                        continue;
                    }

                    PkaKontrol::create([
                        'pka_risiko_id'     => $risiko->id,
                        'deskripsi_kontrol' => $deskKontrol,
                        'urutan'            => $kontrolUrutan + 1,
                    ]);
                }
            }
        }
    }

    // Approval dokumen
    public function approval($pkaId, $dokId, Request $request)
    {
        $dok = PkaDokumen::findOrFail($dokId);
        if ($request->action == 'approve') {
            $dok->status_approval = 'approved';
            $dok->approved_by = auth()->id();
            $dok->approved_at = now();
        } elseif ($request->action == 'reject') {
            $dok->status_approval = 'rejected';
            $dok->approved_by = auth()->id();
            $dok->approved_at = now();
        }
        $dok->save();
        return redirect()->back()->with('success', 'Status dokumen berhasil diubah!');
    }

    /**
     * Cek relasi data sebelum hapus (untuk AJAX warning).
     */
    public function checkRelations($id)
    {
        $item = ProgramKerjaAudit::with([
            'entryMeeting',
            'walkthroughAudit',
            'prosesBisnis.risikoList.kontrolList',
            'milestones',
            'dokumen',
        ])->findOrFail($id);

        $relations = [];

        if ($item->entryMeeting) {
            $relations[] = '1 data Entry Meeting';
        }
        if ($item->walkthroughAudit) {
            $relations[] = '1 data Walkthrough Audit';
        }

        $pbCount = $item->prosesBisnis->count();
        if ($pbCount > 0) {
            $risikoCount  = $item->prosesBisnis->sum(fn($pb) => $pb->risikoList->count());
            $kontrolCount = $item->prosesBisnis->sum(fn($pb) => $pb->risikoList->sum(fn($r) => $r->kontrolList->count()));
            $relations[] = "{$pbCount} Proses Bisnis ({$risikoCount} Risiko, {$kontrolCount} Kontrol)";
        }

        $milestoneCount = $item->milestones->count();
        if ($milestoneCount > 0) {
            $relations[] = "{$milestoneCount} Milestone";
        }
        $dokumenCount = $item->dokumen->count();
        if ($dokumenCount > 0) {
            $relations[] = "{$dokumenCount} Dokumen";
        }

        return response()->json([
            'has_relations' => count($relations) > 0,
            'relations'     => $relations,
            'no_pka'        => $item->no_pka,
            'surat_tugas'   => $item->perencanaanAudit->nomor_surat_tugas ?? '-',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * Manual cascade delete untuk menghindari FK RESTRICT violation.
     */
    public function destroy($id)
    {
        $item = ProgramKerjaAudit::with(['entryMeeting', 'walkthroughAudit'])->findOrFail($id);

        // Hapus entry_meeting terkait (FK RESTRICT)
        if ($item->entryMeeting) {
            $item->entryMeeting->delete();
        }

        // Hapus walkthrough_audit terkait
        if ($item->walkthroughAudit) {
            $item->walkthroughAudit->delete();
        }

        // prosesBisnis CASCADE ke risiko & kontrol otomatis
        // milestones & dokumen juga CASCADE
        // risks (lama) dihapus manual
        $item->risks()->delete();
        $item->milestones()->delete();
        $item->dokumen()->delete();
        $item->prosesBisnis()->delete(); // CASCADE → pka_risiko → pka_kontrol

        // Hapus parent
        $item->delete();

        return redirect()->route('audit.pka.index')->with('success', 'Data PKA dan seluruh proses audit terkait berhasil dihapus!');
    }

    /**
     * Download dokumen PKA berdasarkan template.
     */
    public function download($id)
    {
        $item = ProgramKerjaAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.koordinator', 'perencanaanAudit.ketuaTim', 'risks', 'milestones', 'dokumen'])->findOrFail($id);

        $templatePath = base_path('Template Program Kerja Audit.docx');

        if (file_exists($templatePath)) {
            // Format data
            $tanggalPka = $item->tanggal_pka ? \Carbon\Carbon::parse($item->tanggal_pka)->locale('id')->translatedFormat('d F Y') : '-';

            $tglMulai = $item->perencanaanAudit->tanggal_audit_mulai;
            $tglSampai = $item->perencanaanAudit->tanggal_audit_sampai;
            $waktuAudit = '-';
            if ($tglMulai && $tglSampai) {
                $mulai = \Carbon\Carbon::parse($tglMulai)->locale('id')->translatedFormat('d F Y');
                $sampai = \Carbon\Carbon::parse($tglSampai)->locale('id')->translatedFormat('d F Y');
                $waktuAudit = $mulai . ' s/d ' . $sampai;
            }

            $periode = $item->perencanaanAudit->periode_audit ?? '-';
            $nomorPka = $item->no_pka ?? '-';

            $nomorTugas = $item->perencanaanAudit->nomor_surat_tugas ?? '-';
            $tanggalTugas = $item->perencanaanAudit->tanggal_surat_tugas ? \Carbon\Carbon::parse($item->perencanaanAudit->tanggal_surat_tugas)->locale('id')->translatedFormat('d F Y') : '-';
            $judulPka = $item->judul_pka ?? '-';

            // Data Awal Yang Perlu Disiapkan
            $dataAwalDokumenRaw = is_array($item->data_awal_dokumen) ? $item->data_awal_dokumen : json_decode($item->data_awal_dokumen ?? '[]', true);
            $dataAwalDokumen = [];
            if (!empty($dataAwalDokumenRaw) && is_array($dataAwalDokumenRaw)) {
                foreach ($dataAwalDokumenRaw as $idx => $da) {
                    $dataAwalDokumen[] = [
                        'no_da' => $idx + 1,
                        'nama_dokumen' => $da['nama_dokumen'] ?? '-',
                        'ruang_lingkup_da' => $da['ruang_lingkup'] ?? '-',
                        'periode_da' => $da['periode'] ?? '-'
                    ];
                }
            } else {
                $dataAwalDokumen = [
                    ['no_da' => 1, 'nama_dokumen' => '-', 'ruang_lingkup_da' => '-', 'periode_da' => '-']
                ];
            }

            // Ruang Lingkup Audit (JSON array dari perencanaan_audit)
            $ruangLingkup = $item->perencanaanAudit->ruang_lingkup ?? [];
            if (is_string($ruangLingkup)) {
                $ruangLingkup = json_decode($ruangLingkup, true) ?? [];
            }

            // Proses template menggunakan PhpWord
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

            // Replace placeholders
            $templateProcessor->setValue('PERIODE_AUDIT', $periode);
            $templateProcessor->setValue('WAKTU_AUDIT', $waktuAudit);
            $templateProcessor->setValue('NOMOR_PKA', $nomorPka);
            $templateProcessor->setValue('TANGGAL_PKA', $tanggalPka);
            $templateProcessor->setValue('NOMOR_TUGAS', $nomorTugas);
            $templateProcessor->setValue('TANGGAL_TUGAS', $tanggalTugas);
            $templateProcessor->setValue('TANGGAL_SURAT_TUGAS', $tanggalTugas);
            $templateProcessor->setValue('JUDUL_PKA', $judulPka);
            $templateProcessor->setValue('RUANG_LINGKUP_AUDIT', '##RUANG_LINGKUP##');
            
            $templateProcessor->setValue('KOORDINATOR_NAMA', $item->perencanaanAudit->koordinator->nama ?? '-');
            $templateProcessor->setValue('KOORDINATOR_NIP', $item->perencanaanAudit->koordinator->nip ?? '-');
            $templateProcessor->setValue('KETUA_TIM_NAMA', $item->perencanaanAudit->ketuaTim->nama ?? '-');
            $templateProcessor->setValue('KETUA_TIM_NIP', $item->perencanaanAudit->ketuaTim->nip ?? '-');

            // Clone baris data awal
            try {
                $templateProcessor->cloneRowAndSetValues('no_da', $dataAwalDokumen);
            } catch (\Exception $e) {
                // Abaikan jika template belum punya variabel no_da
            }
            
            // Siapkan Data Tim Pemeriksa
            $timPemeriksa = [];
            $noTim = 1;

            // 1. Koordinator
            if ($item->perencanaanAudit->koordinator) {
                $timPemeriksa[] = [
                    'no_tim' => $noTim++,
                    'nama_tim' => $item->perencanaanAudit->koordinator->nama,
                    'role_tim' => 'Koordinator',
                    'nip_tim' => $item->perencanaanAudit->koordinator->nip,
                ];
            }

            // 2. Ketua Tim
            if ($item->perencanaanAudit->ketuaTim) {
                $timPemeriksa[] = [
                    'no_tim' => $noTim++,
                    'nama_tim' => $item->perencanaanAudit->ketuaTim->nama,
                    'role_tim' => 'Ketua Tim',
                    'nip_tim' => $item->perencanaanAudit->ketuaTim->nip,
                ];
            }

            // 3. Anggota
            $auditors = is_array($item->perencanaanAudit->auditor) ? $item->perencanaanAudit->auditor : json_decode($item->perencanaanAudit->auditor ?? '[]', true);
            if (is_array($auditors)) {
                foreach ($auditors as $auditorString) {
                    if (preg_match('/^(.*?)\s*-\s*NIP:\s*(.*)$/', $auditorString, $matches)) {
                        $timPemeriksa[] = [
                            'no_tim' => $noTim++,
                            'nama_tim' => trim($matches[1]),
                            'role_tim' => 'Anggota',
                            'nip_tim' => trim($matches[2]),
                        ];
                    } else {
                        $timPemeriksa[] = [
                            'no_tim' => $noTim++,
                            'nama_tim' => $auditorString,
                            'role_tim' => 'Anggota',
                            'nip_tim' => '-',
                        ];
                    }
                }
            }

            if (empty($timPemeriksa)) {
                $timPemeriksa[] = [
                    'no_tim' => 1,
                    'nama_tim' => '-',
                    'role_tim' => '-',
                    'nip_tim' => '-',
                ];
            }

            try {
                $templateProcessor->cloneRowAndSetValues('no_tim', $timPemeriksa);
            } catch (\Exception $e) {
                // Abaikan jika tidak ada variabel no_tim
            }
            
            // Format format tanggal milestone untuk Word
            $formatDateRange = function($ms) {
                if (!$ms || (!$ms->tanggal_mulai && !$ms->tanggal_selesai)) return '-';
                if ($ms->tanggal_mulai && !$ms->tanggal_selesai) return \Carbon\Carbon::parse($ms->tanggal_mulai)->locale('id')->translatedFormat('d F Y');
                if (!$ms->tanggal_mulai && $ms->tanggal_selesai) return \Carbon\Carbon::parse($ms->tanggal_selesai)->locale('id')->translatedFormat('d F Y');
                
                $mulai = \Carbon\Carbon::parse($ms->tanggal_mulai)->locale('id');
                $selesai = \Carbon\Carbon::parse($ms->tanggal_selesai)->locale('id');
                
                if ($mulai->format('Y-m-d') === $selesai->format('Y-m-d')) {
                    return $mulai->translatedFormat('d F Y');
                }
                
                if ($mulai->format('Y') === $selesai->format('Y')) {
                    return $mulai->translatedFormat('d F') . ' s.d ' . $selesai->translatedFormat('d F Y');
                }
                
                return $mulai->translatedFormat('d F Y') . ' s.d ' . $selesai->translatedFormat('d F Y');
            };

            // Set Template Variables for Milestones
            $templateProcessor->setValue('MS_PERMINTAAN_DOKUMEN', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Surat Permintaan Dokumen kepada Auditee')));
            $templateProcessor->setValue('MS_EKSPOSE_PKA', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Ekspose PKA Internal')));
            $templateProcessor->setValue('MS_ENTRY_MEETING', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Entry Meeting')));
            $templateProcessor->setValue('MS_WALKTHROUGH', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Walkthrough')));
            $templateProcessor->setValue('MS_TOD', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'TOD')));
            $templateProcessor->setValue('MS_TOE', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'TOE')));
            $templateProcessor->setValue('MS_DRAF_LHA', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Draf LHA')));
            $templateProcessor->setValue('MS_PRA_EXIT', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Pra Exit Meeting untuk Finalisasi LHA')));
            $templateProcessor->setValue('MS_EXIT_MEETING', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Exit Meeting')));

            // Set DAFTAR_AUDITOR langsung (berada di baris tabel terpisah)
            $anggotaList = collect($timPemeriksa)->where('role_tim', 'Anggota')->values();
            if ($anggotaList->count() > 0) {
                $auditorLines = $anggotaList->map(function($a, $i) {
                    return ($i + 1) . '. ' . $a['nama_tim'];
                })->toArray();
                // Gunakan </w:t><w:br/><w:t> untuk line break di Word
                $auditorText = implode('</w:t><w:br/><w:t>', $auditorLines);
            } else {
                $auditorText = '-';
            }
            $templateProcessor->setValue('DAFTAR_AUDITOR', $auditorText);

            // Menyiapkan data untuk tabel dinamis (Proses Bisnis & Risk)
            $prosesBisnis = is_array($item->proses_bisnis) ? $item->proses_bisnis : json_decode($item->proses_bisnis ?? '[]', true) ?? [];
            $risks = $item->risks;

            $pbCount = max(count($prosesBisnis), 1);
            $mergeStartMarker = '##VMERGE_START##';
            $mergeContinueMarker = '##VMERGE_CONT##';

            $tableData = [];
            for ($i = 0; $i < $pbCount; $i++) {
                $tableData[] = [
                    'NO' => $i + 1,
                    'PROSES_BISNIS' => isset($prosesBisnis[$i]) ? $prosesBisnis[$i] : '-',
                    'DESKRIPSI_RISIKO' => ($i == 0) ? $mergeStartMarker : $mergeContinueMarker,
                ];
            }

            try {
                $templateProcessor->cloneRowAndSetValues('NO', $tableData);
            } catch (\Exception $e) {
                \Log::error('CloneRowAndSetValues Error: ' . $e->getMessage());
            }

            // Variabel terpisah untuk tabel Audit Program (baris lanjutan halaman kedua)
            // Gunakan marker, lalu post-process XML agar tiap proses bisnis jadi paragraf terpisah
            $apNoMarker = '##AP_NO_DATA##';
            $apPbMarker = '##AP_PB_DATA##';
            $templateProcessor->setValue('AP_NO', $apNoMarker);
            $templateProcessor->setValue('AP_PROSES_BISNIS', $apPbMarker);

            // Post-process XML: merge Risk cells + bangun paragraf risk dgn font yg benar
            $refClass = new \ReflectionClass(get_class($templateProcessor));
            $mainPart = $refClass->getProperty('tempDocumentMainPart');
            $mainPart->setAccessible(true);
            $xml = $mainPart->getValue($templateProcessor);

            $risksForCallback = $risks;
            
            $xml = preg_replace_callback(
                '/<w:tc[^>]*>.*?<\/w:tc>/s',
                function ($match) use ($mergeStartMarker, $mergeContinueMarker, $risksForCallback, $apNoMarker, $apPbMarker, $prosesBisnis) {
                    $cellXml = $match[0];

                    if (strpos($cellXml, $mergeStartMarker) !== false) {
                        // Ambil run properties (font) dari template, paksa size 11pt
                        $rPr = '';
                        if (preg_match('/<w:rPr>.*?<\/w:rPr>/s', $cellXml, $m)) {
                            $rPr = $m[0];
                            // Hapus size lama jika ada, ganti dengan size 11 (22 half-points)
                            $rPr = preg_replace('/<w:sz[^\/]*\/?>/', '', $rPr);
                            $rPr = preg_replace('/<w:szCs[^\/]*\/?>/', '', $rPr);
                            $rPr = str_replace('</w:rPr>', '<w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>', $rPr);
                        } else {
                            $rPr = '<w:rPr><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>';
                        }

                        // Tambahkan vMerge restart ke tcPr
                        if (strpos($cellXml, '<w:tcPr') !== false) {
                            $cellXml = preg_replace('/<w:tcPr([^>]*)>/', '<w:tcPr$1><w:vMerge w:val="restart"/>', $cellXml, 1);
                        }

                        // Bangun paragraf risk dgn Word native auto-numbering (numId=99)
                        $riskParagraphs = '';
                        if ($risksForCallback->count() > 0) {
                            foreach ($risksForCallback as $idx => $risk) {
                                $text = htmlspecialchars($risk->deskripsi_resiko);
                                $riskParagraphs .= '<w:p>'
                                    . '<w:pPr>'
                                    . '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="99"/></w:numPr>'
                                    . '<w:jc w:val="both"/>'
                                    . '</w:pPr>'
                                    . '<w:r>' . $rPr . '<w:t>' . $text . '</w:t></w:r>'
                                    . '</w:p>';
                            }
                        } else {
                            $riskParagraphs = '<w:p><w:r>' . $rPr . '<w:t>-</w:t></w:r></w:p>';
                        }

                        // Ganti isi sel: ambil dari awal sampai </w:tcPr>, lalu paragraf baru
                        $tcPrEnd = strpos($cellXml, '</w:tcPr>');
                        if ($tcPrEnd !== false) {
                            $cellXml = substr($cellXml, 0, $tcPrEnd + strlen('</w:tcPr>'))
                                . $riskParagraphs . '</w:tc>';
                        } else {
                            $cellXml = preg_replace('/(<w:tc[^>]*>).*?(<\/w:tc>)/s', '$1' . $riskParagraphs . '$2', $cellXml);
                        }

                    } elseif (strpos($cellXml, $mergeContinueMarker) !== false) {
                        // Sel lanjutan: tambah vMerge continue dan kosongkan
                        if (strpos($cellXml, '<w:tcPr') !== false) {
                            $cellXml = preg_replace('/<w:tcPr([^>]*)>/', '<w:tcPr$1><w:vMerge/>', $cellXml, 1);
                        }
                        $cellXml = preg_replace('/<w:p[^\/].*?<\/w:p>/s', '<w:p/>', $cellXml);

                    } elseif (strpos($cellXml, $apNoMarker) !== false) {
                        // Kosongkan kolom NO karena penomoran sudah include di proses bisnis
                        $tcPrEnd = strpos($cellXml, '</w:tcPr>');
                        if ($tcPrEnd !== false) {
                            $cellXml = substr($cellXml, 0, $tcPrEnd + strlen('</w:tcPr>'))
                                . '<w:p/>' . '</w:tc>';
                        }

                    } elseif (strpos($cellXml, $apPbMarker) !== false) {
                        // Bangun paragraf terpisah untuk tiap proses bisnis dengan nomor dan spacing
                        $rPr = '';
                        if (preg_match('/<w:rPr>.*?<\/w:rPr>/s', $cellXml, $m)) {
                            $rPr = $m[0];
                        }
                        $pbParagraphs = '';
                        if (count($prosesBisnis) > 0) {
                            foreach ($prosesBisnis as $i => $pb) {
                                $text = ($i + 1) . '. ' . htmlspecialchars($pb);
                                $pbParagraphs .= '<w:p>'
                                    . '<w:pPr><w:spacing w:after="200"/></w:pPr>'
                                    . '<w:r>' . $rPr . '<w:t xml:space="preserve">' . $text . '</w:t></w:r>'
                                    . '</w:p>';
                            }
                        } else {
                            $pbParagraphs = '<w:p><w:r>' . $rPr . '<w:t>-</w:t></w:r></w:p>';
                        }
                        $tcPrEnd = strpos($cellXml, '</w:tcPr>');
                        if ($tcPrEnd !== false) {
                            $cellXml = substr($cellXml, 0, $tcPrEnd + strlen('</w:tcPr>'))
                                . $pbParagraphs . '</w:tc>';
                        }
                    }

                    return $cellXml;
                },
                $xml
            );

            // Post-process: Ruang Lingkup Audit auto-numbering (numId=98)
            $rlMarker = '##RUANG_LINGKUP##';
            if (strpos($xml, $rlMarker) !== false) {
                // Cari seluruh blok paragraf yang mengandung marker
                if (preg_match('/<w:p\b(?:(?!<w:p\b).)*?' . preg_quote($rlMarker, '/') . '.*?<\/w:p>/s', $xml, $pMatch)) {
                    $origPara = $pMatch[0];
                    
                    // Ekstrak rPr (font style) dari template, paksa Tahoma size 11
                    $rlRPr = '';
                    if (preg_match('/<w:rPr>.*?<\/w:rPr>/s', $origPara, $rlMatch)) {
                        $rlRPr = $rlMatch[0];
                        $rlRPr = preg_replace('/<w:sz[^\/]*\/?>/', '', $rlRPr);
                        $rlRPr = preg_replace('/<w:szCs[^\/]*\/?>/', '', $rlRPr);
                        $rlRPr = preg_replace('/<w:rFonts[^\/]*\/?>/', '', $rlRPr);
                        $rlRPr = str_replace('</w:rPr>', '<w:rFonts w:ascii="Tahoma" w:hAnsi="Tahoma" w:cs="Tahoma" w:eastAsia="Tahoma"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>', $rlRPr);
                    } else {
                        $rlRPr = '<w:rPr><w:rFonts w:ascii="Tahoma" w:hAnsi="Tahoma" w:cs="Tahoma" w:eastAsia="Tahoma"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>';
                    }
                    
                    // Ekstrak pPr (jarak spasi, dll) dari paragraf asli
                    $origPPr = '';
                    if (preg_match('/<w:pPr>.*?<\/w:pPr>/s', $origPara, $pPrMatch)) {
                        $origPPr = $pPrMatch[0];
                    }
                    
                    // Buat versi pPr dengan numbering untuk daftar ruang lingkup
                    $numberedPPr = $origPPr;
                    if ($numberedPPr !== '') {
                        $numberedPPr = preg_replace('/<w:jc[^\/]*\/?>/', '', $numberedPPr);
                        $numberedPPr = preg_replace('/<w:numPr>.*?<\/w:numPr>/s', '', $numberedPPr);
                        $numberedPPr = preg_replace('/<w:ind[^\/]*\/?>/', '', $numberedPPr);
                        $numberedPPr = str_replace('</w:pPr>', '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="98"/></w:numPr><w:jc w:val="both"/><w:ind w:left="1000" w:hanging="360"/></w:pPr>', $numberedPPr);
                    } else {
                        $numberedPPr = '<w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="98"/></w:numPr><w:jc w:val="both"/><w:ind w:left="1000" w:hanging="360"/></w:pPr>';
                    }
                    
                    // Ganti teks markernya SAJA dengan menyisipkan penutup paragraf lama, 
                    // menambahkan daftar paragraf baru, dan pembuka paragraf sisanya
                    $injectedXml = '</w:t></w:r></w:p>'; // Tutup paragraf asli
                    
                    if (!empty($ruangLingkup)) {
                        foreach ($ruangLingkup as $rl) {
                            $injectedXml .= '<w:p>'
                                . $numberedPPr
                                . '<w:r>' . $rlRPr . '<w:t>' . htmlspecialchars($rl) . '</w:t></w:r>'
                                . '</w:p>';
                        }
                    } else {
                        $injectedXml .= '<w:p>' . $numberedPPr . '<w:r>' . $rlRPr . '<w:t>-</w:t></w:r></w:p>';
                    }
                    
                    // Buka paragraf lagi dengan pPr ASLI untuk mempertahankan elemen seperti Page Break dll
                    $injectedXml .= '<w:p>' . $origPPr . '<w:r><w:t>';
                    
                    // Terapkan ke $origPara, lalu terapkan ke seluruh $xml
                    $newPara = str_replace($rlMarker, $injectedXml, $origPara);
                    $xml = str_replace($origPara, $newPara, $xml);
                }
            }

            $mainPart->setValue($templateProcessor, $xml);

            $filename = 'PKA_' . str_replace(['/', '\\'], '_', $item->no_pka) . '.docx';
            $tempPath = storage_path('app/public/temp_' . uniqid() . '.docx');

            $templateProcessor->saveAs($tempPath);

            // Inject Word native numbering definition (1. 2. 3.) ke dalam file docx
            $zip = new \ZipArchive();
            if ($zip->open($tempPath) === true) {
                $numberingXml = $zip->getFromName('word/numbering.xml');

                $ns = 'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"';
                $abstractNum = '<w:abstractNum w:abstractNumId="99" ' . $ns . '>'
                    . '<w:lvl w:ilvl="0">'
                    . '<w:start w:val="1"/>'
                    . '<w:numFmt w:val="decimal"/>'
                    . '<w:lvlText w:val="%1."/>'
                    . '<w:lvlJc w:val="left"/>'
                    . '<w:pPr><w:ind w:left="360" w:hanging="360"/></w:pPr>'
                    . '<w:rPr><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
                    . '</w:lvl>'
                    . '</w:abstractNum>';
                $numRef = '<w:num w:numId="99" ' . $ns . '><w:abstractNumId w:val="99"/></w:num>';

                if ($numberingXml !== false) {
                    // Tambahkan ke numbering.xml yang sudah ada (tanpa namespace duplikat)
                    $abstractNumClean = '<w:abstractNum w:abstractNumId="99">'
                        . '<w:lvl w:ilvl="0">'
                        . '<w:start w:val="1"/>'
                        . '<w:numFmt w:val="decimal"/>'
                        . '<w:lvlText w:val="%1."/>'
                        . '<w:lvlJc w:val="left"/>'
                        . '<w:pPr><w:ind w:left="360" w:hanging="360"/></w:pPr>'
                        . '<w:rPr><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
                        . '</w:lvl>'
                        . '</w:abstractNum>';
                    $numRefClean = '<w:num w:numId="99"><w:abstractNumId w:val="99"/></w:num>';

                    // Definisi untuk Ruang Lingkup (numId=98)
                    $abstractNumRL = '<w:abstractNum w:abstractNumId="98">'
                        . '<w:lvl w:ilvl="0">'
                        . '<w:start w:val="1"/>'
                        . '<w:numFmt w:val="decimal"/>'
                        . '<w:lvlText w:val="%1."/>'
                        . '<w:lvlJc w:val="left"/>'
                        . '<w:pPr><w:ind w:left="1000" w:hanging="360"/></w:pPr>'
                        . '<w:rPr><w:rFonts w:ascii="Tahoma" w:hAnsi="Tahoma" w:cs="Tahoma" w:eastAsia="Tahoma"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
                        . '</w:lvl>'
                        . '</w:abstractNum>';
                    $numRefRL = '<w:num w:numId="98"><w:abstractNumId w:val="98"/></w:num>';

                    // abstractNum HARUS sebelum w:num pertama (aturan Word XML)
                    if (preg_match('/<w:num\s/', $numberingXml)) {
                        $numberingXml = preg_replace('/<w:num\s/', $abstractNumClean . $abstractNumRL . '<w:num ', $numberingXml, 1);
                    } else {
                        $numberingXml = str_replace('</w:numbering>', $abstractNumClean . $abstractNumRL . '</w:numbering>', $numberingXml);
                    }
                    // numRef di akhir sebelum penutup
                    $numberingXml = str_replace('</w:numbering>', $numRefClean . $numRefRL . '</w:numbering>', $numberingXml);
                } else {
                    // Buat numbering.xml baru
                    $numberingXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                        . '<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
                        . $abstractNum . $numRef
                        . '</w:numbering>';

                    // Tambahkan relationship
                    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
                    if ($relsXml !== false && strpos($relsXml, 'numbering.xml') === false) {
                        $relsXml = str_replace(
                            '</Relationships>',
                            '<Relationship Id="rIdNum99" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/></Relationships>',
                            $relsXml
                        );
                        $zip->addFromString('word/_rels/document.xml.rels', $relsXml);
                    }

                    // Tambahkan content type
                    $contentTypes = $zip->getFromName('[Content_Types].xml');
                    if ($contentTypes !== false && strpos($contentTypes, 'numbering.xml') === false) {
                        $contentTypes = str_replace(
                            '</Types>',
                            '<Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/></Types>',
                            $contentTypes
                        );
                        $zip->addFromString('[Content_Types].xml', $contentTypes);
                    }
                }

                $zip->addFromString('word/numbering.xml', $numberingXml);
                $zip->close();
            }

            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Template dokumen .docx tidak ditemukan. Pastikan file "Template Program Kerja Audit.docx" ada di root folder.');
    }

    /**
     * API: Kembalikan flat list Risiko + Kontrol dari PKA yang terkait surat tugas.
     * Digunakan oleh form TOD dan TOE saat user memilih Surat Tugas.
     *
     * Response:
     *   { has_hierarki: bool, pka_id: int|null, risiko: [ { id, deskripsi, penyebab, dampak, kontrol: [...] } ] }
     */
    public function getHierarkiFlat($perencanaanId)
    {
        $pka = ProgramKerjaAudit::where('perencanaan_audit_id', $perencanaanId)
            ->with(['prosesBisnis.risikoList.kontrolList'])
            ->first();

        if (!$pka) {
            return response()->json([
                'has_hierarki' => false,
                'pka_id'       => null,
                'risiko'       => [],
            ]);
        }

        // Cek apakah PKA sudah punya hierarki baru
        $hasHierarki = $pka->prosesBisnis->isNotEmpty();

        if (!$hasHierarki) {
            return response()->json([
                'has_hierarki' => false,
                'pka_id'       => $pka->id,
                'risiko'       => [],
            ]);
        }

        // Flatten semua risiko dari semua proses bisnis
        $risikoFlat = collect();
        foreach ($pka->prosesBisnis as $pb) {
            foreach ($pb->risikoList as $risiko) {
                $risikoFlat->push([
                    'id'               => $risiko->id,
                    'deskripsi_risiko' => $risiko->deskripsi_risiko,
                    'penyebab_risiko'  => $risiko->penyebab_risiko,
                    'dampak_risiko'    => $risiko->dampak_risiko,
                    'kontrol'          => $risiko->kontrolList->map(fn($k) => [
                        'id'                => $k->id,
                        'deskripsi_kontrol' => $k->deskripsi_kontrol,
                    ])->values(),
                ]);
            }
        }

        return response()->json([
            'has_hierarki' => true,
            'pka_id'       => $pka->id,
            'risiko'       => $risikoFlat->values(),
        ]);
    }
}

