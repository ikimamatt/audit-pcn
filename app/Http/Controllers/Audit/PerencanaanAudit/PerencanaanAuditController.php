<?php

namespace App\Http\Controllers\Audit\PerencanaanAudit;

use App\Http\Controllers\Controller;
use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PerencanaanAudit\StorePerencanaanRequest;
use App\Http\Requests\Audit\PerencanaanAudit\UpdatePerencanaanRequest;
use App\Services\Audit\PerencanaanAuditService;
use App\Services\Audit\NomorGeneratorService;

class PerencanaanAuditController extends Controller
{
    protected $perencanaanService;
    protected $nomorService;

    public function __construct(
        PerencanaanAuditService $perencanaanService,
        NomorGeneratorService $nomorService
    ) {
        $this->perencanaanService = $perencanaanService;
        $this->nomorService = $nomorService;
    }

    public function index()
    {
        $data = PerencanaanAudit::with('auditee')->get();
        return view('audit.perencanaan.index', compact('data'));
    }

    public function create()
    {
        $auditees   = MasterAuditee::all();
        $jenisAudits = MasterJenisAudit::all();
        $areas      = MasterArea::with('region')->orderBy('kd_area')->get();
        $auditors   = MasterUser::with('akses')
            ->whereDoesntHave('akses', function($q) {
                $q->where('nama_akses', 'AUDITEE');
            })
            ->orderBy('nama')
            ->get();
        
        return view('audit.perencanaan.create', compact('auditees', 'auditors', 'jenisAudits', 'areas'));
    }

    public function store(StorePerencanaanRequest $request)
    {
        $perencanaan = $this->perencanaanService->create($request->validated());
        
        // Redirect ke index dengan session data untuk modal
        return redirect()->route('audit.perencanaan.index')->with([
            'success' => 'Data perencanaan audit berhasil disimpan!',
            'nomor' => $perencanaan->nomor_surat_tugas
        ]);
    }

    public function edit($id)
    {
        $item = PerencanaanAudit::findOrFail($id);
        
        // Memisahkan periode_audit menjadi periode_awal dan periode_akhir
        if ($item->periode_audit) {
            $periodeParts = explode(' s/d ', $item->periode_audit);
            $item->periode_awal = $periodeParts[0] ?? '';
            $item->periode_akhir = $periodeParts[1] ?? '';
        }
        
        $auditees    = MasterAuditee::all();
        $jenisAudits = MasterJenisAudit::all();
        $areas       = MasterArea::with('region')->orderBy('kd_area')->get();
        $auditors    = MasterUser::with('akses')
            ->whereDoesntHave('akses', function($q) {
                $q->where('nama_akses', 'AUDITEE');
            })
            ->orderBy('nama')
            ->get();
        
        // Mencocokkan auditor lama dengan user baru berdasarkan NIP
        $matchedAuditorIds = [];
        if ($item->auditor && is_array($item->auditor)) {
            foreach ($item->auditor as $auditorText) {
                // Parse format: "Nama - NIP: xxxxx" atau format lain
                if (preg_match('/NIP:\s*([^\s-]+)/', $auditorText, $matches)) {
                    $nip = trim($matches[1]);
                    $matchedUser = MasterUser::where('nip', $nip)->first();
                    if ($matchedUser) {
                        $matchedAuditorIds[] = $matchedUser->id;
                    }
                }
            }
        }
        $item->matched_auditor_ids = $matchedAuditorIds;
        
        return view('audit.perencanaan.edit', compact('item', 'auditees', 'auditors', 'jenisAudits', 'areas'));
    }

    public function update(UpdatePerencanaanRequest $request, $id)
    {
        $item = PerencanaanAudit::findOrFail($id);
        
        $this->perencanaanService->update($item, $request->validated());
        
        // Redirect ke index dengan session data untuk modal
        return redirect()->route('audit.perencanaan.index')->with([
            'success' => 'Data perencanaan audit berhasil diupdate!',
            'nomor' => $item->nomor_surat_tugas
        ]);
    }

    public function destroy($id)
    {
        $item = PerencanaanAudit::findOrFail($id);
        
        try {
            $this->perencanaanService->delete($item);
            return redirect()->route('audit.perencanaan.index')->with('success', 'Data perencanaan audit berhasil dihapus!');
        } catch (\DomainException $e) {
            return redirect()->route('audit.perencanaan.index')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('audit.perencanaan.index')->with('error', 
                'Terjadi kesalahan saat menghapus data. Silakan coba lagi atau hubungi administrator.'
            );
        }
    }

    /**
     * API endpoint untuk mendapatkan nomor surat tugas otomatis
     */
    public function getNomorSuratTugas(Request $request)
    {
        $jenisAudit = $request->input('jenis_audit');
        $nomorSuratTugas = $this->nomorService->generateNomorSuratTugas($jenisAudit);
        
        return response()->json([
            'nomor_surat_tugas' => $nomorSuratTugas
        ]);
    }
} 