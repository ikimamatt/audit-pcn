<?php

namespace App\Http\Controllers\Audit\PelaksanaanAudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Audit\PelaksanaanAudit\StoreTodBpmRequest;
use App\Http\Requests\Audit\PelaksanaanAudit\UpdateTodBpmRequest;
use App\Http\Requests\Audit\PelaporanAudit\ApprovalRequest;
use App\Models\TodBpmAudit;
use App\Models\Audit\PerencanaanAudit;
use App\Models\WalkthroughAudit;
use App\Services\Audit\TodBpmService;
use Carbon\Carbon;

class TodBpmAuditController extends Controller
{
    protected $todBpmService;

    public function __construct(TodBpmService $todBpmService)
    {
        $this->todBpmService = $todBpmService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Pindahkan filter ke DB-level dengan scope forCurrentAuditee
        $query = TodBpmAudit::with(['perencanaanAudit.auditee', 'evaluasi', 'pkaRisiko.kontrolList', 'pkaKontrol'])
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
    public function store(StoreTodBpmRequest $request)
    {
        // Ambil file BPM dari walkthrough
        $walkthrough = WalkthroughAudit::findOrFail($request->walkthrough_id);
        if (!$walkthrough->file_bpm) {
            return redirect()->back()
                ->with('error', 'File BPM dari walkthrough tidak ditemukan!')
                ->withInput();
        }

        $data = $request->validated();
        if ($request->hasFile('file_kka_tod')) {
            $data['file_kka_tod_file'] = $request->file('file_kka_tod');
        }

        try {
            $this->todBpmService->create($data);
            return redirect()->route('audit.tod-bpm.index')->with('success', 'TOD berhasil disimpan!');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
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
            'evaluasi',
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
    public function update(UpdateTodBpmRequest $request, $id)
    {
        $item = TodBpmAudit::findOrFail($id);

        $data = $request->validated();
        if ($request->hasFile('file_kka_tod')) {
            $data['file_kka_tod_file'] = $request->file('file_kka_tod');
        }

        $this->todBpmService->update($item, $data);

        return redirect()->route('audit.tod-bpm.index')->with('success', 'Data TOD berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = TodBpmAudit::findOrFail($id);
        $this->todBpmService->delete($item);
        return redirect()->route('audit.tod-bpm.index')->with('success', 'Data TOD berhasil dihapus!');
    }

    public function approval($id, ApprovalRequest $request)
    {
        $item = TodBpmAudit::findOrFail($id);

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
