@extends('layouts.vertical', ['title' => 'Dashboard Monitoring Rencana PKPT'])

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
            <h4 class="page-title">Dashboard Monitoring Rencana PKPT</h4>
            <p class="text-muted">Monitoring rencana PKPT berdasarkan data Program Kerja Audit (PKA)</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-3 d-flex align-items-center" action="">
                    <label for="bulan" class="me-2 mb-0">Filter Bulan (Tanggal PKA):</label>
                    <input type="month" name="bulan" id="bulan" class="form-control me-2" style="max-width:200px;" value="{{ request('bulan') }}">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('audit.dashboard-rencana-pkpt.index') }}" class="btn btn-secondary ms-2">
                        <i class="mdi mdi-refresh"></i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    @php
        $totalPka = count($dashboardData);
        $totalAuditor = collect($dashboardData)->sum('jumlah_auditor');
        $totalRisiko = collect($dashboardData)->sum('jumlah_risiko');
        $totalMilestone = collect($dashboardData)->sum('jumlah_milestone');
        
        $statusSelesai = collect($dashboardData)->where('status', 'Selesai')->count();
        $statusBerlangsung = collect($dashboardData)->where('status', 'Sedang Berlangsung')->count();
        $statusBelum = collect($dashboardData)->where('status', 'Belum Dimulai')->count();
        $statusTerlambat = collect($dashboardData)->where('status', 'Terlambat')->count();
    @endphp
    
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-muted fw-normal mt-0" title="Total PKA">Total PKA</h5>
                        <h3 class="mt-3 mb-3">{{ $totalPka }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-primary rounded">
                            <i class="mdi mdi-file-document-multiple font-20 text-primary"></i>
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
                        <h5 class="text-muted fw-normal mt-0" title="Total Auditor">Total Auditor</h5>
                        <h3 class="mt-3 mb-3 text-info">{{ $totalAuditor }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-info rounded">
                            <i class="mdi mdi-account-group font-20 text-info"></i>
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
                        <h5 class="text-muted fw-normal mt-0" title="Total Risiko">Total Risiko</h5>
                        <h3 class="mt-3 mb-3 text-warning">{{ $totalRisiko }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-warning rounded">
                            <i class="mdi mdi-alert-circle font-20 text-warning"></i>
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
                        <h5 class="text-muted fw-normal mt-0" title="Total Milestone">Total Milestone</h5>
                        <h3 class="mt-3 mb-3 text-success">{{ $totalMilestone }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-success rounded">
                            <i class="mdi mdi-check-circle-multiple font-20 text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-muted fw-normal mt-0">Selesai</h5>
                        <h3 class="mt-3 mb-3 text-success">{{ $statusSelesai }}</h3>
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
                        <h5 class="text-muted fw-normal mt-0">Sedang Berlangsung</h5>
                        <h3 class="mt-3 mb-3 text-warning">{{ $statusBerlangsung }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-warning rounded">
                            <i class="mdi mdi-clock-outline font-20 text-warning"></i>
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
                        <h5 class="text-muted fw-normal mt-0">Belum Dimulai</h5>
                        <h3 class="mt-3 mb-3 text-secondary">{{ $statusBelum }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-secondary rounded">
                            <i class="mdi mdi-pause-circle font-20 text-secondary"></i>
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
                        <h5 class="text-muted fw-normal mt-0">Terlambat</h5>
                        <h3 class="mt-3 mb-3 text-danger">{{ $statusTerlambat }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-danger rounded">
                            <i class="mdi mdi-alert font-20 text-danger"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pie Chart Status PKA -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="mdi mdi-chart-pie text-primary"></i> Status PKA
                </h5>
                <div id="pie_chart_pka_status" class="apex-charts"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="mdi mdi-information-outline text-info"></i> Ringkasan Status
                </h5>
                <div class="row mt-3">
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-success rounded-circle">
                                    <i class="mdi mdi-check-circle font-20 text-success"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0 text-success">{{ $statusSelesai }}</h4>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-warning rounded-circle">
                                    <i class="mdi mdi-clock-outline font-20 text-warning"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0 text-warning">{{ $statusBerlangsung }}</h4>
                                <small class="text-muted">Sedang Berlangsung</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-secondary rounded-circle">
                                    <i class="mdi mdi-pause-circle font-20 text-secondary"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0 text-secondary">{{ $statusBelum }}</h4>
                                <small class="text-muted">Belum Dimulai</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-danger rounded-circle">
                                    <i class="mdi mdi-alert font-20 text-danger"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0 text-danger">{{ $statusTerlambat }}</h4>
                                <small class="text-muted">Terlambat</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Dashboard Monitoring Rencana PKPT -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Data Monitoring Rencana PKPT</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No PKA</th>
                                <th>Tanggal PKA</th>
                                <th>Surat Tugas</th>
                                <th>Auditee</th>
                                <th>Jenis Audit</th>
                                <th>Jml Auditor</th>
                                <th>Jml Risiko</th>
                                <th>Jml Milestone</th>
                                <th>Rencana Mulai</th>
                                <th>Rencana Selesai</th>
                                <th>Realisasi Mulai</th>
                                <th>Realisasi Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dashboardData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['no_pka'] }}</strong></td>
                                    <td>{{ $data['tanggal_pka'] }}</td>
                                    <td>{{ $data['surat_tugas'] }}</td>
                                    <td><strong>{{ $data['auditee'] }}</strong></td>
                                    <td>{{ $data['jenis_audit'] }}</td>
                                    <td class="text-center">{{ $data['jumlah_auditor'] }}</td>
                                    <td class="text-center">{{ $data['jumlah_risiko'] }}</td>
                                    <td class="text-center">{{ $data['jumlah_milestone'] }}</td>
                                    <td>{{ $data['rencana_mulai'] }}</td>
                                    <td>{{ $data['rencana_selesai'] }}</td>
                                    <td>{{ $data['realisasi_mulai'] }}</td>
                                    <td>{{ $data['realisasi_selesai'] }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            switch(strtolower($data['status'])) {
                                                case 'selesai':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'sedang berlangsung':
                                                    $statusClass = 'bg-warning';
                                                    break;
                                                case 'belum dimulai':
                                                    $statusClass = 'bg-secondary';
                                                    break;
                                                case 'terlambat':
                                                    $statusClass = 'bg-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $data['status'] }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('audit.pka.show', $data['id']) }}" class="btn btn-info btn-sm" title="Detail">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite(['resources/js/pages/datatable.init.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Pie Chart - Status PKA
        var pieOptions = {
            series: [
                {{ $statusSelesai }},
                {{ $statusBerlangsung }},
                {{ $statusBelum }},
                {{ $statusTerlambat }}
            ],
            chart: {
                type: 'pie',
                height: 350,
            },
            labels: ['Selesai', 'Sedang Berlangsung', 'Belum Dimulai', 'Terlambat'],
            colors: ['#10b981', '#f59e0b', '#6b7280', '#ef4444'],
            legend: {
                position: 'bottom',
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return opts.w.globals.labels[opts.seriesIndex] + "\n" + Math.round(val) + "%";
                },
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' PKA';
                    }
                }
            },
        };
        var pieChart = new ApexCharts(document.querySelector("#pie_chart_pka_status"), pieOptions);
        pieChart.render();
    </script>
@endsection
