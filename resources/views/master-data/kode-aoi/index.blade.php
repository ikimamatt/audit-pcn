@extends('layouts.vertical', ['title' => 'Master Kode AOI'])

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
                <i class="mdi mdi-clipboard-text-search-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Master Kode AOI</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Master Data &rsaquo; Kode AOI
            </div>
        </div>
        <a href="{{ route('master.kode-aoi.create') }}" class="btn-add-em">
            <i class="mdi mdi-plus-circle"></i> Tambah Kode AOI
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
            <h5 class="mb-0">Daftar Kode AOI</h5>
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
                        <th>Indikator Pengawasan</th>
                        <th>Kode Area of Improvement</th>
                        <th>Deskripsi Area of Improvement</th>
                        <th style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td><span class="row-num">{{ $index + 1 }}</span></td>
                            <td>{{ $item->indikator_pengawasan }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.8rem; font-weight:600; border-radius:6px;">
                                    {{ $item->kode_area_of_improvement }}
                                </span>
                            </td>
                            <td>{{ $item->deskripsi_area_of_improvement }}</td>
                            <td>
                                <div class="action-wrap">
                                    <button type="button" class="btn-act btn-act-edit btn-edit-modal" 
                                        data-id="{{ $item->id }}"
                                        data-indikator="{{ $item->indikator_pengawasan }}"
                                        data-kode="{{ $item->kode_area_of_improvement }}"
                                        data-deskripsi="{{ $item->deskripsi_area_of_improvement }}"
                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                        title="Edit Kode AOI">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('master.kode-aoi.destroy', $item->id) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn-act btn-act-delete"
                                                onclick="deleteData('{{ $item->id }}')"
                                                title="Hapus Kode AOI">
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
                    <i class="mdi mdi-clipboard-text-search-outline"></i>
                    <p class="mb-0 fw-semibold">Belum ada data Kode AOI</p>
                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah Kode AOI</strong> untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('master.kode-aoi.store') }}" method="POST" autocomplete="off">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="createModalLabel">Tambah Kode AOI</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_indikator_pengawasan" class="form-label">Indikator Pengawasan <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('indikator_pengawasan') is-invalid @enderror" id="create_indikator_pengawasan" name="indikator_pengawasan" value="{{ old('indikator_pengawasan') }}" required>
            @error('indikator_pengawasan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="create_kode_area_of_improvement" class="form-label">Kode Area of Improvement <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('kode_area_of_improvement') is-invalid @enderror" id="create_kode_area_of_improvement" name="kode_area_of_improvement" value="{{ old('kode_area_of_improvement') }}" required>
            @error('kode_area_of_improvement')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="create_deskripsi_area_of_improvement" class="form-label">Deskripsi Area of Improvement <span class="text-danger">*</span></label>
            <textarea class="form-control @error('deskripsi_area_of_improvement') is-invalid @enderror" id="create_deskripsi_area_of_improvement" name="deskripsi_area_of_improvement" rows="3" required>{{ old('deskripsi_area_of_improvement') }}</textarea>
            @error('deskripsi_area_of_improvement')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm d-none" id="createLoading" role="status" aria-hidden="true"></span>
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Global -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editForm" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="modal-header text-white" style="background:#1a3a5c;">
          <h5 class="modal-title" id="editModalLabel">Edit Kode AOI</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_indikator_pengawasan" class="form-label">Indikator Pengawasan <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_indikator_pengawasan" name="indikator_pengawasan" required>
          </div>
          <div class="mb-3">
            <label for="edit_kode_area_of_improvement" class="form-label">Kode Area of Improvement <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_kode_area_of_improvement" name="kode_area_of_improvement" required>
          </div>
          <div class="mb-3">
            <label for="edit_deskripsi_area_of_improvement" class="form-label">Deskripsi Area of Improvement <span class="text-danger">*</span></label>
            <textarea class="form-control" id="edit_deskripsi_area_of_improvement" name="deskripsi_area_of_improvement" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn text-white" style="background:#1a3a5c;">
            <span class="spinner-border spinner-border-sm d-none" id="editLoading" role="status" aria-hidden="true"></span>
            Simpan Perubahan
          </button>
        </div>
      </form>
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

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit-modal').forEach(function (button) {
      button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const indikator = this.getAttribute('data-indikator');
        const kode = this.getAttribute('data-kode');
        const deskripsi = this.getAttribute('data-deskripsi');
        const inputIndikator = document.getElementById('edit_indikator_pengawasan');
        const inputKode = document.getElementById('edit_kode_area_of_improvement');
        const inputDeskripsi = document.getElementById('edit_deskripsi_area_of_improvement');
        if (!inputIndikator || !inputKode || !inputDeskripsi) {
          alert('Input modal edit tidak ditemukan!');
          return;
        }
        inputIndikator.value = indikator;
        inputKode.value = kode;
        inputDeskripsi.value = deskripsi;
        const form = document.getElementById('editForm');
        form.action = '/master/kode-aoi/' + id;
        console.log('Set modal edit:', {id, indikator, kode, deskripsi});
      });
    });
    // Loading spinner on submit
    document.querySelectorAll('form').forEach(function(form) {
      form.addEventListener('submit', function() {
        var btn = form.querySelector('button[type="submit"]');
        var spinner = btn.querySelector('.spinner-border');
        if (spinner) spinner.classList.remove('d-none');
        btn.setAttribute('disabled', 'disabled');
      });
    });
  });
</script>
@endpush
