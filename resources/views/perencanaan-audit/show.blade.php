@extends('layouts.vertical', ['title' => 'Detail Program Kerja Audit'])

@section('css')
<style>
/* ===== HERO ===== */
.show-hero {
    background: #fff;
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 24px;
    border: 1px solid #e8edf5;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.show-hero h2 {
    font-size: 1.35rem;
    font-weight: 700;
    color: #1a3a5c;
    margin-bottom: 4px;
}
.show-hero .subtitle { font-size: 0.85rem; color: #6b7a99; }

.btn-back {
    background: #f3f4f6;
    color: #374151;
    border: none;
    border-radius: 10px;
    padding: 9px 18px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .2s;
}
.btn-back:hover { background: #e5e7eb; color: #111827; }

/* ===== SECTION CARDS ===== */
.section-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e8edf5;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    overflow: hidden;
}
.section-card .section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 22px;
    border-bottom: 1px solid #f0f3f9;
    background: #fafbfd;
}
.section-card .section-header .s-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.section-card .section-header h6 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1a3a5c;
    margin: 0;
}
.section-card .section-body { padding: 20px 22px; }

/* ===== INFO GRID ===== */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}
.info-item .info-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.info-item .info-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.4;
}
.info-item .info-value.muted { color: #6b7280; font-weight: 400; }

/* ===== No PKA badge ===== */
.no-pka-badge {
    font-weight: 700;
    font-size: 0.9rem;
    color: #6366f1;
    background: #eef2ff;
    border-radius: 8px;
    padding: 4px 12px;
    display: inline-block;
}
.no-surat-badge {
    font-weight: 600;
    font-size: 0.82rem;
    color: #1a3a5c;
    background: #eef3fb;
    border-radius: 8px;
    padding: 4px 12px;
    display: inline-block;
}
.auditee-badge {
    font-weight: 600;
    font-size: 0.82rem;
    color: #065f46;
    background: #d1fae5;
    border-radius: 8px;
    padding: 4px 12px;
    display: inline-block;
}

/* ===== PROSES BISNIS ===== */
.pb-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    background: #eff6ff;
    color: #1d4ed8;
    border-radius: 20px;
    padding: 5px 12px;
    margin: 4px 4px 4px 0;
    border: 1px solid #bfdbfe;
}

/* ===== RISK TABLE ===== */
.risk-table { width: 100%; border-collapse: collapse; }
.risk-table thead th {
    background: #f8fafd;
    font-size: 0.72rem;
    font-weight: 700;
    color: #6b7a99;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    border-bottom: 2px solid #e8edf5;
}
.risk-table tbody td {
    padding: 10px 12px;
    font-size: 0.82rem;
    color: #374151;
    border-bottom: 1px solid #f0f3f9;
    vertical-align: top;
}
.risk-table tbody tr:last-child td { border-bottom: none; }
.risk-num {
    font-weight: 700;
    color: #dc2626;
    background: #fef2f2;
    border-radius: 6px;
    padding: 2px 7px;
    font-size: 0.75rem;
    display: inline-block;
}

