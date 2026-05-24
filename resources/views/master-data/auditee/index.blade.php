@extends('layouts.vertical', ['title' => 'Master Auditee'])

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
.btn-add-em {
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
.btn-add-em:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(26,58,92,0.25);
    color: #fff;
    background: #2d6a9f;
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
    flex-shrink: 0;
}
.btn-act:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.15); }
.btn-act-edit     { background: #fef3c7; color: #d97706; }
.btn-act-edit:hover   { background: #fde68a; color: #b45309; }
.btn-act-delete   { background: #fee2e2; color: #dc2626; }
.btn-act-delete:hover { background: #fecaca; color: #b91c1c; }

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
                <i class="mdi mdi-office-building-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Master Auditee</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Master Data &rsaquo; Auditee
            </div>
        </div>
        <a href="{{ route('master.auditee.create') }}" class="btn-add-em">
            <i class="mdi mdi-plus-circle"></i> Tambah Auditee
        </a>
    </div>
</div>

@include('components.alert')

{{-- ===== TABLE ===== --}}
@php $total = $data->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Auditee</h5>
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
                        <th>Divisi/Cabang</th>
                        <th style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td><span class="row-num">{{ $index + 1 }}</span></td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->divisi }}</span>
                            </td>
                            <td>
                                <div class="action-wrap">
                                    <a href="{{ route('master.auditee.edit', $item->id) }}"
                                       class="btn-act btn-act-edit"
                                       title="Edit Auditee">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('master.auditee.destroy', $item->id) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn-act btn-act-delete"
                                                onclick="deleteData({{ $item->id }})"
                                                title="Hapus Auditee">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty state handled below --}}
                    @endforelse
                </tbody>
            </table>
            @if($data->isEmpty())
                <div class="empty-state">
                    <i class="mdi mdi-office-building-outline"></i>
                    <p class="mb-0 fw-semibold">Belum ada data Auditee</p>
                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah Auditee</strong> untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteData(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
