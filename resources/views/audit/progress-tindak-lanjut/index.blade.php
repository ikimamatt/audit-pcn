@extends('layouts.vertical', ['title' => 'Progress Tindak Lanjut Rekomendasi Audit'])

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
        
        .progress-bar-custom {
            height: 25px;
            border-radius: 5px;
        }
     </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Progress Tindak Lanjut Rekomendasi Audit</h4>
            <p class="text-muted">Dashboard monitoring progress tindak lanjut rekomendasi audit yang sudah selesai maupun sedang berlangsung</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-0" action="">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="tahun" class="form-label fw-semibold">Tahun:</label>
                            <select name="tahun" id="tahun" class="form-select">
                                @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label fw-semibold">Status:</label>
                            <select name="status" id="status" class="form-select">
                                <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="open" {{ $selectedStatus == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="on_progress" {{ $selectedStatus == 'on_progress' ? 'selected' : '' }}>On Progress</option>
                                <option value="closed" {{ $selectedStatus == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="auditee_id" class="form-label fw-semibold">Auditee:</label>
                            <select name="auditee_id" id="auditee_id" class="form-select">
                                <option value="">Semua Auditee</option>
                                @foreach($auditees as $auditee)
                                    @php
                                        $direktorat = $auditee->direktorat ?? '';
                                        $divisiCabang = $auditee->divisi_cabang ?? '';
                                        $divisi = $auditee->divisi ?? '';
                                        $auditeeName = '';
                                        if (!empty($direktorat) || !empty($divisiCabang)) {
                                            $auditeeName = trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                                            $auditeeName = trim($auditeeName, '- ');
                                        } elseif (!empty($divisi)) {
                                            $auditeeName = $divisi;
                                        }
                                    @endphp
                                    <option value="{{ $auditee->id }}" {{ $selectedAuditee == $auditee->id ? 'selected' : '' }}>
                                        {{ $auditeeName ?: 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                            <a href="{{ route('audit.progress-tindak-lanjut.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
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
                        <h6 class="text-muted fw-normal mb-2">Total Rekomendasi</h6>
                        <h3 class="mt-0 mb-0 text-primary">{{ number_format($totalRekomendasi) }}</h3>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            <i class="mdi mdi-clipboard-list font-24 text-primary"></i>
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
                        <h6 class="text-muted fw-normal mb-2">Open</h6>
                        <h3 class="mt-0 mb-0 text-warning">{{ number_format($statusOpen) }}</h3>
                        <small class="text-muted">{{ $totalRekomendasi > 0 ? round(($statusOpen / $totalRekomendasi) * 100, 1) : 0 }}%</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-warning rounded-circle">
                            <i class="mdi mdi-folder-open font-24 text-warning"></i>
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
                        <h6 class="text-muted fw-normal mb-2">On Progress</h6>
                        <h3 class="mt-0 mb-0 text-info">{{ number_format($statusOnProgress) }}</h3>
                        <small class="text-muted">{{ $totalRekomendasi > 0 ? round(($statusOnProgress / $totalRekomendasi) * 100, 1) : 0 }}%</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-info rounded-circle">
                            <i class="mdi mdi-clock-outline font-24 text-info"></i>
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
                        <h6 class="text-muted fw-normal mb-2">Closed</h6>
                        <h3 class="mt-0 mb-0 text-success">{{ number_format($statusClosed) }}</h3>
                        <small class="text-muted">{{ $completionRate }}% Completion</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-success rounded-circle">
                            <i class="mdi mdi-check-circle font-24 text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="row mb-4">
    <div class="col-xl-6 col-md-6 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-normal mb-2">On Time</h6>
                        <h3 class="mt-0 mb-0 text-success">{{ number_format($onTimeCount) }}</h3>
                        <small class="text-muted">Rekomendasi selesai tepat waktu</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-success rounded-circle">
                            <i class="mdi mdi-check-all font-24 text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6 mb-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-normal mb-2">Overdue</h6>
                        <h3 class="mt-0 mb-0 text-danger">{{ number_format($overdueCount) }}</h3>
                        <small class="text-muted">Rekomendasi terlambat</small>
                    </div>
                    <div class="avatar-lg">
                        <span class="avatar-title bg-soft-danger rounded-circle">
                            <i class="mdi mdi-alert-circle font-24 text-danger"></i>
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
                    <i class="mdi mdi-chart-pie text-primary"></i> Distribusi Status
                </h5>
                <div id="pie_chart_status" class="apex-charts"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 mb-3">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-donut text-success"></i> Completion Rate
                </h5>
                <div id="donut_chart_completion" class="apex-charts"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2: Line Chart & Bar Chart -->
<div class="row mb-4">
    <div class="col-xl-8 mb-3">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-line text-info"></i> Trend Progress per Bulan ({{ $selectedYear }})
                </h5>
                <div id="line_chart_bulanan" class="apex-charts"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 mb-3">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">
                    <i class="mdi mdi-chart-bar text-warning"></i> On Time vs Overdue
                </h5>
                <div id="bar_chart_timing" class="apex-charts"></div>
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
                    <i class="mdi mdi-chart-bar text-danger"></i> Top 10 Auditee - Progress Tindak Lanjut
                </h5>
                <div id="bar_chart_auditee" class="apex-charts"></div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-table-large"></i> Detail Progress Tindak Lanjut
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dt-responsive nowrap" id="responsive-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Rekomendasi</th>
                                <th>Auditee</th>
                                <th>Target Waktu</th>
                                <th>Real Waktu</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Update Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ Str::limit($data['rekomendasi'], 50) }}</strong>
                                    </td>
                                    <td>{{ $data['auditee'] }}</td>
                                    <td>{{ $data['target_waktu'] }}</td>
                                    <td>{{ $data['real_waktu'] }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            $statusLabel = '';
                                            switch($data['status']) {
                                                case 'closed':
                                                    $statusClass = 'bg-success';
                                                    $statusLabel = 'Closed';
                                                    break;
                                                case 'on_progress':
                                                    $statusClass = 'bg-info';
                                                    $statusLabel = 'On Progress';
                                                    break;
                                                case 'open':
                                                    $statusClass = 'bg-warning';
                                                    $statusLabel = 'Open';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-secondary';
                                                    $statusLabel = ucfirst($data['status']);
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td>
                                        <div class="progress progress-bar-custom">
                                            <div class="progress-bar 
                                                @if($data['progress'] == 100) bg-success
                                                @elseif($data['progress'] >= 50) bg-info
                                                @else bg-warning
                                                @endif" 
                                                role="progressbar" 
                                                style="width: {{ $data['progress'] }}%"
                                                aria-valuenow="{{ $data['progress'] }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ $data['progress'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $data['latest_update'] }}</td>
                                    <td>
                                        <a href="{{ route('audit.penutup-lha-rekomendasi.show', $data['id']) }}" 
                                           class="btn btn-info btn-sm" title="Detail">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data rekomendasi.</td>
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
    @vite(['resources/js/pages/datatable.init.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // 1. Pie Chart - Status Distribution
        var pieOptions = {
            series: [
                {{ $statusData['Open'] }},
                {{ $statusData['On Progress'] }},
                {{ $statusData['Closed'] }}
            ],
            chart: {
                type: 'pie',
                height: 350,
            },
            labels: ['Open', 'On Progress', 'Closed'],
            colors: ['#f59e0b', '#3b82f6', '#10b981'],
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
                        return val + ' rekomendasi';
                    }
                }
            },
        };
        var pieChart = new ApexCharts(document.querySelector("#pie_chart_status"), pieOptions);
        pieChart.render();

        // 2. Donut Chart - Completion Rate
        var donutOptions = {
            series: [{{ $statusClosed }}, {{ $statusOpen + $statusOnProgress }}],
            chart: {
                type: 'donut',
                height: 350,
            },
            labels: ['Closed', 'Belum Selesai'],
            colors: ['#10b981', '#f59e0b'],
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
                                fontSize: '24px',
                                fontWeight: 700,
                                formatter: function(val) {
                                    return val + '%';
                                }
                            },
                            total: {
                                show: true,
                                label: 'Completion Rate',
                                fontSize: '14px',
                                fontWeight: 600,
                                formatter: function() {
                                    return '{{ $completionRate }}%';
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' rekomendasi';
                    }
                }
            },
        };
        var donutChart = new ApexCharts(document.querySelector("#donut_chart_completion"), donutOptions);
        donutChart.render();

        // 3. Line Chart - Trend Bulanan
        var lineOptions = {
            series: [
                {
                    name: 'Open',
                    data: [
                        @foreach($months as $month)
                        {{ $bulananData[$month]['open'] }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ]
                },
                {
                    name: 'On Progress',
                    data: [
                        @foreach($months as $month)
                        {{ $bulananData[$month]['on_progress'] }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ]
                },
                {
                    name: 'Closed',
                    data: [
                        @foreach($months as $month)
                        {{ $bulananData[$month]['closed'] }}{{ !$loop->last ? ',' : '' }}
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
            colors: ['#f59e0b', '#3b82f6', '#10b981'],
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
                    text: 'Jumlah Rekomendasi'
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

        // 4. Bar Chart - On Time vs Overdue
        var barTimingOptions = {
            series: [{
                name: 'Jumlah',
                data: [
                    {{ $onTimeCount }},
                    {{ $overdueCount }}
                ]
            }],
            chart: {
                type: 'bar',
                height: 350,
            },
            colors: ['#10b981', '#ef4444'],
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
                categories: ['On Time', 'Overdue'],
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
                        return val + " rekomendasi";
                    }
                }
            },
        };
        var barTimingChart = new ApexCharts(document.querySelector("#bar_chart_timing"), barTimingOptions);
        barTimingChart.render();

        // 5. Bar Chart - Top Auditee
        var auditeeLabels = {!! json_encode($auditeeProgress->pluck('name')->toArray()) !!};
        var auditeeOpen = {!! json_encode($auditeeProgress->pluck('open')->toArray()) !!};
        var auditeeOnProgress = {!! json_encode($auditeeProgress->pluck('on_progress')->toArray()) !!};
        var auditeeClosed = {!! json_encode($auditeeProgress->pluck('closed')->toArray()) !!};
        
        var barAuditeeOptions = {
            series: [
                {
                    name: 'Open',
                    data: auditeeOpen
                },
                {
                    name: 'On Progress',
                    data: auditeeOnProgress
                },
                {
                    name: 'Closed',
                    data: auditeeClosed
                }
            ],
            chart: {
                type: 'bar',
                height: 400,
                stacked: true,
                toolbar: {
                    show: true
                }
            },
            colors: ['#f59e0b', '#3b82f6', '#10b981'],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 5,
                    dataLabels: {
                        total: {
                            enabled: true,
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                fontSize: '13px',
                                fontWeight: 900
                            }
                        }
                    },
                },
            },
            dataLabels: {
                enabled: true,
            },
            xaxis: {
                categories: auditeeLabels,
                title: {
                    text: 'Jumlah Rekomendasi'
                }
            },
            yaxis: {
                title: {
                    text: 'Auditee'
                }
            },
            legend: {
                position: 'top',
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " rekomendasi";
                    }
                }
            },
        };
        var barAuditeeChart = new ApexCharts(document.querySelector("#bar_chart_auditee"), barAuditeeOptions);
        barAuditeeChart.render();
    </script>
@endsection