/* ===== MILESTONE ===== */
.milestone-list { list-style: none; padding: 0; margin: 0; }
.milestone-list li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f3f9;
}
.milestone-list li:last-child { border-bottom: none; }
.ms-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #2d6a9f;
    flex-shrink: 0;
    margin-top: 5px;
}
.ms-name { font-size: 0.85rem; font-weight: 600; color: #1f2937; margin-bottom: 2px; }
.ms-date { font-size: 0.78rem; color: #6b7280; display: flex; align-items: center; gap: 5px; }

/* ===== DOKUMEN TABLE ===== */
.dok-table { width: 100%; border-collapse: collapse; }
.dok-table thead th {
    background: #f8fafd;
    font-size: 0.72rem;
    font-weight: 700;
    color: #6b7a99;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    border-bottom: 2px solid #e8edf5;
}
.dok-table tbody td {
    padding: 10px 12px;
    font-size: 0.82rem;
    color: #374151;
    border-bottom: 1px solid #f0f3f9;
    vertical-align: middle;
}
.dok-table tbody tr:last-child td { border-bottom: none; }

/* Status badges */
.badge-approved { background: #d1fae5; color: #065f46; font-size: .72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.badge-rejected { background: #fee2e2; color: #991b1b; font-size: .72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.badge-pending  { background: #fef3c7; color: #92400e; font-size: .72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }

/* Data awal table */
.data-awal-table { width: 100%; border-collapse: collapse; }
.data-awal-table thead th {
    background: #f8fafd;
    font-size: 0.72rem;
    font-weight: 700;
    color: #6b7a99;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    border-bottom: 2px solid #e8edf5;
}
.data-awal-table tbody td {
    padding: 10px 12px;
    font-size: 0.82rem;
    color: #374151;
    border-bottom: 1px solid #f0f3f9;
    vertical-align: middle;
}
.data-awal-table tbody tr:last-child td { border-bottom: none; }

.empty-text { color: #9ca3af; font-size: 0.82rem; font-style: italic; }
</style>
@endsection

@section('content')

{{-- ===== HERO HEADER ===== --}}
<div class="show-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-file-document-edit-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Detail Program Kerja Audit</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo;
                <a href="{{ route('audit.pka.index') }}" class="text-decoration-none" style="color:#6b7a99;">Program Kerja Audit</a>
                &rsaquo; Detail
            </div>
        </div>
        <a href="{{ route('audit.pka.index') }}" class="btn-back">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- ===== INFO UTAMA ===== --}}
<div class="section-card">
    <div class="section-header">
        <div class="s-icon" style="background:#eef3fb;">
            <i class="mdi mdi-information-outline" style="color:#2d6a9f;"></i>
        </div>
        <h6>Informasi Utama</h6>
    </div>
    <div class="section-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">No PKA</div>
                <div class="info-value"><span class="no-pka-badge">{{ $item->no_pka }}</span></div>
            </div>
            <div class="info-item">
                <div class="info-label">Surat Tugas</div>
                <div class="info-value"><span class="no-surat-badge"><i class="mdi mdi-file-document-outline me-1"></i>{{ $item->perencanaanAudit->nomor_surat_tugas ?? '-' }}</span></div>
            </div>
            <div class="info-item">
                <div class="info-label">Auditee</div>
                <div class="info-value"><span class="auditee-badge">{{ $item->perencanaanAudit->auditee->divisi ?? '-' }}</span></div>
            </div>
            <div class="info-item">
                <div class="info-label">Tanggal PKA</div>
                <div class="info-value">
                    <i class="mdi mdi-calendar-outline me-1" style="color:#9ca3af;"></i>
                    {{ $item->tanggal_pka ? \Carbon\Carbon::parse($item->tanggal_pka)->format('d M Y') : '-' }}
                </div>
            </div>
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">Judul PKA</div>
                <div class="info-value">{{ $item->judul_pka ?? '-' }}</div>
            </div>
            @if($item->informasi_umum)
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">Informasi Umum</div>
                <div class="info-value muted">{{ $item->informasi_umum }}</div>
            </div>
            @endif
            @if($item->kpi_tidak_tercapai)
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">KPI Tidak Tercapai</div>
                <div class="info-value muted">{{ $item->kpi_tidak_tercapai }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== PROSES BISNIS ===== --}}
<div class="section-card">
    <div class="section-header">
        <div class="s-icon" style="background:#eff6ff;">
            <i class="mdi mdi-sitemap-outline" style="color:#1d4ed8;"></i>
        </div>
        <h6>Proses Bisnis</h6>
    </div>
    <div class="section-body">
        @if($item->proses_bisnis && is_array($item->proses_bisnis) && count($item->proses_bisnis) > 0)
            @foreach($item->proses_bisnis as $pb)
                <span class="pb-chip">
                    <i class="mdi mdi-check-circle-outline"></i>{{ $pb }}
                </span>
            @endforeach
        @else
            <span class="empty-text">Tidak ada data proses bisnis</span>
        @endif
    </div>
</div>

{{-- ===== RISK BASED AUDIT (Hierarki Baru: PB → Risiko → Kontrol) ===== --}}
<div class="section-card">
    <div class="section-header">
        <div class="s-icon" style="background:#fef2f2;">
            <i class="mdi mdi-alert-circle-outline" style="color:#dc2626;"></i>
        </div>
        <h6>Risk Based Audit</h6>
        @if($item->prosesBisnis && $item->prosesBisnis->count() > 0)
            <span class="ms-auto" style="font-size:.78rem;font-weight:600;background:#fef2f2;color:#dc2626;padding:3px 10px;border-radius:20px;">
                {{ $item->prosesBisnis->count() }} Proses Bisnis
            </span>
        @else
            <span class="ms-auto" style="font-size:.78rem;font-weight:600;background:#fef2f2;color:#dc2626;padding:3px 10px;border-radius:20px;">
                {{ $item->risks->count() }} Risiko (Data Lama)
            </span>
        @endif
    </div>
    <div class="section-body">

        @if($item->prosesBisnis && $item->prosesBisnis->count() > 0)
        {{-- ── Tampilan Hierarki Baru ── --}}
        <div class="accordion" id="accordionPb">
            @foreach($item->prosesBisnis as $pbIdx => $pb)
            <div class="accordion-item mb-2" style="border:1.5px solid #bfdbfe;border-radius:10px;overflow:hidden;">
                <h2 class="accordion-header" id="pbHead{{ $pb->id }}">
                    <button class="accordion-button {{ $pbIdx > 0 ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#pbCollapse{{ $pb->id }}"
                            aria-expanded="{{ $pbIdx === 0 ? 'true' : 'false' }}"
                            style="background:#eff6ff;font-weight:600;font-size:.9rem;color:#1e40af;">
                        <span class="me-2" style="background:#3b82f6;color:#fff;border-radius:6px;padding:2px 9px;font-size:.78rem;">
                            {{ $pbIdx + 1 }}
                        </span>
                        {{ $pb->nama_proses_bisnis }}
                        <span class="ms-auto me-3 badge" style="background:#dbeafe;color:#1d4ed8;font-size:.72rem;font-weight:600;">
                            {{ $pb->risikoList->count() }} Risiko
                        </span>
                    </button>
                </h2>
                <div id="pbCollapse{{ $pb->id }}"
                     class="accordion-collapse collapse {{ $pbIdx === 0 ? 'show' : '' }}"
                     aria-labelledby="pbHead{{ $pb->id }}"
                     data-bs-parent="#accordionPb">
                    <div class="accordion-body p-3">

                        @if($pb->risikoList->count() > 0)
                            @foreach($pb->risikoList as $rIdx => $risiko)
                            <div class="card mb-2" style="border:1.5px solid #fde68a;border-radius:8px;">
                                <div class="card-header d-flex align-items-center gap-2 py-2"
                                     style="background:#fffbeb;border-bottom:1px solid #fde68a;">
                                    <i class="mdi mdi-alert-outline text-warning"></i>
                                    <span class="fw-semibold" style="font-size:.85rem;color:#92400e;">
                                        Risiko #{{ $rIdx + 1 }}: {{ $risiko->deskripsi_risiko }}
                                    </span>
                                </div>
                                <div class="card-body py-2 px-3">
                                    @if($risiko->penyebab_risiko || $risiko->dampak_risiko)
                                    <div class="row g-2 mb-2">
                                        @if($risiko->penyebab_risiko)
                                        <div class="col-md-6">
                                            <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;">Penyebab</div>
                                            <div style="font-size:.82rem;color:#374151;">{{ $risiko->penyebab_risiko }}</div>
                                        </div>
                                        @endif
                                        @if($risiko->dampak_risiko)
                                        <div class="col-md-6">
                                            <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;">Dampak</div>
                                            <div style="font-size:.82rem;color:#374151;">{{ $risiko->dampak_risiko }}</div>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    @if($risiko->kontrolList->count() > 0)
                                    <div>
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <i class="mdi mdi-shield-check-outline text-success" style="font-size:.9rem;"></i>
                                            <span style="font-size:.78rem;font-weight:700;color:#065f46;">Kontrol Pengendalian</span>
                                        </div>
                                        <ol class="mb-0 ps-3" style="font-size:.82rem;color:#374151;">
                                            @foreach($risiko->kontrolList as $kontrol)
                                            <li>{{ $kontrol->deskripsi_kontrol }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    @else
                                        <span class="empty-text">Belum ada kontrol terdaftar</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <span class="empty-text">Belum ada risiko pada proses bisnis ini</span>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @elseif($item->risks && $item->risks->count() > 0)
        {{-- ── Fallback: Tampilan Data Lama (pka_risk_based_audit) ── --}}
        <div class="alert alert-info py-2 mb-3" style="font-size:.8rem;">
            <i class="mdi mdi-information-outline me-1"></i>
            Menampilkan data risiko lama. Silakan edit PKA ini untuk menggunakan struktur baru.
        </div>
        <div class="table-responsive">
            <table class="risk-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Deskripsi Risiko</th>
                        <th>Penyebab</th>
                        <th>Dampak</th>
                        <th>Pengendalian Eksisting</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->risks as $idx => $risk)
                    <tr>
                        <td><span class="risk-num">{{ $idx + 1 }}</span></td>
                        <td>{{ $risk->deskripsi_resiko }}</td>
                        <td>{{ $risk->penyebab_resiko ?: '-' }}</td>
                        <td>{{ $risk->dampak_resiko ?: '-' }}</td>
                        <td>{{ $risk->pengendalian_eksisting ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else
            <span class="empty-text">Tidak ada data risiko</span>
        @endif
    </div>
</div>



{{-- ===== MILESTONE ===== --}}
<div class="section-card">
    <div class="section-header">
        <div class="s-icon" style="background:#f0fdf4;">
            <i class="mdi mdi-calendar-check-outline" style="color:#16a34a;"></i>
        </div>
        <h6>Milestone</h6>
        <span class="ms-auto" style="font-size:.78rem;font-weight:600;background:#f0fdf4;color:#16a34a;padding:3px 10px;border-radius:20px;">
            {{ $item->milestones->count() }} Tahap
        </span>
    </div>
    <div class="section-body">
        @if($item->milestones && $item->milestones->count() > 0)
        <ul class="milestone-list">
            @foreach($item->milestones as $ms)
            <li>
                <span class="ms-dot"></span>
                <div>
                    <div class="ms-name">{{ $ms->nama_milestone }}</div>
                    <div class="ms-date">
                        <i class="mdi mdi-calendar-start" style="color:#2d6a9f;"></i>
                        <span style="color:#065f46;font-weight:600;">{{ $ms->tanggal_mulai ? \Carbon\Carbon::parse($ms->tanggal_mulai)->format('d M Y') : '-' }}</span>
                        <span style="color:#9ca3af;">s/d</span>
                        <i class="mdi mdi-calendar-end" style="color:#dc2626;"></i>
                        <span style="color:#991b1b;font-weight:600;">{{ $ms->tanggal_selesai ? \Carbon\Carbon::parse($ms->tanggal_selesai)->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
            <span class="empty-text">Tidak ada data milestone</span>
        @endif
    </div>
</div>

{{-- ===== DATA AWAL YANG PERLU DISIAPKAN ===== --}}
@php
    $dataAwal = is_array($item->data_awal_dokumen)
        ? $item->data_awal_dokumen
        : json_decode($item->data_awal_dokumen ?? '[]', true);
@endphp
@if(!empty($dataAwal))
<div class="section-card">
    <div class="section-header">
        <div class="s-icon" style="background:#fefce8;">
            <i class="mdi mdi-clipboard-list-outline" style="color:#d97706;"></i>
        </div>
        <h6>Data Awal Yang Perlu Disiapkan</h6>
    </div>
    <div class="section-body p-0">
        <div class="table-responsive">
            <table class="data-awal-table">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nama Dokumen</th>
                        <th>Ruang Lingkup</th>
                        <th>Periode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataAwal as $idx => $da)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $da['nama_dokumen'] ?? '-' }}</td>
                        <td>{{ $da['ruang_lingkup'] ?? '-' }}</td>
                        <td>{{ $da['periode'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ===== DOKUMEN PKA ===== --}}
<div class="section-card">
    <div class="section-header">
        <div class="s-icon" style="background:#f5f3ff;">
            <i class="mdi mdi-paperclip" style="color:#7c3aed;"></i>
        </div>
        <h6>Dokumen PKA</h6>
        <span class="ms-auto" style="font-size:.78rem;font-weight:600;background:#f5f3ff;color:#7c3aed;padding:3px 10px;border-radius:20px;">
            {{ $item->dokumen->count() }} File
        </span>
    </div>
    <div class="section-body p-0">
        @if($item->dokumen && $item->dokumen->count() > 0)
        <div class="table-responsive">
            <table class="dok-table">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nama Dokumen</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->dokumen as $idx => $dok)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-file-pdf-box" style="color:#dc2626;font-size:1.1rem;"></i>
                                {{ $dok->nama_dokumen }}
                            </div>
                        </td>
                        <td>
                            @if($dok->status_approval == 'approved')
                                <span class="badge-approved"><i class="mdi mdi-check-circle me-1"></i>Approved</span>
                            @elseif($dok->status_approval == 'rejected')
                                <span class="badge-rejected"><i class="mdi mdi-close-circle me-1"></i>Rejected</span>
                            @else
                                <span class="badge-pending"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="{{ asset('storage/'.$dok->file_path) }}" target="_blank"
                                   class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.78rem;">
                                    <i class="mdi mdi-eye me-1"></i>Lihat
                                </a>
                                @if($dok->status_approval == 'pending')
                                    @canApproveReject
                                    <form action="{{ route('audit.pka.approval', [$item->id, $dok->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" name="action" value="approve"
                                            class="btn btn-sm btn-success" style="border-radius:8px;font-size:.78rem;">
                                            <i class="mdi mdi-check me-1"></i>Approve
                                        </button>
                                        <button type="submit" name="action" value="reject"
                                            class="btn btn-sm btn-danger ms-1" style="border-radius:8px;font-size:.78rem;">
                                            <i class="mdi mdi-close me-1"></i>Reject
                                        </button>
                                    </form>
                                    @endcanApproveReject
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="section-body"><span class="empty-text">Belum ada dokumen yang diupload</span></div>
        @endif
    </div>
</div>

@endsection