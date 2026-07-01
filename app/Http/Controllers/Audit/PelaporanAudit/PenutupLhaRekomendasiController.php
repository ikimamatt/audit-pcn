<?php

namespace App\Http\Controllers\Audit\PelaporanAudit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
// use App\Models\Models\Audit\PelaporanIsiLha;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterUser;
use App\Helpers\QueryHelper;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaporanAudit\StorePenutupLhaRekomendasiRequest;
use App\Http\Requests\Audit\PelaporanAudit\UpdatePenutupLhaRekomendasiRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Http\Requests\Audit\TindakLanjut\StoreTindakLanjutRequest;
use App\Http\Requests\Audit\TindakLanjut\UpdateTindakLanjutRequest;
use Illuminate\Support\Facades\Storage;

class PenutupLhaRekomendasiController extends Controller
{
    protected $rekomendasiService;
    protected $tindakLanjutService;

    public function __construct(
        \App\Services\Audit\PenutupLhaRekomendasiService $rekomendasiService,
        \App\Services\Audit\TindakLanjutService $tindakLanjutService
    ) {
        $this->rekomendasiService = $rekomendasiService;
        $this->tindakLanjutService = $tindakLanjutService;
    }
    public function selectNomorSuratTugas(Request $request)
    {
        // Single JOIN aggregate query replaces get() + map() + foreach loop
        $query = \Illuminate\Support\Facades\DB::table('perencanaan_audit as pa')
            ->join('pelaporan_hasil_audit as pha', 'pa.id', '=', 'pha.perencanaan_audit_id')
            ->join('pelaporan_temuan as pt', function ($join) {
                $join->on('pha.id', '=', 'pt.pelaporan_hasil_audit_id')
                     ->where('pt.status_approval', '=', 'approved');
            })
            ->select(
                'pa.id as perencanaan_audit_id',
                'pa.nomor_surat_tugas',
                'pa.jenis_audit',
                \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT pt.id) as count_temuan'),
                \Illuminate\Support\Facades\DB::raw('GROUP_CONCAT(DISTINCT pha.nomor_lha_lhk SEPARATOR ", ") as nomor_lha_lhk')
            )
            ->groupBy('pa.id', 'pa.nomor_surat_tugas', 'pa.jenis_audit');

