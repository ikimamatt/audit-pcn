@extends('layouts.vertical', ['title' => 'Hasil TOE Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
        'node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'
     ])
<style>
.toe-hero { background:#fff; border-radius:16px; padding:24px 28px; color:#1a3a5c; margin-bottom:24px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,0.07); }
.toe-hero h2 { font-size:1.4rem; font-weight:700; margin-bottom:4px; letter-spacing:-0.5px; color:#1a3a5c; }
.toe-hero .subtitle { font-size:0.85rem; color:#6b7a99; }
.btn-add-toe { background:#1a3a5c; color:#fff; font-weight:600; border:none; border-radius:10px; padding:10px 22px; font-size:0.9rem; box-shadow:0 2px 10px rgba(26,58,92,0.18); transition:all .2s; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
.btn-add-toe:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(26,58,92,0.25); color:#fff; background:#2d6a9f; }
.filter-card { border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,0.04); margin-bottom:24px; background:#fff; }
.table-card { border-radius:16px; border:none; box-shadow:0 2px 20px rgba(0,0,0,0.06); overflow:hidden; }
.table-card .card-header-custom { background:#fff; padding:20px 24px 0; border-bottom:1px solid #f0f0f0; }
.table-card .card-header-custom h5 { font-size:1rem; font-weight:700; color:#1a3a5c; }
#responsive-datatable thead th { background:#f8fafd; color:#6b7a99; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; border-bottom:2px solid #e8edf5; padding:13px 14px; white-space:nowrap; }
#responsive-datatable tbody tr { transition:background .15s; }
#responsive-datatable tbody tr:hover { background:#f4f8ff !important; }
#responsive-datatable tbody td { padding:13px 14px; vertical-align:middle; border-color:#f0f3f9; font-size:0.875rem; color:#374151; }
.row-num { font-size:0.78rem; font-weight:700; color:#9ca3af; background:#f9fafb; border-radius:6px; padding:3px 8px; display:inline-block; }
.no-surat { font-weight:600; font-size:0.82rem; color:#1a3a5c; background:#eef3fb; border-radius:8px; padding:5px 10px; display:inline-block; max-width:200px; word-break:break-word; line-height:1.4; }
.tgl-badge { font-size:0.8rem; color:#6b7280; display:flex; align-items:center; gap:5px; white-space:nowrap; }
.action-wrap { display:flex; gap:5px; align-items:center; flex-wrap:wrap; }
.btn-act { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.85rem; border:none; transition:all .2s; cursor:pointer; text-decoration:none; flex-shrink:0; }
.btn-act:hover { transform:translateY(-1px); box-shadow:0 3px 10px rgba(0,0,0,0.15); }
.btn-act-edit { background:#fef3c7; color:#d97706; }
.btn-act-edit:hover { background:#fde68a; color:#b45309; }
.btn-act-delete { background:#fee2e2; color:#dc2626; }
.btn-act-delete:hover { background:#fecaca; color:#b91c1c; }
.btn-act-approve { background:#dcfce7; color:#16a34a; }
.btn-act-approve:hover { background:#bbf7d0; color:#15803d; }
.btn-act-reject { background:#f3f4f6; color:#4b5563; }
.btn-act-reject:hover { background:#e5e7eb; color:#374151; }
.btn-rk-badge { display:inline-flex; align-items:center; gap:6px; background:#f0f4ff; color:#3b5bdb; border:1.5px solid #c5d0fa; border-radius:8px; padding:5px 12px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all .2s; white-space:nowrap; }
.btn-rk-badge:hover { background:#dbe4ff; border-color:#748ffc; transform:translateY(-1px); box-shadow:0 3px 8px rgba(59,91,219,0.15); }
.sampel-toggle { text-decoration:none; font-weight:600; }
.sampel-toggle:hover { text-decoration:underline; }
.sampel-badge { font-size:0.75rem; color:#374151; background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:3px 8px; display:inline-block; max-width:200px; line-height:1.4; }
.empty-state { text-align:center; padding:60px 20px; color:#9ca3af; }
.empty-state i { font-size:3rem; margin-bottom:12px; display:block; opacity:.4; }
</style>
@endsection

@section('content')

<div class="toe-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-clipboard-check-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Hasil TOE Audit</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Audit &rsaquo; Hasil TOE Audit
            </div>
        </div>
        @canModifyData
        <a href="{{ route('audit.toe.create') }}" class="btn-add-toe">
            <i class="mdi mdi-plus-circle"></i> Tambah TOE
        </a>
        @endcanModifyData
    </div>
</div>

<div class="card filter-card">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap" action="">
            <label for="bulan" class="mb-0 fw-semibold text-muted" style="font-size:0.85rem;">Filter Bulan (Planning Start):</label>
            <input type="month" name="bulan" id="bulan" class="form-control form-control-sm" style="max-width:200px; border-radius:8px;" value="{{ request('bulan') }}">
            <button type="submit" class="btn btn-sm" style="background:#1a3a5c; color:#fff; border-radius:8px; padding:5px 12px;">
                <i class="mdi mdi-filter-outline me-1"></i> Filter
            </button>
            <a href="{{ route('audit.toe.index') }}" class="btn btn-sm btn-light" style="border-radius:8px; padding:5px 12px;">
                <i class="mdi mdi-refresh me-1"></i> Reset
            </a>
        </form>
    </div>
</div>

@php $total = $data->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Hasil TOE Audit</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} Data
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-centered dt-responsive w-100 mb-0" id="responsive-datatable">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Surat Tugas & Tgl Audit</th>
                        <th>Judul BPM</th>
                        <th>Sampel Audit</th>
                        <th>Risiko & Kontrol</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th>Evaluasi</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $i => $item)
                    <tr>
                        <td><span class="row-num">{{ $i+1 }}</span></td>
                        <td>
                            <div class="no-surat mb-2">
                                <i class="mdi mdi-file-document-outline me-1" style="color:#2d6a9f;"></i>
                                {{ $item->perencanaanAudit ? $item->perencanaanAudit->nomor_surat_tugas : '-' }}
                            </div>
                            <div class="tgl-badge">
                                <i class="mdi mdi-calendar-outline" style="color:#9ca3af;"></i>
                                @if($item->perencanaanAudit)
                                    {{ \Carbon\Carbon::parse($item->perencanaanAudit->tanggal_audit_mulai)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($item->perencanaanAudit->tanggal_audit_sampai)->format('d M Y') }}
                                @else -
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600; color:#1a3a5c; max-width:220px; line-height:1.4; font-size:0.85rem;">
                                {{ $item->judul_bpm }}
                            </div>
                        </td>
                        <td style="max-width:200px;">
                            @if($item->pemilihan_sampel_audit)
                                @php $sampel = $item->pemilihan_sampel_audit; $sampelId = 'sampel-' . $item->id; @endphp
                                @if(strlen($sampel) > 80)
                                    <div>
                                        <span class="sampel-short" id="short-{{ $sampelId }}" style="font-size:0.8rem; color:#374151; line-height:1.4;">
                                            {{ Str::limit($sampel, 80) }}
                                            <a href="javascript:void(0)" class="sampel-toggle" onclick="toggleSampel('{{ $sampelId }}')" style="color:#3b5bdb; font-size:0.75rem; white-space:nowrap;"> selengkapnya ▼</a>
                                        </span>
                                        <span class="sampel-full d-none" id="full-{{ $sampelId }}" style="font-size:0.8rem; color:#374151; line-height:1.4;">
                                            {{ $sampel }}
                                            <a href="javascript:void(0)" class="sampel-toggle" onclick="toggleSampel('{{ $sampelId }}')" style="color:#3b5bdb; font-size:0.75rem; white-space:nowrap;"> lebih sedikit ▲</a>
                                        </span>
                                    </div>
                                @else
                                    <span style="font-size:0.8rem; color:#374151; line-height:1.4;">{{ $sampel }}</span>
                                @endif
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $selectedKontrolIds = $item->pkaKontrol->pluck('id')->toArray();
                                $risikoCount = $item->pkaRisiko->count();
                                $kontrolCount = $item->pkaKontrol->count();
                                $modalData = $item->pkaRisiko->map(function($r) use ($selectedKontrolIds) {
                                    return [
                                        'risiko'  => $r->deskripsi_risiko,
                                        'kontrol' => $r->kontrolList
                                            ->filter(fn($k) => in_array($k->id, $selectedKontrolIds))
                                            ->pluck('deskripsi_kontrol')->values()->toArray(),
                                    ];
                                })->toArray();
                            @endphp
                            @if($risikoCount > 0)
                                <button type="button" class="btn-rk-badge" onclick="showRisikoModal(this)"
                                    data-judul="{{ $item->judul_bpm }}"
                                    data-risiko='@json($modalData)'>
                                    <i class="mdi mdi-shield-alert-outline"></i>
                                    <span>{{ $risikoCount }}R &middot; {{ $kontrolCount }}K</span>
                                </button>
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->file_kka_toe)
                                <a href="{{ asset('storage/' . $item->file_kka_toe) }}" target="_blank"
                                    class="btn btn-sm btn-light border d-flex align-items-center gap-1"
                                    style="font-size:0.75rem; border-radius:6px; padding:2px 8px; width:fit-content;">
                                    <i class="mdi mdi-download text-success"></i> KKA ToE
                                </a>
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status_approval == 'approved')
                                <span class="badge bg-success-subtle text-success px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-check-circle me-1"></i>Approved (Final)</span>
                            @elseif($item->status_approval == 'approved_level1')
                                <span class="badge bg-info-subtle text-info px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-check me-1"></i>Approved Level 1</span>
                            @elseif($item->status_approval == 'rejected')
                                @php $reason = htmlspecialchars($item->rejection_reason_level2 ?? $item->rejection_reason, ENT_QUOTES); @endphp
                                <span class="badge bg-danger-subtle text-danger px-2 py-1" style="border-radius:6px; cursor:pointer;" onclick="showRejectionReason('{{ $reason }}')" title="Klik untuk melihat alasan">
                                    <i class="mdi mdi-close-circle me-1"></i>Rejected (Final)
                                </span>
                            @elseif($item->status_approval == 'rejected_level1')
                                @php $reason1 = htmlspecialchars($item->rejection_reason_level1, ENT_QUOTES); @endphp
                                <span class="badge bg-warning-subtle text-warning px-2 py-1" style="border-radius:6px; cursor:pointer;" onclick="showRejectionReason('{{ $reason1 }}')" title="Klik untuk melihat alasan">
                                    <i class="mdi mdi-close me-1"></i>Rejected Level 1
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>
                            @endif
                        </td>
                        <td>
                            @php $ev = $item->evaluasi->first(); @endphp
                            @if($ev)
                                @php
                                    [$evClass, $evIcon] = match($ev->hasil_evaluasi) {
                                        'Efektif'          => ['bg-success-subtle text-success', 'mdi-check-circle-outline'],
                                        'Efektif Sebagian' => ['bg-warning-subtle text-warning', 'mdi-alert-circle-outline'],
                                        'Tidak Efektif'    => ['bg-danger-subtle text-danger',   'mdi-close-circle-outline'],
                                        default            => ['bg-secondary-subtle text-secondary', 'mdi-help-circle-outline'],
                                    };
                                @endphp
                                <span class="badge {{ $evClass }} px-2 py-1" style="border-radius:6px; white-space:nowrap;">
                                    <i class="mdi {{ $evIcon }} me-1"></i>{{ $ev->hasil_evaluasi }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-wrap">
                                @canModifyData
                                <a href="{{ route('audit.toe.edit', $item->id) }}" class="btn-act btn-act-edit" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <form action="{{ route('audit.toe.destroy', $item->id) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-act btn-act-delete btn-delete-swal" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                                @endcanModifyData
                                @canApproveReject
                                    @if($item->status_approval == 'pending')
                                        @isAsmanKspi
                                            <form id="approval-form-{{ $item->id }}" action="{{ route('audit.toe.approval', $item->id) }}" method="POST" class="m-0" style="display:inline-flex;gap:5px;">
                                                @csrf
                                                <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                <button type="button" class="btn-act btn-act-approve" onclick="approveData({{ $item->id }})" title="Approve Level 1"><i class="mdi mdi-check"></i></button>
                                                <button type="button" class="btn-act btn-act-reject" onclick="rejectData({{ $item->id }})" title="Reject Level 1"><i class="mdi mdi-close"></i></button>
                                            </form>
                                        @endisAsmanKspi
                                        @isKspi
                                            @php $hasAsmanKspi = \App\Helpers\AuthHelper::hasAsmanKspiUsers(); @endphp
                                            <form id="approval-form-{{ $item->id }}" action="{{ route('audit.toe.approval', $item->id) }}" method="POST" class="m-0" style="display:inline-flex;gap:5px;">
                                                @csrf
                                                <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                @if($hasAsmanKspi)
                                                    <button type="button" class="btn-act btn-act-approve" onclick="approveDataPending({{ $item->id }})" title="Butuh Approve ASMAN KSPI dulu"><i class="mdi mdi-check"></i></button>
                                                @else
                                                    <button type="button" class="btn-act btn-act-approve" onclick="approveData({{ $item->id }})" title="Approve"><i class="mdi mdi-check"></i></button>
                                                @endif
                                                <button type="button" class="btn-act btn-act-reject" onclick="rejectData({{ $item->id }})" title="Reject Level 2"><i class="mdi mdi-close"></i></button>
                                            </form>
                                        @endisKspi
                                    @elseif($item->status_approval == 'approved_level1')
                                        @isKspi
                                            <form id="approval-form-{{ $item->id }}" action="{{ route('audit.toe.approval', $item->id) }}" method="POST" class="m-0" style="display:inline-flex;gap:5px;">
                                                @csrf
                                                <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                <button type="button" class="btn-act btn-act-approve" onclick="approveData({{ $item->id }})" title="Approve Level 2"><i class="mdi mdi-check"></i></button>
                                                <button type="button" class="btn-act btn-act-reject" onclick="rejectData({{ $item->id }})" title="Reject Level 2"><i class="mdi mdi-close"></i></button>
                                            </form>
                                        @endisKspi
                                    @elseif($item->status_approval == 'rejected_level1')
                                        @isKspi
                                            <form id="approval-form-{{ $item->id }}" action="{{ route('audit.toe.approval', $item->id) }}" method="POST" class="m-0" style="display:inline-flex;gap:5px;">
                                                @csrf
                                                <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                <button type="button" class="btn-act btn-act-reject" onclick="rejectData({{ $item->id }})" title="Reject Level 2"><i class="mdi mdi-close"></i></button>
                                            </form>
                                        @endisKspi
                                    @endif
                                @endcanApproveReject
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($data->isEmpty())
                <div class="empty-state">
                    <i class="mdi mdi-clipboard-check-outline"></i>
                    <p class="mb-0 fw-semibold">Belum ada data Hasil TOE Audit</p>
                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah TOE</strong> untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Risiko & Kontrol --}}
<div class="modal fade" id="modalRisikoToe" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px; border:none;">
            <div class="modal-header" style="background:#1a3a5c; color:#fff; border-radius:16px 16px 0 0; border:none;">
                <div>
                    <h5 class="modal-title mb-0"><i class="mdi mdi-shield-alert-outline me-2"></i>Risiko &amp; Kontrol</h5>
                    <div id="modal-toe-judul" style="font-size:0.8rem; opacity:0.75; margin-top:2px;"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-toe-body" style="padding:24px;"></div>
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite(['resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleSampel(id) {
        const shortEl = document.getElementById('short-' + id);
        const fullEl  = document.getElementById('full-'  + id);
        shortEl.classList.toggle('d-none');
        fullEl.classList.toggle('d-none');
    }

    function showRejectionReason(reason) {
        Swal.fire({ title:'Alasan Penolakan', text:reason||'Tidak ada alasan', icon:'info', confirmButtonColor:'#1a3a5c', confirmButtonText:'Tutup' });
    }

    function showRisikoModal(btn) {
        const data = JSON.parse(btn.dataset.risiko);
        document.getElementById('modal-toe-judul').textContent = btn.dataset.judul;
        let html = '';
        data.forEach(function(item, i) {
            html += `<div class="mb-3"><div class="d-flex align-items-start gap-2 mb-2">
                <span style="background:#fee2e2;color:#dc2626;border-radius:6px;padding:2px 8px;font-size:0.72rem;font-weight:700;flex-shrink:0;margin-top:2px;">R${i+1}</span>
                <div style="font-size:0.88rem;font-weight:600;color:#1a3a5c;line-height:1.4;">${item.risiko}</div></div>`;
            if (item.kontrol.length > 0) {
                item.kontrol.forEach(function(k, j) {
                    html += `<div class="d-flex align-items-start gap-2 ms-4 mb-1">
                        <span style="background:#dbeafe;color:#1d4ed8;border-radius:6px;padding:2px 7px;font-size:0.68rem;font-weight:700;flex-shrink:0;margin-top:2px;">K${j+1}</span>
                        <div style="font-size:0.83rem;color:#4b5563;line-height:1.4;">${k}</div></div>`;
                });
            } else {
                html += `<div class="ms-4 text-muted" style="font-size:0.8rem;"><i class="mdi mdi-minus"></i> Tidak ada kontrol terkait</div>`;
            }
            html += `</div>`;
            if (i < data.length - 1) html += `<hr style="border-color:#f0f0f0;margin:12px 0;">`;
        });
        document.getElementById('modal-toe-body').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalRisikoToe')).show();
    }

    document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({ title:'Hapus TOE?', text:'Yakin ingin menghapus TOE ini?', icon:'warning',
                confirmButtonText:'Ya, Hapus', cancelButtonText:'Batal', showCancelButton:true,
                confirmButtonColor:'#d33', cancelButtonColor:'#3085d6'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    function approveData(id) {
        Swal.fire({ title:'Approve TOE?', text:'Apakah Anda yakin ingin approve TOE ini?', icon:'question',
            showCancelButton:true, confirmButtonColor:'#28a745', cancelButtonColor:'#6c757d',
            confirmButtonText:'Ya, Approve!', cancelButtonText:'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                document.getElementById('action-' + id).value = 'approve';
                document.getElementById('approval-form-' + id).submit();
            }
        });
    }

    function approveDataPending(id) {
        Swal.fire({ title:'Tidak Dapat Approve',
            html:'<p><strong>Data belum diapprove oleh ASMAN KSPI!</strong></p><p>Approval Level 2 membutuhkan approval Level 1 terlebih dahulu.</p>',
            icon:'warning', confirmButtonColor:'#3085d6', confirmButtonText:'Mengerti' });
    }

    function rejectData(id) {
        Swal.fire({
            title:'Tolak TOE',
            html:`<p class="mb-3">Apakah Anda yakin ingin menolak TOE ini?</p>
                <div class="form-group"><label class="form-label text-start d-block">Alasan Penolakan *</label>
                <textarea id="rejection_reason" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea></div>`,
            icon:'warning', showCancelButton:true, confirmButtonText:'Tolak', cancelButtonText:'Batal',
            confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d', focusConfirm:false,
            preConfirm: () => {
                const r = document.getElementById('rejection_reason').value.trim();
                if (!r) { Swal.showValidationMessage('Alasan penolakan harus diisi'); return false; }
                return r;
            }
        }).then(r => {
            if (r.isConfirmed) {
                const form = document.getElementById('approval-form-' + id);
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'rejection_reason'; inp.value = r.value;
                form.appendChild(inp);
                document.getElementById('action-' + id).value = 'reject';
                form.submit();
            }
        });
    }
</script>
@endsection