@extends('layouts.vertical', ['title' => 'Master Akses User'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
<style>
/* ===== HERO HEADER ===== */
.em-hero {
    background: #fff;
    border-radius: 16px;
    padding: 24px 28px;
    color: #1a3a5c;
    margin-bottom: 24px;
    border: 1px solid #e8edf5;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.em-hero h2 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 4px;
    letter-spacing: -0.5px;
    color: #1a3a5c;
}
.em-hero .subtitle {
    font-size: 0.85rem;
    color: #6b7a99;
}

/* ===== TABLE CARD ===== */
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

/* No baris */
.row-num {
    font-size: 0.78rem;
    font-weight: 700;
    color: #9ca3af;
    background: #f9fafb;
    border-radius: 6px;
    padding: 3px 8px;
    display: inline-block;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}
.empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; opacity: .4; }
</style>
@endsection

@section('content')

{{-- ===== HERO HEADER ===== --}}
<div class="em-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-shield-account-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Master Akses User</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Master Data &rsaquo; Akses User
            </div>
        </div>
    </div>
</div>

{{-- ===== TABLE ===== --}}
@php $total = $data->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Akses User</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} Data
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="responsive-datatable" class="table table-centered dt-responsive w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nama Akses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td><span class="row-num">{{ $index + 1 }}</span></td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary px-3 py-1.5" style="font-size:0.825rem; font-weight:600; border-radius:6px;">
                                    {{ $item->nama_akses }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty state handled below --}}
                    @endforelse
                </tbody>
            </table>
            @if($data->isEmpty())
                <div class="empty-state">
                    <i class="mdi mdi-shield-account-outline"></i>
                    <p class="mb-0 fw-semibold">Belum ada data Akses User</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
@endsection