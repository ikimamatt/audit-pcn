@extends('layouts.vertical', ['title' => 'Perencanaan Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
<style>
/* ===== PAGE HEADER ===== */
.pa-hero {
    background: #fff;
    border-radius: 16px;
    padding: 24px 28px;
    color: #1a3a5c;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    border: 1px solid #e8edf5;
}
.pa-hero h2 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 4px;
    letter-spacing: -0.5px;
    color: #1a3a5c;
}
.pa-hero .subtitle {
    font-size: 0.85rem;
    color: #6b7a99;
}
.btn-add-pa {
    background: #1a3a5c;
    color: #fff;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    padding: 10px 22px;
    font-size: 0.9rem;
    box-shadow: 0 2px 10px rgba(26,58,92,0.18);
    transition: all .2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-add-pa:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(26,58,92,0.25);
    color: #fff;
    background: #2d6a9f;
}

/* ===== STAT CARDS ===== */
.stat-card {
    border-radius: 14px;
    padding: 20px 22px;
    border: none;
    transition: transform .2s, box-shadow .2s;
    position: relative;
    overflow: hidden;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.12) !important;
}
.stat-card .stat-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    margin-bottom: 14px;
}
.stat-card .stat-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
}
.stat-card .stat-label {
    font-size: 0.78rem;
    font-weight: 500;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ===== TABLE ===== */
.table-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 2px 20px rgba(0,0,0,0.06);
    overflow: hidden;
}
.table-card .card-header-custom {
    background: #fff;
    padding: 20px 24px 0;
    border-bottom: 1px solid #f0f0f0;
}
.table-card .card-header-custom h5 {
    font-size: 1rem;
    font-weight: 700;
    color: #1a3a5c;
}

#responsive-datatable thead th {
    background: #f8fafd;
    color: #6b7a99;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 2px solid #e8edf5;
    padding: 13px 14px;
    white-space: nowrap;
}
#responsive-datatable tbody tr {
    transition: background .15s;
}
#responsive-datatable tbody tr:hover {
    background: #f4f8ff !important;
}
#responsive-datatable tbody td {
    padding: 13px 14px;
    vertical-align: middle;
    border-color: #f0f3f9;
    font-size: 0.875rem;
    color: #374151;
}

/* Nomor surat styling */
.no-surat {
    font-weight: 600;
    font-size: 0.82rem;
    color: #1a3a5c;
    background: #eef3fb;
    border-radius: 8px;
    padding: 5px 10px;
    display: inline-block;
    max-width: 200px;
    word-break: break-word;
    line-height: 1.4;
}

