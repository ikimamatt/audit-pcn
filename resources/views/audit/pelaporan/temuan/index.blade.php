@extends('layouts.vertical', ['title' => 'Temuan Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
    <style>
        .btn-custom {
            transition: all 0.3s ease;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border-color: #007bff;
            transform: translateY(-1px);
        }
        
        .btn-outline-success:hover {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border-color: #28a745;
            transform: translateY(-1px);
        }
        
        .btn-outline-warning:hover {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            border-color: #ffc107;
            transform: translateY(-1px);
        }
        
        .btn-outline-danger:hover {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border-color: #dc3545;
            transform: translateY(-1px);
        }
        
        .btn-outline-info:hover {
            background: linear-gradient(45deg, #17a2b8, #138496);
            border-color: #17a2b8;
            transform: translateY(-1px);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Audit</a></li>
                    <li class="breadcrumb-item active">Temuan Audit</li>
                </ol>
            </div>
            <h4 class="page-title">
                <i class="mdi mdi-alert-circle-outline me-2"></i>
                Temuan Audit
            </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-table me-2"></i>
                            Data Temuan Audit
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('audit.pelaporan-hasil-audit.create') }}" class="btn btn-primary" style="border-radius: 25px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Tambah Temuan Audit
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    @include('components.alert')
                @endif

                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('audit.pelaporan-hasil-audit.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="pelaporan" class="form-label">Pelaporan</label>
                                <input type="text" name="pelaporan" id="pelaporan" class="form-control" placeholder="Cari pelaporan..." value="{{ request('pelaporan') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="kode_aoi" class="form-label">Kode AOI</label>
                                <input type="text" name="kode_aoi" id="kode_aoi" class="form-control" placeholder="Cari kode AOI..." value="{{ request('kode_aoi') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="kode_risk" class="form-label">Kode Risiko</label>
                                <input type="text" name="kode_risk" id="kode_risk" class="form-control" placeholder="Cari kode risiko..." value="{{ request('kode_risk') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <input type="text" name="tahun" id="tahun" class="form-control" placeholder="Cari tahun..." value="{{ request('tahun') }}">
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; font-weight: 500;">
                                        <i class="mdi mdi-magnify me-2"></i>
                                        Filter Data
                                    </button>
                                    <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-outline-secondary" style="border-radius: 25px; font-weight: 500;">
                                        <i class="mdi mdi-refresh me-2"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelaporan</th>
                                <th>Hasil Temuan</th>
                                <th>Kode AOI</th>
                                <th>Kode Risiko</th>
                                <th>Nomor ISS</th>
                                <th>Tahun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;" title="{{ $item->pelaporanHasilAudit->nomor_lha_lhk ?? '-' }}">
                                            {{ Str::limit($item->pelaporanHasilAudit->nomor_lha_lhk ?? '-', 30) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $item->hasil_temuan }}">
                                            {{ Str::limit($item->hasil_temuan, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;" title="{{ $item->kodeAoi->kode_area_of_improvement ?? '-' }} - {{ $item->kodeAoi->deskripsi_area_of_improvement ?? '-' }}">
                                            {{ Str::limit(($item->kodeAoi->kode_area_of_improvement ?? '-') . ' - ' . ($item->kodeAoi->deskripsi_area_of_improvement ?? '-'), 40) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;" title="{{ $item->kodeRisk->kode_risiko ?? '-' }} - {{ $item->kodeRisk->deskripsi_risiko ?? '-' }}">
                                            {{ Str::limit(($item->kodeRisk->kode_risiko ?? '-') . ' - ' . ($item->kodeRisk->deskripsi_risiko ?? '-'), 40) }}
                                        </div>
                                    </td>
                                    <td>{{ $item->nomor_iss }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->tahun }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            <a href="{{ route('audit.pelaporan-hasil-audit.edit', $item->id) }}" 
                                               class="btn btn-outline-primary btn-sm mb-1 btn-custom" 
                                               title="Edit">
                                                <i class="mdi mdi-pencil me-1"></i>Edit
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm btn-custom" 
                                                    title="Hapus"
                                                    onclick="deleteData({{ $item->id }})">
                                                <i class="mdi mdi-delete me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data temuan audit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

function deleteData(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data temuan audit yang dihapus tidak dapat dikembalikan!",
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

<!-- Hidden Forms -->
@foreach($data as $item)
                            <form id="delete-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.destroy', $item->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection 