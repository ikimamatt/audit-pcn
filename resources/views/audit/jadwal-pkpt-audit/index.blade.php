@extends('layouts.vertical', ['title' => 'Jadwal PKPT Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
        'node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'
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

/* Auditee chip */
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

/* Jumlah auditor badge */
.badge-auditor-count {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e40af;
    background: #dbeafe;
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
                <i class="mdi mdi-calendar-clock" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Jadwal PKPT Audit</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Jadwal PKPT
            </div>
        </div>
        @canModifyData
        <a href="{{ route('audit.pkpt.create') }}" class="btn-add-pa">
            <i class="mdi mdi-plus-circle"></i> Tambah Jadwal PKPT
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
            <h5 class="mb-0">Daftar Jadwal PKPT Audit</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} Jadwal PKPT
        </span>
    </div>

    <div class="card-body p-0 pt-0">
        <div class="table-responsive">
            <table id="responsive-datatable" class="table table-centered w-100 dt-responsive nowrap mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Auditee</th>
                        <th>Jenis Audit</th>
                        <th>Jumlah Auditor</th>
                        <th>Tanggal Audit</th>
                        <th style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                    <tr>
                        {{-- No --}}
                        <td><span class="row-num">{{ $i+1 }}</span></td>

                        {{-- Auditee --}}
                        <td>
                            <div class="auditee-chip">
                                <span class="dot"></span>
                                {{ $item->auditee ? $item->auditee->divisi : '-' }}
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

                        {{-- Jumlah Auditor --}}
                        <td>
                            <span class="badge-auditor-count">
                                <i class="mdi mdi-account-group-outline me-1"></i>{{ $item->jumlah_auditor }} Orang
                            </span>
                        </td>

                        {{-- Tanggal Audit --}}
                        <td>
                            <div class="tgl-audit">
                                <div class="tgl-range">
                                    <span class="tgl-start">
                                        <i class="mdi mdi-calendar-start me-1"></i>{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}
                                    </span>
                                    <span class="tgl-sep">&rsaquo;&rsaquo;</span>
                                    <span class="tgl-end">
                                        <i class="mdi mdi-calendar-end me-1"></i>{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td>
                            <div class="action-wrap">
                                <a href="{{ route('audit.pkpt.edit', $item->id) }}" class="btn-act btn-act-edit" title="Edit Jadwal PKPT">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <form action="{{ route('audit.pkpt.destroy', $item->id) }}" method="POST" class="delete-form m-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-act btn-act-delete btn-delete-swal" title="Hapus Jadwal PKPT">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="mdi mdi-calendar-remove-outline"></i>
                                <p class="mb-0 fw-semibold">Belum ada data Jadwal PKPT Audit</p>
                                <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah Jadwal PKPT</strong> untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmWithSwal({ title = 'Konfirmasi', text = 'Apakah Anda yakin?', icon = 'question', confirmButtonText = 'Ya', cancelButtonText = 'Batal', onConfirm = null } = {}) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = btn.closest('form');

                Swal.fire({
                    title: 'Hapus Jadwal PKPT?',
                    text: 'Yakin ingin menghapus jadwal PKPT ini?',
                    icon: 'warning',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });


    });
</script>
    @vite([ 'resources/js/pages/datatable.init.js'])
@endsection