/* Jenis audit badge */
.badge-jenis {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 20px;
    letter-spacing: 0.3px;
}
.badge-reguler { background: #dbeafe; color: #1d4ed8; }
.badge-khusus  { background: #fce7f3; color: #9d174d; }
.badge-default { background: #f3f4f6; color: #374151; }

/* Auditor badges */
.badge-auditor {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 600;
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 20px;
    padding: 3px 10px;
    margin: 2px 2px 2px 0;
}

/* Ruang lingkup */
.scope-item {
    display: inline-block;
    font-size: 0.72rem;
    background: #fef9c3;
    color: #854d0e;
    border-radius: 6px;
    padding: 2px 8px;
    margin: 2px 2px 2px 0;
}

/* Periode */
.periode-badge {
    font-size: 0.78rem;
    font-weight: 600;
    color: #6366f1;
    background: #eef2ff;
    border-radius: 8px;
    padding: 4px 10px;
    display: inline-block;
}

/* Tanggal audit */
.tgl-audit {
    font-size: 0.8rem;
    color: #374151;
}
.tgl-audit .tgl-range {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.tgl-audit .tgl-start { color: #065f46; font-weight: 600; }
.tgl-audit .tgl-end   { color: #991b1b; font-weight: 600; }
.tgl-audit .tgl-sep   { color: #9ca3af; font-size: 0.7rem; }

/* Auditee */
.auditee-chip {
    font-size: 0.8rem;
    font-weight: 600;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 6px;
}
.auditee-chip .dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2d6a9f, #3a8dcc);
    flex-shrink: 0;
}

/* Tanggal surat */
.tgl-surat {
    font-size: 0.8rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Action buttons */
.action-wrap {
    display: flex;
    gap: 5px;
    align-items: center;
}
.btn-act {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
    border: none;
    transition: all .2s;
    cursor: pointer;
    text-decoration: none;
}
.btn-act:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.15); }
.btn-act-edit   { background: #fef3c7; color: #d97706; }
.btn-act-edit:hover { background: #fde68a; color: #b45309; }
.btn-act-delete { background: #fee2e2; color: #dc2626; }
.btn-act-delete:hover { background: #fecaca; color: #b91c1c; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}
.empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; opacity: .4; }
.empty-state p { font-size: 0.9rem; }

/* Nomor row */
.row-num {
    font-size: 0.78rem;
    font-weight: 700;
    color: #9ca3af;
    background: #f9fafb;
    border-radius: 6px;
    padding: 3px 8px;
    display: inline-block;
}
</style>
@endsection

@section('content')

{{-- ===== HERO HEADER ===== --}}
<div class="pa-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-clipboard-check-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Perencanaan Audit</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Perencanaan Audit
            </div>
        </div>
        @canModifyData
        <a href="{{ route('audit.perencanaan.create') }}" class="btn-add-pa">
            <i class="mdi mdi-plus-circle"></i> Tambah Surat Tugas
        </a>
        @endcanModifyData
    </div>
</div>

@php $total = $data->count(); @endphp

{{-- ===== ALERT ===== --}}
@include('components.alert')

{{-- ===== TABLE ===== --}}
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Surat Tugas Audit</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} Surat Tugas
        </span>
    </div>

    <div class="card-body p-0 pt-0">
        <div class="table-responsive">
            <table id="responsive-datatable" class="table table-centered w-100 dt-responsive nowrap mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nomor Surat Tugas</th>
                        <th>Tgl Surat Tugas</th>
                        <th>Jenis Audit</th>
                        <th>Auditee</th>
                        <th>Petugas</th>
                        <th>Ruang Lingkup</th>
                        <th>Periode</th>
                        <th>Tanggal Audit</th>
                        <th style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            {{-- No --}}
                            <td><span class="row-num">{{ $index + 1 }}</span></td>

                            {{-- Nomor Surat Tugas --}}
                            <td>
                                <div class="no-surat">
                                    <i class="mdi mdi-file-document-outline me-1" style="color:#2d6a9f;"></i>{{ $item->nomor_surat_tugas }}
                                </div>
                            </td>

                            {{-- Tanggal Surat Tugas --}}
                            <td>
                                <div class="tgl-surat">
                                    <i class="mdi mdi-calendar-outline" style="color:#9ca3af;"></i>
                                    {{ $item->tanggal_surat_tugas ? \Carbon\Carbon::parse($item->tanggal_surat_tugas)->format('d M Y') : '-' }}
                                </div>
                            </td>

                            {{-- Jenis Audit --}}
                            <td>
                                @php
                                    $jenis = $item->jenis_audit ?? '-';
                                    $jenisClass = 'badge-default';
                                    if (stripos($jenis, 'reguler') !== false || stripos($jenis, 'regular') !== false) $jenisClass = 'badge-reguler';
                                    elseif (stripos($jenis, 'khusus') !== false || stripos($jenis, 'special') !== false) $jenisClass = 'badge-khusus';
                                @endphp
                                <span class="badge-jenis {{ $jenisClass }}">{{ $jenis }}</span>
                            </td>

                            {{-- Auditee --}}
                            <td>
                                <div class="auditee-chip">
                                    <span class="dot"></span>
                                    {{ $item->auditee->divisi ?? '-' }}
                                </div>
                            </td>

                            {{-- Petugas --}}
                            <td style="min-width: 180px; max-width: 240px;">
                                <div class="d-flex flex-column gap-1">
                                    {{-- Koordinator --}}
                                    @if($item->koordinator)
                                        <div class="rounded px-2 py-1 border" style="background:#f8f9fa; font-size:0.72rem; line-height:1.4;">
                                            <div class="fw-semibold text-muted" style="font-size:0.68rem; text-transform:uppercase; letter-spacing:.03em;">
                                                <i class="mdi mdi-tag-outline me-1"></i>Koordinator
                                            </div>
                                            <div class="text-dark fw-bold" style="word-break:break-word;">
                                                <i class="mdi mdi-account-star-outline me-1"></i>{{ $item->koordinator->nama ?? '-' }}{{ $item->koordinator->nip ? ' - NIP: ' . $item->koordinator->nip : '' }}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Ketua Tim --}}
                                    @if($item->ketuaTim)
                                        <div class="rounded px-2 py-1 border" style="background:#f8f9fa; font-size:0.72rem; line-height:1.4;">
                                            <div class="fw-semibold text-muted" style="font-size:0.68rem; text-transform:uppercase; letter-spacing:.03em;">
                                                <i class="mdi mdi-tag-outline me-1"></i>Ketua Tim
                                            </div>
                                            <div class="text-dark fw-bold" style="word-break:break-word;">
                                                <i class="mdi mdi-account-tie-outline me-1"></i>{{ $item->ketuaTim->nama ?? '-' }}{{ $item->ketuaTim->nip ? ' - NIP: ' . $item->ketuaTim->nip : '' }}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Auditor --}}
                                    @if(!empty($item->auditor) && (is_array($item->auditor) ? count($item->auditor) > 0 : true))
                                        @php $auditors = is_array($item->auditor) ? $item->auditor : [$item->auditor]; @endphp
                                        @foreach($auditors as $aud)
                                            <div class="rounded px-2 py-1 border" style="background:#f8f9fa; font-size:0.72rem; line-height:1.4;">
                                                <div class="fw-semibold text-muted" style="font-size:0.68rem; text-transform:uppercase; letter-spacing:.03em;">
                                                    <i class="mdi mdi-tag-outline me-1"></i>Auditor
                                                </div>
                                                <div class="text-dark fw-bold" style="word-break:break-word;">
                                                    <i class="mdi mdi-account-outline me-1"></i>{{ $aud }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if(!$item->koordinator && !$item->ketuaTim && empty($item->auditor))
                                        <span class="text-muted" style="font-size:.8rem;">-</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Ruang Lingkup --}}
                            <td>
                                @if(is_array($item->ruang_lingkup) && count($item->ruang_lingkup) > 0)
                                    <div style="max-width:180px;">
                                        @foreach($item->ruang_lingkup as $scope)
                                            <span class="scope-item">{{ $scope }}</span>
                                        @endforeach
                                    </div>
                                @elseif(!empty($item->ruang_lingkup))
                                    <span class="scope-item">{{ $item->ruang_lingkup }}</span>
                                @else
                                    <span class="text-muted" style="font-size:.8rem;">-</span>
                                @endif
                            </td>

                            {{-- Periode --}}
                            <td>
                                @if($item->periode_audit)
                                    <span class="periode-badge">{{ $item->periode_audit }}</span>
                                @else
                                    <span class="text-muted" style="font-size:.8rem;">-</span>
                                @endif
                            </td>

                            {{-- Tanggal Audit --}}
                            <td>
                                <div class="tgl-audit">
                                    <div class="tgl-range">
                                        <span class="tgl-start">
                                            <i class="mdi mdi-calendar-start me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_audit_mulai)->format('d M Y') }}
                                        </span>
                                        <span class="tgl-sep">&rsaquo;&rsaquo;</span>
                                        <span class="tgl-end">
                                            <i class="mdi mdi-calendar-end me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_audit_sampai)->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td>
                                <div class="action-wrap">
                                    <a href="{{ route('audit.perencanaan.edit', $item->id) }}"
                                       class="btn-act btn-act-edit"
                                       title="Edit Surat Tugas">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <button type="button"
                                        class="btn-act btn-act-delete"
                                        onclick="deleteData({{ $item->id }})"
                                        title="Hapus Surat Tugas">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                    <form id="delete-form-{{ $item->id }}"
                                          action="{{ route('audit.perencanaan.destroy', $item->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="mdi mdi-clipboard-text-off-outline"></i>
                                    <p class="mb-0 fw-semibold">Belum ada data perencanaan audit</p>
                                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah Surat Tugas</strong> untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== Modal Notifikasi Surat Tugas ===== --}}