        // Jika user adalah AUDITEE, filter berdasarkan unit (area) mereka
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            if ($userAreaId !== null) {
                $query->where('pa.area_id', $userAreaId);
            }
        }
        
        // Filter berdasarkan jenis audit
        if ($request->filled('jenis_audit')) {
            $query->where('pa.jenis_audit', $request->jenis_audit);
        }
        
        // Filter berdasarkan search (nomor surat tugas atau nomor LHA/LHK)
        if ($request->filled('search')) {
            $search = QueryHelper::escapeLike($request->search);
            $query->where(function($q) use ($search) {
                $q->where('pa.nomor_surat_tugas', 'like', '%' . $search . '%')
                  ->orWhere('pha.nomor_lha_lhk', 'like', '%' . $search . '%');
            });
        }
        
        $nomorSuratTugasList = $query->orderBy('pa.nomor_surat_tugas')->get()
            ->map(function($row) {
                return [
                    'nomor_surat_tugas'    => $row->nomor_surat_tugas,
                    'perencanaan_audit_id' => $row->perencanaan_audit_id,
                    'jenis_audit'          => $row->jenis_audit,
                    'nomor_lha_lhk'        => $row->nomor_lha_lhk ?? '',
                    'count_temuan'         => (int) $row->count_temuan,
                ];
            })
            ->values();
        
        // Ambil daftar jenis audit untuk filter dropdown
        $jenisAuditQuery = \Illuminate\Support\Facades\DB::table('perencanaan_audit as pa')
            ->join('pelaporan_hasil_audit as pha', 'pa.id', '=', 'pha.perencanaan_audit_id')
            ->join('pelaporan_temuan as pt', function ($join) {
                $join->on('pha.id', '=', 'pt.pelaporan_hasil_audit_id')
                     ->where('pt.status_approval', '=', 'approved');
            });
            
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            if ($userAreaId !== null) {
                $jenisAuditQuery->where('pa.area_id', $userAreaId);
            }
        }
        
        $jenisAuditList = $jenisAuditQuery
            ->distinct()
            ->orderBy('pa.jenis_audit')
            ->pluck('pa.jenis_audit')
            ->values();
        
        return view('audit.pelaporan.penutup-lha.select-nomor-surat-tugas', compact('nomorSuratTugasList', 'jenisAuditList'));
    }

    public function index(Request $request)
    {
        // Jika tidak ada nomor_surat_tugas, redirect ke halaman pemilihan
        if (!$request->filled('nomor_surat_tugas')) {
            return redirect()->route('audit.penutup-lha-rekomendasi.select-nomor-surat-tugas');
        }
        
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        $isiLhaId = $request->get('pelaporan_isi_lha_id');
        
        // Get all data first with relationships
        $query = PenutupLhaRekomendasi::with(['approvedBy', 'temuan.pelaporanHasilAudit.perencanaanAudit', 'picUsers.auditee']);
        
        // Filter berdasarkan nomor surat tugas
        if ($nomorSuratTugas) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        // Jika user adalah AUDITEE, filter semua rekomendasi berdasarkan unit (area) mereka
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                if ($userAreaId !== null) {
                    $q->where('area_id', $userAreaId);
                }
            });
        }
        
        // Apply filters
        if ($isiLhaId) {
            $query->where('pelaporan_isi_lha_id', $isiLhaId);
        }
        
        if ($request->filled('status_approval')) {
            $query->where('status_approval', $request->status_approval);
        }
        
        if ($request->filled('search')) {
            $query->where('rekomendasi', 'like', '%' . QueryHelper::escapeLike($request->search) . '%');
        }
        
        if ($request->filled('pic')) {
            $query->where('pic_rekomendasi', 'like', '%' . QueryHelper::escapeLike($request->pic) . '%');
        }
        
        $data = $query->get();
        
        // Ambil info perencanaan audit untuk ditampilkan
        $perencanaanAudit = null;
        if ($nomorSuratTugas) {
            $paQuery = \App\Models\Audit\PerencanaanAudit::where('nomor_surat_tugas', $nomorSuratTugas);
            if (\App\Helpers\AuthHelper::isAuditee()) {
                $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
                if ($userAreaId !== null) {
                    $paQuery->where('area_id', $userAreaId);
                }
            }
            $perencanaanAudit = $paQuery->first();
            if (\App\Helpers\AuthHelper::isAuditee() && !$perencanaanAudit) {
                abort(403, 'Anda tidak memiliki akses untuk melihat dokumen ini.');
            }
        }
        
        return view('audit.pelaporan.penutup-lha.index', compact('data', 'isiLhaId', 'nomorSuratTugas', 'perencanaanAudit'));
    }

    public function create(Request $request)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat rekomendasi.');
        }
        
        $isiLhaId = $request->get('pelaporan_isi_lha_id');
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        
        // Get approved ISS data from PelaporanTemuan
        $query = PelaporanTemuan::where('status_approval', 'approved')
            ->with(['pelaporanHasilAudit.perencanaanAudit']);
        
        // Filter berdasarkan nomor surat tugas jika ada
        if ($nomorSuratTugas) {
            $query->whereHas('pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        $approvedIss = $query->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_iss' => $item->nomor_iss,
                    'nomor_lha_lhk' => $item->pelaporanHasilAudit->nomor_lha_lhk ?? '-',
                    'hasil_temuan' => $item->hasil_temuan,
                    'permasalahan' => $item->permasalahan
                ];
            });
        
        // Dapatkan perencanaan_audit_id
        $perencanaanAudit = null;
        if ($nomorSuratTugas) {
            $perencanaanAudit = PerencanaanAudit::where('nomor_surat_tugas', $nomorSuratTugas)->first();
        } elseif ($isiLhaId) {
            $temuan = PelaporanTemuan::with('pelaporanHasilAudit.perencanaanAudit')->find($isiLhaId);
            $perencanaanAudit = $temuan->pelaporanHasilAudit->perencanaanAudit ?? null;
        }

        $areaId = $perencanaanAudit ? $perencanaanAudit->area_id : null;

        // Ambil semua user dari master_user untuk dipilih sebagai PIC
        // Filter: Hanya user dengan role AUDITEE dan dari unit (area) perencanaan tersebut
        $picUsersQuery = MasterUser::with(['akses', 'auditee', 'area'])
            ->whereHas('akses', function($q) {
                $q->where('nama_akses', 'AUDITEE');
            });

        if ($areaId) {
            $picUsersQuery->where('master_area_id', $areaId);
        }

        $picUsers = $picUsersQuery->orderBy('nama')->get();
        $returnUrl = $request->query('return_url');
        
        return view('audit.pelaporan.penutup-lha.create', compact('isiLhaId', 'approvedIss', 'picUsers', 'nomorSuratTugas', 'returnUrl'));
    }

    public function getIssData(Request $request)
    {
        $nomorSuratTugas = $request->get('nomor_surat_tugas');
        
        // Get approved ISS data for dropdown
        $query = PelaporanTemuan::where('status_approval', 'approved')
            ->with(['pelaporanHasilAudit.perencanaanAudit']);
        
        // Filter berdasarkan nomor surat tugas jika ada
        if ($nomorSuratTugas) {
            $query->whereHas('pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        // Jika user adalah AUDITEE, filter semua rekomendasi berdasarkan unit (area) mereka
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            $query->whereHas('pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                if ($userAreaId !== null) {
                    $q->where('area_id', $userAreaId);
                }
            });
        }
        
        $approvedIss = $query->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_iss' => $item->nomor_iss,
                    'nomor_lha_lhk' => $item->pelaporanHasilAudit->nomor_lha_lhk ?? '-',
                    'hasil_temuan' => $item->hasil_temuan,
                    'permasalahan' => $item->permasalahan
                ];
            });
        
        return response()->json($approvedIss);
    }

    public function store(StorePenutupLhaRekomendasiRequest $request)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat rekomendasi.');
        }
        
        $rekomendasi = $this->rekomendasiService->create($request->validated());
        
        // Reload dengan relasi untuk mendapatkan nomor surat tugas
        $rekomendasi->load(['temuan.pelaporanHasilAudit.perencanaanAudit']);
        
        // Ambil nomor surat tugas dari temuan
        $nomorSuratTugas = null;
        if ($rekomendasi->temuan && $rekomendasi->temuan->pelaporanHasilAudit && $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil ditambahkan!');
    }

    public function edit($id, Request $request)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit rekomendasi.');
        }

        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit.perencanaanAudit', 'picUsers.auditee'])->findOrFail($id);
        
        $perencanaanAudit = $item->temuan->pelaporanHasilAudit->perencanaanAudit ?? null;
        $areaId = $perencanaanAudit ? $perencanaanAudit->area_id : null;

        // Ambil semua user dari master_user untuk dipilih sebagai PIC
        // Filter: Hanya user dengan role AUDITEE dan dari unit (area) perencanaan tersebut
        $picUsersQuery = MasterUser::with(['akses', 'auditee', 'area'])
            ->whereHas('akses', function($q) {
                $q->where('nama_akses', 'AUDITEE');
            });

        if ($areaId) {
            $picUsersQuery->where('master_area_id', $areaId);
        }

        $picUsers = $picUsersQuery->orderBy('nama')->get();
        
        // Ambil PIC berdasarkan pic_type dari pivot table
        $picBusinessContact = $item->picUsers()->wherePivot('pic_type', 'business_contact')->first();
        $picApproval1 = $item->picUsers()->wherePivot('pic_type', 'approval_1_spi')->first();
        $picApproval2 = $item->picUsers()->wherePivot('pic_type', 'approval_2_spi')->first();
        
        $item->pic_business_contact_id = $picBusinessContact ? $picBusinessContact->id : null;
        $item->pic_approval_1_spi_id = $picApproval1 ? $picApproval1->id : null;
        $item->pic_approval_2_spi_id = $picApproval2 ? $picApproval2->id : null;

        $returnUrl = $request->query('return_url');
        
        return view('audit.pelaporan.penutup-lha.edit', compact('item', 'picUsers', 'returnUrl'));
    }

    public function update(UpdatePenutupLhaRekomendasiRequest $request, $id)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate rekomendasi.');
        }

        $item = PenutupLhaRekomendasi::findOrFail($id);
        $this->rekomendasiService->update($item, $request->validated());
        
        // Ambil nomor surat tugas dari temuan
        $item->load(['temuan.pelaporanHasilAudit.perencanaanAudit']);
        $nomorSuratTugas = null;
        if ($item->temuan && $item->temuan->pelaporanHasilAudit && $item->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil diupdate!');
    }

    public function destroy($id)
    {
        if (!\App\Helpers\AuthHelper::canModifyData()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus rekomendasi.');
        }

        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit.perencanaanAudit'])->findOrFail($id);
        
        // Ambil nomor surat tugas dari temuan sebelum delete
        $nomorSuratTugas = null;
        if ($item->temuan && $item->temuan->pelaporanHasilAudit && $item->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        $this->rekomendasiService->delete($item);
        
        return redirect()->route('audit.penutup-lha-rekomendasi.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', 'Rekomendasi penutup LHA/LHK berhasil dihapus!');
    }

    public function show($id)
    {
        $item = PenutupLhaRekomendasi::with([
            'approvedBy', 
            'temuan.pelaporanHasilAudit.perencanaanAudit', 
            'tindakLanjut', 
            'picUsers.auditee'
        ])->findOrFail($id);
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAreaId = \App\Helpers\AuthHelper::getUserAreaId();
            $pa = $item->temuan->pelaporanHasilAudit->perencanaanAudit ?? null;
            if (!$pa || ($userAreaId !== null && $pa->area_id != $userAreaId)) {
                abort(403, 'Anda tidak memiliki akses untuk melihat rekomendasi ini.');
            }
        }
        return view('audit.pelaporan.penutup-lha.show', compact('item'));
    }

    public function approval(ApprovalRequest $request, $id)
    {
        $item = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit'])->findOrFail($id);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $item,
            $request->action,
            $request->rejection_reason ?? $request->alasan_reject ?? null
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    // TINDAK LANJUT
    public function tindakLanjutForm(Request $request, $rekomendasiId)
    {
        $rekomendasi = PenutupLhaRekomendasi::with(['temuan.pelaporanHasilAudit', 'tindakLanjut'])->findOrFail($rekomendasiId);
        
        $currentUserId = \App\Helpers\AuthHelper::getCurrentUserId();
        $isBusinessContact = $rekomendasi->picUsers()
            ->where('master_user_id', $currentUserId)
            ->wherePivot('pic_type', 'business_contact')
            ->exists();
            
        if (!$isBusinessContact && !\App\Helpers\AuthHelper::isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menginput tindak lanjut. Hanya Business Contact yang berhak.');
        }

        $returnUrl = $request->query('return_url');

        return view('audit.pelaporan.penutup-lha.tindak-lanjut-form', compact('rekomendasi', 'returnUrl'));
    }

    public function storeTindakLanjut(StoreTindakLanjutRequest $request, $rekomendasiId)
    {
        $rekomendasi = PenutupLhaRekomendasi::findOrFail($rekomendasiId);

        $currentUserId = \App\Helpers\AuthHelper::getCurrentUserId();
        $isBusinessContact = $rekomendasi->picUsers()
            ->where('master_user_id', $currentUserId)
            ->wherePivot('pic_type', 'business_contact')
            ->exists();
            
        if (!$isBusinessContact && !\App\Helpers\AuthHelper::isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan tindak lanjut. Hanya Business Contact yang berhak.');
        }
        
        $data = $request->validated();
        if ($request->hasFile('file_eviden')) {
            $data['file_eviden_file'] = $request->file('file_eviden');
        }

        $this->tindakLanjutService->storeTindakLanjut($rekomendasiId, $data);
        
        // Ambil nomor surat tugas untuk redirect fallback
        $rekomendasi->load(['temuan.pelaporanHasilAudit.perencanaanAudit']);
        $nomorSuratTugas = null;
        if ($rekomendasi->temuan && $rekomendasi->temuan->pelaporanHasilAudit && $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit) {
            $nomorSuratTugas = $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas;
        }
        
        $validKomentar = array_filter($request->komentar, function($k) { 
            return trim($k) !== ''; 
        });
        $komentarCount = count($validKomentar);

        // Dynamic redirect back handling
        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            $expectedHost = parse_url(config('erp.allowed_domain'), PHP_URL_HOST);
            $actualHost = parse_url($returnUrl, PHP_URL_HOST);
            if ($expectedHost === $actualHost) {
                return redirect()->to($returnUrl)
                    ->with('success', "Berhasil menambahkan tindak lanjut dengan {$komentarCount} komentar!");
            }
        }

        return redirect()->route('audit.pemantauan.index', ['nomor_surat_tugas' => $nomorSuratTugas])
            ->with('success', "Berhasil menambahkan tindak lanjut dengan {$komentarCount} komentar!");
    }

    public function editTindakLanjut($id, Request $request)
    {
        $tindakLanjut = PenutupLhaTindakLanjut::with(['rekomendasi.temuan.pelaporanHasilAudit'])->findOrFail($id);
        $returnUrl = $request->query('return_url');
        return view('audit.pelaporan.penutup-lha.tindak-lanjut-edit', compact('tindakLanjut', 'returnUrl'));
    }

    public function updateTindakLanjut(UpdateTindakLanjutRequest $request, $id)
    {
        $data = $request->validated();
        if ($request->hasFile('file_eviden')) {
            $data['file_eviden_file'] = $request->file('file_eviden');
        }

        $tindakLanjut = $this->tindakLanjutService->updateTindakLanjut($id, $data);
        
        return redirect()->route('audit.penutup-lha-rekomendasi.show', $tindakLanjut->penutup_lha_rekomendasi_id)
            ->with('success', 'Tindak lanjut berhasil diupdate!');
    }

    public function destroyTindakLanjut($id)
    {
        $rekomendasiId = $this->tindakLanjutService->destroyTindakLanjut($id);
        return redirect()->route('audit.penutup-lha-rekomendasi.show', $rekomendasiId)
            ->with('success', 'Tindak lanjut berhasil dihapus!');
    }

    public function myReminders()
    {
        $userId = \App\Helpers\AuthHelper::getCurrentUserId();
        if (!$userId) {
            return response()->json([]);
        }

        $reminders = PenutupLhaRekomendasi::whereIn('status_tindak_lanjut', ['open', 'on_progress'])
            ->whereHas('picUsers', function ($q) use ($userId) {
                $q->where('master_user_id', $userId)
                  ->where('pic_type', 'business_contact');
            })
            ->whereIn('status_approval', ['approved', 'rejected', 'rejected_level1'])
            ->with(['temuan.pelaporanHasilAudit.perencanaanAudit'])
            ->get()
            ->map(function ($item) {
                $pa = $item->temuan->pelaporanHasilAudit->perencanaanAudit ?? null;
                $pha = $item->temuan->pelaporanHasilAudit ?? null;
                $pt = $item->temuan ?? null;

                return [
                    'id' => $item->id,
                    'rekomendasi' => $item->rekomendasi,
                    'target_waktu' => $item->target_waktu,
                    'nomor_surat_tugas' => $pa ? $pa->nomor_surat_tugas : '-',
                    'nomor_lha_lhk' => $pha ? $pha->nomor_lha_lhk : '-',
                    'nomor_iss' => $pt ? $pt->nomor_iss : '-',
                    'link' => route('audit.penutup-lha-rekomendasi.tindak-lanjut.form', ['rekomendasi' => $item->id]),
                ];
            });

        return response()->json($reminders);
    }
} 