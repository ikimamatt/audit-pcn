<?php

namespace App\Http\Controllers\Audit\PelaksanaanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreToeRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateToeRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Models\ToeAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\TodBpmAudit;
use App\Services\Audit\ToeService;
use Carbon\Carbon;

class ToeAuditController extends Controller
{
    protected $toeService;

    public function __construct(ToeService $toeService)
    {
        $this->toeService = $toeService;
    }

    public function index(Request $request)
    {
        // Pindahkan filter ke DB-level dengan scope forCurrentAuditee
        $query = ToeAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko.kontrolList', 'pkaKontrol'])
            ->forCurrentAuditee('perencanaanAudit');

        // Filter by specific ID from details page
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('bulan')) {
            $selectedMonth = Carbon::parse($request->bulan);
            $query->whereHas('perencanaanAudit', function($q) use ($selectedMonth) {
                $q->whereYear('tanggal_audit_mulai', $selectedMonth->year)
                  ->whereMonth('tanggal_audit_mulai', $selectedMonth->month);
            });
        }

        $data = $query->get();

        return view('audit.toe.index', compact('data'));
    }

    public function create(Request $request)
    {
        $returnUrl  = $request->query('return_url');
        $suratTugas = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $bpmList    = TodBpmAudit::all();
        return view('audit.toe.create', compact('suratTugas', 'bpmList', 'returnUrl'));
    }

    public function store(StoreToeRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('file_kka_toe')) {
            $data['file_kka_toe_file'] = $request->file('file_kka_toe');
        }

        $this->toeService->create($data);

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

    public function edit($id, Request $request)
    {
        $item = ToeAudit::with([
            'perencanaanAudit',
            'pkaRisiko',
            'pkaKontrol',
            'evaluasi',
        ])->findOrFail($id);

        $returnUrl  = $request->query('return_url');
        $suratTugas = PerencanaanAudit::with('auditee')->orderBy('nomor_surat_tugas')->get();
        $bpmList    = TodBpmAudit::all();

        $selectedRisikoIds  = $item->pkaRisiko->pluck('id')->toArray();
        $selectedKontrolIds = $item->pkaKontrol->pluck('id')->toArray();

        return view('audit.toe.edit', compact(
            'item', 'suratTugas', 'bpmList',
            'selectedRisikoIds', 'selectedKontrolIds', 'returnUrl'
        ));
    }

    public function update(UpdateToeRequest $request, $id)
    {
        $item = ToeAudit::findOrFail($id);

        $data = $request->validated();
        if ($request->hasFile('file_kka_toe')) {
            $data['file_kka_toe_file'] = $request->file('file_kka_toe');
        }

        $this->toeService->update($item, $data);

        return redirect()->route('audit.toe.index')->with('success', 'Data TOE berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = ToeAudit::findOrFail($id);
        $this->toeService->delete($item);
        return redirect()->route('audit.toe.index')->with('success', 'Data TOE berhasil dihapus!');
    }

    public function approval($id, ApprovalRequest $request)
    {
        $item = ToeAudit::findOrFail($id);

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