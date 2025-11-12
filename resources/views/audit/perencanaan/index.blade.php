@extends('layouts.vertical', ['title' => 'Perencanaan Audit'])

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
                    <li class="breadcrumb-item active">Perencanaan Audit</li>
                </ol>
            </div>
            <h4 class="page-title">Perencanaan Audit</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-5">
                        <a href="{{ route('audit.perencanaan.create') }}" class="btn btn-primary mb-2">
                            <i class="mdi mdi-plus-circle me-2"></i> Tambah Perencanaan Audit
                        </a>
                    </div>
                </div>

                @include('components.alert')

                <div class="table-responsive">
                    <table id="responsive-datatable" class="table table-centered w-100 dt-responsive nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat Tugas</th>
                                <th>Tanggal Surat Tugas</th>
                                <th>Jenis Audit</th>
                                <th>Auditee</th>
                                <th>Nama Auditor</th>
                                <th>Ruang Lingkup</th>
                                <th>Periode Audit</th>
                                <th>Tanggal Audit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->nomor_surat_tugas }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_surat_tugas)->format('d/m/Y') }}</td>
                                    <td>{{ $item->jenis_audit }}</td>
                                    <td>{{ $item->auditee->divisi ?? '-' }}</td>
                                    <td>
                                        @if(is_array($item->auditor))
                                            @foreach($item->auditor as $auditor)
                                                <div class="badge bg-primary mb-1">{{ $auditor }}</div>
                                            @endforeach
                                        @else
                                            <span class="badge bg-primary">{{ $item->auditor ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_array($item->ruang_lingkup))
                                            @foreach($item->ruang_lingkup as $scope)
                                                <div>{{ $scope }}</div>
                                            @endforeach
                                        @else
                                            {{ $item->ruang_lingkup ?? '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $item->periode_audit }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_audit_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_audit_sampai)->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('audit.perencanaan.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteData({{ $item->id }})">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('audit.perencanaan.destroy', $item->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notifikasi Surat Tugas -->
<div class="modal fade" id="modalSuratTugas" tabindex="-1" aria-labelledby="modalSuratTugasLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #4478c7; color: #fff; border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <h5 class="mb-3" style="font-weight:600;">SURAT TUGAS</h5>
                <div class="mb-4" style="font-size:1.2rem;">
                    {{ session('nomor') ?? '001.STG/SPI.01.02/SPI-PCN/2025' }}
                </div>
                <button type="button" class="btn" style="background:#f47c2b; color:#fff; min-width:100px;" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
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
