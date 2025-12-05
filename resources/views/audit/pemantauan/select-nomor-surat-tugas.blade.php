@extends('layouts.vertical', ['title' => 'Pilih Nomor Surat Tugas'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="mdi mdi-file-document-outline me-2"></i>
                            Pilih Nomor Surat Tugas
                        </h4>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Silakan pilih nomor surat tugas untuk melihat pemantauan hasil audit.
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Cari</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Cari nomor surat tugas atau nomor LHA/LHK..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="jenis_audit" class="form-label">Jenis Audit</label>
                                    <select name="jenis_audit" id="jenis_audit" class="form-select">
                                        <option value="">Semua Jenis</option>
                                        @foreach($jenisAuditList as $jenis)
                                            <option value="{{ $jenis }}" {{ request('jenis_audit') == $jenis ? 'selected' : '' }}>
                                                {{ $jenis }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="mdi mdi-magnify me-1"></i>Cari
                                    </button>
                                    <a href="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-refresh me-1"></i>Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($nomorSuratTugasList->count() > 0)
                        <div class="mb-2">
                            <small class="text-muted">
                                Menampilkan <strong>{{ $nomorSuratTugasList->count() }}</strong> nomor surat tugas
                            </small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Surat Tugas</th>
                                        <th>Jenis Audit</th>
                                        <th>Nomor LHA/LHK</th>
                                        <th>Jumlah Rekomendasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nomorSuratTugasList as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item['nomor_surat_tugas'] }}</strong>
                                        </td>
                                        <td>{{ $item['jenis_audit'] }}</td>
                                        <td>
                                            @if($item['nomor_lha_lhk'])
                                                {{ $item['nomor_lha_lhk'] }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $item['count_rekomendasi'] }} rekomendasi</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('audit.pemantauan.index', ['nomor_surat_tugas' => $item['nomor_surat_tugas']]) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="mdi mdi-eye me-1"></i>Lihat Pemantauan
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert-outline me-2"></i>
                            @if(request('search') || request('jenis_audit'))
                                Tidak ada nomor surat tugas yang sesuai dengan filter yang dipilih.
                                <a href="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="alert-link">Reset filter</a>
                            @else
                                Belum ada nomor surat tugas yang memiliki rekomendasi.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