<div class="modal fade" id="modalSuratTugas" tabindex="-1" aria-labelledby="modalSuratTugasLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="background: linear-gradient(135deg,#1a3a5c,#2d6a9f); color: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(26,58,92,0.35);">
            <div class="modal-body text-center p-5">
                <div style="width:64px;height:64px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="mdi mdi-check-circle" style="font-size:2rem;color:#6ee7b7;"></i>
                </div>
                <h5 class="mb-1" style="font-weight:700; font-size:1.1rem;">Surat Tugas Berhasil Dibuat</h5>
                <p style="opacity:.75; font-size:.85rem; margin-bottom:20px;">Nomor surat tugas yang diterbitkan:</p>
                <div class="mb-4" style="font-size:1.1rem; font-weight:700; background:rgba(255,255,255,.12); border-radius:12px; padding:14px 20px; letter-spacing:.5px;">
                    {{ session('nomor') ?? '001.STG/SPI.01.02/SPI-PCN/2025' }}
                </div>
                <button type="button"
                    class="btn"
                    style="background:#f59e0b; color:#fff; min-width:120px; border-radius:10px; font-weight:600; padding:10px 24px; border:none;"
                    data-bs-dismiss="modal">
                    <i class="mdi mdi-check me-1"></i> OK
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Tampilkan modal jika ada session success DAN session nomor
    @if(session('success') && session('nomor'))
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var modal = new bootstrap.Modal(document.getElementById('modalSuratTugas'));
                modal.show();
            }, 500);
        });
    @endif

    // Fungsi untuk delete dengan SweetAlert
    function deleteData(id) {
        Swal.fire({
            title: 'Hapus Surat Tugas?',
            html: '<p style="color:#6b7280;font-size:.9rem;">Data perencanaan audit yang dihapus <strong style="color:#dc2626;">tidak dapat dikembalikan</strong>.</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="mdi mdi-delete me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary me-2',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
