@extends('layouts.vertical', ['title' => 'Master Kode AOI'])

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
                    <li class="breadcrumb-item"><a href="{{ route('master.kode-aoi.index') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Kode AOI</li>
                </ol>
            </div>
            <h4 class="page-title">Master Kode AOI</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-5">
                        <a href="{{ route('master.kode-aoi.create') }}" class="btn btn-primary mb-2">
                            <i class="mdi mdi-plus-circle me-2"></i> Tambah Kode AOI
                        </a>
                    </div>
                </div>

                @include('components.alert')
                <div class="table-responsive">
                    <table id="responsive-datatable" class="table table-centered w-100 dt-responsive nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Indikator Pengawasan</th>
                                <th>Kode Area of Improvement</th>
                                <th>Deskripsi Area of Improvement</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->indikator_pengawasan }}</td>
                                    <td>{{ $item->kode_area_of_improvement }}</td>
                                    <td>{{ $item->deskripsi_area_of_improvement }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning btn-edit-modal" 
                                            data-id="{{ $item->id }}"
                                            data-indikator="{{ $item->indikator_pengawasan }}"
                                            data-kode="{{ $item->kode_area_of_improvement }}"
                                            data-deskripsi="{{ $item->deskripsi_area_of_improvement }}"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            >Edit</button>
                                        <form action="{{ route('master.kode-aoi.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
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
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="editModalLabel">Edit Kode AOI</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
          <button type="submit" class="btn btn-warning">
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
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
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
        });
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
        console.log('Value after set:', {
          indikator: inputIndikator.value,
          kode: inputKode.value,
          deskripsi: inputDeskripsi.value
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
