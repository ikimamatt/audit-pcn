@extends('layouts.vertical', ['title' => 'Realisasi Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
        'node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'
     ])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-info">
                    <i class="mdi mdi-cog"></i> Kelola Exit Meeting
                </a>
            </div>
            <h4 class="page-title">Realisasi Audit (Berdasarkan Exit Meeting Approved)</h4>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-3 d-flex align-items-center flex-wrap" action="">
                    <div class="me-3 mb-2">
                        <label for="bulan" class="me-2 mb-0">Filter Bulan:</label>
                        <input type="month" name="bulan" id="bulan" class="form-control" style="max-width:200px;" value="{{ request('bulan') }}">
                    </div>
                    <div class="me-3 mb-2">
                        <label for="status" class="me-2 mb-0">Filter Status:</label>
                        <select name="status" id="status" class="form-control" style="max-width:200px;">
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="me-3 mb-2">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                        <a href="{{ route('audit.realisasi-audit.index') }}" class="btn btn-secondary ms-2">
                            <i class="mdi mdi-refresh"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Divisi/Satuan/Cabang</th>
                                <th>Jenis Audit</th>
                                <th>Total Audit</th>
                                <th>Selesai</th>
                                <th>Sedang Berlangsung</th>
                                <th>Belum Dimulai</th>
                                <th>Progress (%)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupedData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['auditee'] }}</strong></td>
                                    <td>{{ $data['jenis_audit'] }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $data['total_audit'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $data['selesai'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $data['on_progress'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $data['belum_dimulai'] }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $progress = $data['total_audit'] > 0 ? round(($data['selesai'] / $data['total_audit']) * 100, 1) : 0;
                                            $progressClass = $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger');
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                                 style="width: {{ $progress }}%" 
                                                 aria-valuenow="{{ $progress }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $progress }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('audit.realisasi-audit.show', [
                                            'auditee' => str_replace(' ', '_', $data['auditee']), 
                                            'jenis_audit' => str_replace(' ', '_', $data['jenis_audit'])
                                        ]) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="mdi mdi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data realisasi audit yang sudah diapprove.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mt-4">
    @php
        $totalAudit = collect($groupedData)->sum('total_audit');
        $totalSelesai = collect($groupedData)->sum('selesai');
        $totalOnProgress = collect($groupedData)->sum('on_progress');
        $totalBelum = collect($groupedData)->sum('belum_dimulai');
        $overallProgress = $totalAudit > 0 ? round(($totalSelesai / $totalAudit) * 100, 1) : 0;
    @endphp
    
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-muted fw-normal mt-0" title="Total Audit">Total Audit</h5>
                        <h3 class="mt-3 mb-3">{{ $totalAudit }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-primary rounded">
                            <i class="mdi mdi-clipboard-check font-20 text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-muted fw-normal mt-0" title="Selesai">Selesai</h5>
                        <h3 class="mt-3 mb-3 text-success">{{ $totalSelesai }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-success rounded">
                            <i class="mdi mdi-check-circle font-20 text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-muted fw-normal mt-0" title="Sedang Berlangsung">Sedang Berlangsung</h5>
                        <h3 class="mt-3 mb-3 text-warning">{{ $totalOnProgress }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-warning rounded">
                            <i class="mdi mdi-clock font-20 text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-muted fw-normal mt-0" title="Progress Keseluruhan">Progress</h5>
                        <h3 class="mt-3 mb-3 text-primary">{{ $overallProgress }}%</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-primary rounded">
                            <i class="mdi mdi-chart-line font-20 text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite(['resources/js/pages/datatable.init.js'])
@endsection









