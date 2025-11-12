@extends('layouts.vertical', ['title' => 'Master Kode Risk'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.kode-risk.index') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Kode Risk</li>
                </ol>
            </div>
            <h4 class="page-title">Master Kode Risk</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-5">
                        <a href="#" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="mdi mdi-plus-circle me-2"></i> Tambah Kode Risk
                        </a>
                    </div>
                </div>

                @include('components.alert')
                <div class="table-responsive">
                    <table id="responsive-datatable" class="table table-centered w-100 dt-responsive nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kelompok Risiko</th>
                                <th>Kode Risiko</th>
                                <th>Kelompok Risiko Detail</th>
                                <th>Deskripsi Risiko</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->kelompok_risiko }}</td>
                                    <td>{{ $item->kode_risiko }}</td>
                                    <td>{{ $item->kelompok_risiko_detail }}</td>
                                    <td>{{ $item->deskripsi_risiko }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning btn-edit-modal"
                                            data-id="{{ $item->id }}"
                                            data-kelompok="{{ $item->kelompok_risiko }}"
                                            data-kode="{{ $item->kode_risiko }}"
                                            data-detail="{{ $item->kelompok_risiko_detail }}"
                                            data-deskripsi="{{ $item->deskripsi_risiko }}"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                        >Edit</button>
                                        <form action="{{ route('master.kode-risk.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('master.kode-risk.store') }}" method="POST" autocomplete="off">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="createModalLabel">Tambah Kode Risk</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_kelompok_risiko" class="form-label">Kelompok Risiko <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="create_kelompok_risiko" name="kelompok_risiko" required>
          </div>
          <div class="mb-3">
            <label for="create_kode_risiko" class="form-label">Kode Risiko <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="create_kode_risiko" name="kode_risiko" required>
          </div>
          <div class="mb-3">
            <label for="create_kelompok_risiko_detail" class="form-label">Kelompok Risiko Detail <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="create_kelompok_risiko_detail" name="kelompok_risiko_detail" required>
          </div>
          <div class="mb-3">
            <label for="create_deskripsi_risiko" class="form-label">Deskripsi Risiko <span class="text-danger">*</span></label>
            <textarea class="form-control" id="create_deskripsi_risiko" name="deskripsi_risiko" rows="3" required></textarea>
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
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="editModalLabel">Edit Kode Risk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_kelompok_risiko" class="form-label">Kelompok Risiko <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_kelompok_risiko" name="kelompok_risiko" required>
          </div>
          <div class="mb-3">
            <label for="edit_kode_risiko" class="form-label">Kode Risiko <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_kode_risiko" name="kode_risiko" required>
          </div>
          <div class="mb-3">
            <label for="edit_kelompok_risiko_detail" class="form-label">Kelompok Risiko Detail <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_kelompok_risiko_detail" name="kelompok_risiko_detail" required>
          </div>
          <div class="mb-3">
            <label for="edit_deskripsi_risiko" class="form-label">Deskripsi Risiko <span class="text-danger">*</span></label>
            <textarea class="form-control" id="edit_deskripsi_risiko" name="deskripsi_risiko" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <span class="spinner-border spinner-border-sm d-none" id="editLoading" role="status" aria-hidden="true"></span>
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit-modal').forEach(function (button) {
      button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const kelompok = this.getAttribute('data-kelompok');
        const kode = this.getAttribute('data-kode');
        const detail = this.getAttribute('data-detail');
        const deskripsi = this.getAttribute('data-deskripsi');
        document.getElementById('edit_kelompok_risiko').value = kelompok;
        document.getElementById('edit_kode_risiko').value = kode;
        document.getElementById('edit_kelompok_risiko_detail').value = detail;
        document.getElementById('edit_deskripsi_risiko').value = deskripsi;
        const form = document.getElementById('editForm');
        form.action = '/master/kode-risk/' + id;
      });
    });
    // Konfirmasi hapus dengan SweetAlert
    document.querySelectorAll('.delete-btn').forEach(function(button) {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Konfirmasi Hapus',
          text: 'Apakah Anda yakin ingin menghapus data ini?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, Hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            e.target.closest('form').submit();
          }
        });
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
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
@endsection 