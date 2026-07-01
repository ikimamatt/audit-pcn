<?php

namespace App\Http\Controllers\Audit\PerencanaanAudit;

use App\Http\Controllers\Controller;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\Models\Audit\PkaDokumen;
use App\Models\Audit\PerencanaanAudit;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PerencanaanAudit\StoreProgramKerjaAuditRequest;
use App\Http\Requests\Audit\PerencanaanAudit\UpdateProgramKerjaAuditRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Services\Audit\ProgramKerjaAuditService;
use App\Services\Audit\PkaDocumentService;

class ProgramKerjaAuditController extends Controller
{
    protected $pkaService;
    protected $documentService;

    public function __construct(
        ProgramKerjaAuditService $pkaService,
        PkaDocumentService $documentService
    ) {
        $this->pkaService = $pkaService;
        $this->documentService = $documentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProgramKerjaAudit::with(['perencanaanAudit.auditee', 'perencanaanAudit.area', 'risks', 'milestones', 'dokumen'])->get();
        return view('perencanaan-audit.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Ambil semua surat tugas yang belum memiliki PKA
        $suratTugas = PerencanaanAudit::whereDoesntHave('programKerjaAudit')->with('auditee')->orderBy('nomor_surat_tugas')->get();
        $returnUrl = $request->query('return_url');

        return view('perencanaan-audit.create', compact('suratTugas', 'returnUrl'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProgramKerjaAuditRequest $request)
    {
        $data = $request->validated();
        $data['dokumen_files'] = $request->file('dokumen') ?? [];

        $this->pkaService->create($data);

        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            $expectedHost = parse_url(config('erp.allowed_domain'), PHP_URL_HOST);
            $actualHost = parse_url($returnUrl, PHP_URL_HOST);
            if ($expectedHost === $actualHost) {
                return redirect()->to($returnUrl)->with('success', 'Program Kerja Audit berhasil disimpan!');
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
    public function edit(Request $request, $id)
    {
        $item = ProgramKerjaAudit::with([
            'perencanaanAudit',
            'prosesBisnis.risikoList.kontrolList',
            'milestones',
            'dokumen',
        ])->findOrFail($id);

        $suratTugas = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $returnUrl = $request->query('return_url');

        return view('perencanaan-audit.edit', compact('item', 'suratTugas', 'returnUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramKerjaAuditRequest $request, $id)
    {
        $pka = ProgramKerjaAudit::findOrFail($id);

        $data = $request->validated();
        $data['dokumen_files'] = $request->file('dokumen') ?? [];

        $this->pkaService->update($pka, $data);

        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            $expectedHost = parse_url(config('erp.allowed_domain'), PHP_URL_HOST);
            $actualHost = parse_url($returnUrl, PHP_URL_HOST);
            if ($expectedHost === $actualHost) {
                return redirect()->to($returnUrl)->with('success', 'Program Kerja Audit berhasil diupdate!');
            }
        }

        return redirect()->route('audit.pka.index')->with('success', 'Program Kerja Audit berhasil diupdate!');
    }

    // Approval dokumen
    public function approval($pkaId, $dokId, ApprovalRequest $request)
    {
        $dok = PkaDokumen::findOrFail($dokId);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $dok,
            $request->action,
            $request->rejection_reason ?? null
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    // Approval keseluruhan PKA
    public function approvalMain($id, ApprovalRequest $request)
    {
        $pka = ProgramKerjaAudit::findOrFail($id);

        $result = \App\Helpers\ApprovalHelper::processApproval(
            $pka,
            $request->action,
            $request->rejection_reason ?? null
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
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

        $relations = $this->pkaService->checkRelations($item);

        return response()->json([
            'has_relations' => count($relations) > 0,
            'relations'     => $relations,
            'no_pka'        => $item->no_pka,
            'surat_tugas'   => $item->perencanaanAudit->nomor_surat_tugas ?? '-',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = ProgramKerjaAudit::findOrFail($id);
        $this->pkaService->delete($item);

        return redirect()->route('audit.pka.index')->with('success', 'Data PKA dan seluruh proses audit terkait berhasil dihapus!');
    }

    /**
     * Download dokumen PKA berdasarkan template.
     */
    public function download($id)
    {
        $pka = ProgramKerjaAudit::findOrFail($id);
        $filename = 'PKA_' . str_replace(['/', '\\'], '_', $pka->no_pka) . '.docx';

        try {
            $tempPath = $this->documentService->generate($id);
            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat men-generate dokumen.');
        }
    }

    /**
     * API: Kembalikan flat list Risiko + Kontrol dari PKA yang terkait surat tugas.
     * Digunakan oleh form TOD dan TOE saat user memilih Surat Tugas.
     */
    public function getHierarkiFlat($perencanaanId)
    {
        return response()->json($this->pkaService->getHierarkiFlat($perencanaanId));
    }
}
