@extends('layouts.vertical', ['title' => 'Rekapitulasi Aktivitas Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
        'node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'
     ])
     <style>
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stat-card.primary { border-left-color: #537AEF; }
        .stat-card.success { border-left-color: #10b981; }
        .stat-card.warning { border-left-color: #f59e0b; }
        .stat-card.danger { border-left-color: #ef4444; }
        .stat-card.info { border-left-color: #3b82f6; }
        .stat-card.purple { border-left-color: #8b5cf6; }
        .stat-card.teal { border-left-color: #14b8a6; }
        .stat-card.orange { border-left-color: #f97316; }
        
        .chart-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: box-shadow 0.3s ease;
        }
        .chart-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .chart-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }
     </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Rekapitulasi Aktivitas Audit & Konsultasi</h4>
            <p class="text-muted">Dashboard lengkap untuk monitoring seluruh aktivitas audit yang sudah selesai maupun sedang berlangsung</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-0 d-flex align-items-center" action="">
                    <label for="tahun" class="me-2 mb-0 fw-semibold">Filter Tahun:</label>
                    <select name="tahun" id="tahun" class="form-control me-2" style="max-width:150px;">
                        @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-filter"></i> Tampilkan
                    </button>
                    <a href="{{ route('audit.rekapitulasi-aktivitas.index') }}" class="btn btn-secondary ms-2">
                        <i class="mdi mdi-refresh"></i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-normal mb-2">Total PKA</h6>
                        <h3 class="mt-0 mb-0 text-primary">{{ number_format($totalSummary['total_pka']) }}</h3>
                        <small class="text-muted">Program Kerja Audit</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            <i class="mdi mdi-file-document-multiple font-24 text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-normal mb-2">Total Perencanaan</h6>
                        <h3 class="mt-0 mb-0 text-success">{{ number_format($totalSummary['total_perencanaan']) }}</h3>
                        <small class="text-muted">Surat Tugas</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-success rounded-circle">
                            <i class="mdi mdi-clipboard-text font-24 text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-normal mb-2">Total Aktivitas</h6>
                        <h3 class="mt-0 mb-0 text-warning">{{ number_format(
                            $totalSummary['total_entry_meeting'] + 
                            $totalSummary['total_walkthrough'] + 
                            $totalSummary['total_tod'] + 
                            $totalSummary['total_toe'] + 
                            $totalSummary['total_exit'] + 
                            $totalSummary['total_pelaporan']
                        ) }}</h3>
                        <small class="text-muted">Semua Aktivitas</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-warning rounded-circle">
                            <i class="mdi mdi-chart-line font-24 text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-normal mb-2">Total Pelaporan</h6>
                        <h3 class="mt-0 mb-0 text-info">{{ number_format($totalSummary['total_pelaporan']) }}</h3>
                        <small class="text-muted">Laporan Hasil Audit</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-info rounded-circle">
                            <i class="mdi mdi-file-document-edit font-24 text-info"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1: Pie Chart & Donut Chart -->
<div class="row mb-4">
    <div class="col-xl-6 mb-3">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-pie text-primary"></i> Status PKA
                </h5>
                <div id="pie_chart_pka_status" class="apex-charts"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 mb-3">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-donut text-success"></i> Aktivitas per Jenis
                </h5>
                <div id="donut_chart_aktivitas" class="apex-charts"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2: Line Chart & Bar Chart Approval -->
<div class="row mb-4">
    <div class="col-xl-8 mb-3">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-line text-info"></i> Trend Aktivitas per Bulan ({{ $selectedYear }})
                </h5>
                <div id="line_chart_bulanan" class="apex-charts"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 mb-3">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-bar text-warning"></i> Status Approval
                </h5>
                <div id="bar_chart_approval" class="apex-charts"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 3: Top Auditee -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-bar text-danger"></i> Top 10 Auditee dengan Aktivitas Terbanyak
                </h5>
                <div id="bar_chart_auditee" class="apex-charts"></div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Summary Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-table-large"></i> Ringkasan Detail Aktivitas
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Aktivitas</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Rejected</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Entry Meeting</strong></td>
                                <td class="text-center">{{ number_format($totalSummary['total_entry_meeting']) }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ \App\Models\EntryMeeting::where('status_approval', 'approved')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-warning">{{ \App\Models\EntryMeeting::where('status_approval', 'pending')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ \App\Models\EntryMeeting::where('status_approval', 'rejected')->count() }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Walkthrough Audit</strong></td>
                                <td class="text-center">{{ number_format($totalSummary['total_walkthrough']) }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ \App\Models\WalkthroughAudit::where('status_approval', 'approved')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-warning">{{ \App\Models\WalkthroughAudit::where('status_approval', 'pending')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ \App\Models\WalkthroughAudit::where('status_approval', 'rejected')->count() }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>TOD BPM Audit</strong></td>
                                <td class="text-center">{{ number_format($totalSummary['total_tod']) }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ \App\Models\TodBpmAudit::where('status_approval', 'approved')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-warning">{{ \App\Models\TodBpmAudit::where('status_approval', 'pending')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ \App\Models\TodBpmAudit::where('status_approval', 'rejected')->count() }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>TOE Audit</strong></td>
                                <td class="text-center">{{ number_format($totalSummary['total_toe']) }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ \App\Models\ToeAudit::where('status_approval', 'approved')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-warning">{{ \App\Models\ToeAudit::where('status_approval', 'pending')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ \App\Models\ToeAudit::where('status_approval', 'rejected')->count() }}</span></td>
                            </tr>
                            @php
                                $exitApproved = \App\Models\ExitMeetingUpload::where('approve', true)->count();
                                $exitPending = \App\Models\ExitMeetingUpload::where('approve', false)
                                    ->where(function($query) {
                                        $query->where('status_approval_undangan', '!=', 'rejected')
                                              ->where('status_approval_absensi', '!=', 'rejected');
                                    })->count();
                                $exitRejected = \App\Models\ExitMeetingUpload::where(function($query) {
                                        $query->where('status_approval_undangan', 'rejected')
                                              ->orWhere('status_approval_absensi', 'rejected');
                                    })->count();
                            @endphp
                            <tr>
                                <td><strong>Exit Meeting</strong></td>
                                <td class="text-center">{{ number_format($totalSummary['total_exit']) }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $exitApproved }}</span></td>
                                <td class="text-center"><span class="badge bg-warning">{{ $exitPending }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ $exitRejected }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Pelaporan Hasil Audit</strong></td>
                                <td class="text-center">{{ number_format($totalSummary['total_pelaporan']) }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ \App\Models\Models\Audit\PelaporanHasilAudit::where('status_approval', 'approved')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-warning">{{ \App\Models\Models\Audit\PelaporanHasilAudit::where('status_approval', 'pending')->count() }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ \App\Models\Models\Audit\PelaporanHasilAudit::where('status_approval', 'rejected')->count() }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // 1. Pie Chart - Status PKA
    var pieOptions = {
        series: [
            {{ $pkaStatusData['Selesai'] }},
            {{ $pkaStatusData['Sedang Berlangsung'] }},
            {{ $pkaStatusData['Belum Dimulai'] }},
            {{ $pkaStatusData['Terlambat'] }}
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

    // 2. Donut Chart - Aktivitas per Jenis
    var donutOptions = {
        series: [
            {{ $aktivitasData['Entry Meeting'] }},
            {{ $aktivitasData['Walkthrough Audit'] }},
            {{ $aktivitasData['TOD BPM Audit'] }},
            {{ $aktivitasData['TOE Audit'] }},
            {{ $aktivitasData['Exit Meeting'] }},
            {{ $aktivitasData['Pelaporan Hasil Audit'] }}
        ],
        chart: {
            type: 'donut',
            height: 350,
        },
        labels: ['Entry Meeting', 'Walkthrough Audit', 'TOD BPM Audit', 'TOE Audit', 'Exit Meeting', 'Pelaporan Hasil Audit'],
        colors: ['#537AEF', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6'],
        legend: {
            position: 'bottom',
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return Math.round(val) + "%";
            },
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 600,
                        },
                        value: {
                            show: true,
                            fontSize: '20px',
                            fontWeight: 700,
                            formatter: function(val) {
                                return val;
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Aktivitas',
                            fontSize: '14px',
                            fontWeight: 600,
                            formatter: function() {
                                return {{ array_sum($aktivitasData) }};
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' aktivitas';
                }
            }
        },
    };
    var donutChart = new ApexCharts(document.querySelector("#donut_chart_aktivitas"), donutOptions);
    donutChart.render();

    // 3. Line Chart - Trend Bulanan
    var lineOptions = {
        series: [
            {
                name: 'Entry Meeting',
                data: [
                    @foreach($months as $month)
                    {{ $bulananData[$month]['Entry Meeting'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            },
            {
                name: 'Walkthrough',
                data: [
                    @foreach($months as $month)
                    {{ $bulananData[$month]['Walkthrough'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            },
            {
                name: 'TOD BPM',
                data: [
                    @foreach($months as $month)
                    {{ $bulananData[$month]['TOD BPM'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            },
            {
                name: 'TOE',
                data: [
                    @foreach($months as $month)
                    {{ $bulananData[$month]['TOE'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            },
            {
                name: 'Exit Meeting',
                data: [
                    @foreach($months as $month)
                    {{ $bulananData[$month]['Exit Meeting'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            },
            {
                name: 'Pelaporan',
                data: [
                    @foreach($months as $month)
                    {{ $bulananData[$month]['Pelaporan'] }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            }
        ],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        colors: ['#537AEF', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 5,
            hover: {
                size: 7
            }
        },
        xaxis: {
            categories: {!! json_encode($months) !!}
        },
        yaxis: {
            title: {
                text: 'Jumlah Aktivitas'
            }
        },
        legend: {
            position: 'top',
        },
        tooltip: {
            shared: true,
            intersect: false,
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
    };
    var lineChart = new ApexCharts(document.querySelector("#line_chart_bulanan"), lineOptions);
    lineChart.render();

    // 4. Bar Chart - Status Approval
    var barApprovalOptions = {
        series: [{
            name: 'Jumlah',
            data: [
                {{ $approvalData['Approved'] }},
                {{ $approvalData['Pending'] }},
                {{ $approvalData['Rejected'] }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 350,
        },
        colors: ['#10b981', '#f59e0b', '#ef4444'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 5,
                dataLabels: {
                    position: 'top',
                },
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val;
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: ['Approved', 'Pending', 'Rejected'],
        },
        yaxis: {
            title: {
                text: 'Jumlah'
            }
        },
        fill: {
            opacity: 0.8
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " aktivitas";
                }
            }
        },
    };
    var barApprovalChart = new ApexCharts(document.querySelector("#bar_chart_approval"), barApprovalOptions);
    barApprovalChart.render();

    // 5. Bar Chart - Top Auditee
    var auditeeLabels = {!! json_encode($auditeeData->pluck('name')->toArray()) !!};
    var auditeeData = {!! json_encode($auditeeData->pluck('total')->toArray()) !!};
    
    var barAuditeeOptions = {
        series: [{
            name: 'Jumlah Aktivitas',
            data: auditeeData
        }],
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: true
            }
        },
        colors: ['#537AEF'],
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 5,
                dataLabels: {
                    position: 'right',
                },
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + " aktivitas";
            },
        },
        xaxis: {
            categories: auditeeLabels,
            title: {
                text: 'Jumlah Aktivitas'
            }
        },
        yaxis: {
            title: {
                text: 'Auditee'
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " aktivitas";
                }
            }
        },
    };
    var barAuditeeChart = new ApexCharts(document.querySelector("#bar_chart_auditee"), barAuditeeOptions);
    barAuditeeChart.render();
</script>
@endsection
