@extends('layouts.vertical', ['title' => 'Dashboard Analitik Audit'])

@section('css')
    <style>
        body {
            background-color: #f8fafc;
        }

        .kpi-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .kpi-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .kpi-icon {
            font-size: 20px;
        }

        .kpi-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .kpi-label {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.5rem;
        }

        .chart-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
        }

        .chart-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.25rem 0;
        }

        .chart-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        .echart-container {
            width: 100%;
            height: 350px;
        }

        .echart-container-tall {
            width: 100%;
            height: 420px;
        }

        .bg-grad-primary {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
        }

        .bg-grad-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #16a34a;
        }

        .bg-grad-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            color: #d97706;
        }

        .bg-grad-info {
            background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);
            color: #0891b2;
        }

        .badge-soft-success {
            background-color: #dcfce7;
            color: #16a34a;
            font-weight: 600;
        }

        .badge-soft-danger {
            background-color: #fee2e2;
            color: #dc2626;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-1" style="font-weight:700; color:#0f172a;">Dashboard Analitik Audit</h3>
                <p class="text-muted">Pantauan komprehensif performa, jadwal, dan temuan audit perusahaan.</p>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="kpi-card" style="padding: 1.25rem 1.5rem;">
                    <form action="{{ url()->current() }}" method="GET" id="filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label kpi-label mb-2" style="font-size:0.75rem;">Mulai Tanggal
                                    Audit</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label kpi-label mb-2" style="font-size:0.75rem;">Sampai Tanggal
                                    Audit</label>
                                <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label kpi-label mb-2" style="font-size:0.75rem;">Divisi / Auditee</label>
                                <select name="divisi_id" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($masterDivisi as $divisi)
                                        <option value="{{ $divisi->id }}" {{ $divisiId == $divisi->id ? 'selected' : '' }}>
                                            {{ $divisi->divisi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label kpi-label mb-2" style="font-size:0.75rem;">Area Kerja</label>
                                <select name="area_id" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($masterArea as $area)
                                        <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>
                                            {{ $area->nama_area }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex">
                                <button type="submit" class="btn btn-primary w-100 me-2"
                                    style="background-color:#2563eb; border:none;">Filter</button>
                                <a href="{{ url()->current() }}" class="btn btn-light">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-value">{{ $totalDirencanakan }}</div>
                            <div class="kpi-label">Rencana Audit</div>
                        </div>
                        <div class="kpi-icon-wrapper bg-grad-primary">
                            <i data-feather="calendar" class="kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-value">{{ $totalTerealisasi }}</div>
                            <div class="kpi-label">Terealisasi</div>
                        </div>
                        <div class="kpi-icon-wrapper bg-grad-success">
                            <i data-feather="check-circle" class="kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-value">{{ $totalTemuan }}</div>
                            <div class="kpi-label">Total Temuan</div>
                        </div>
                        <div class="kpi-icon-wrapper bg-grad-warning">
                            <i data-feather="alert-triangle" class="kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-value">{{ $percentClosed }}%</div>
                            <div class="kpi-label">Penyelesaian TL</div>
                            <div class="mt-2" style="display:flex; flex-wrap:wrap; gap:4px;">
                                <span class="badge badge-soft-success">Closed: {{ $rekomendasiClosed }}</span>
                                <span class="badge badge-soft-warning"
                                    style="background-color:#fef3c7; color:#d97706; font-weight:600;">On Progress:
                                    {{ $rekomendasiOnProgress }}</span>
                                <span class="badge badge-soft-danger">Open: {{ $rekomendasiOpen }}</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper bg-grad-info">
                            <i data-feather="activity" class="kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1 -->
        <div class="row">
            <div class="col-xl-8">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Tren Penyelesaian Rekomendasi Audit</h3>
                        <p class="chart-subtitle">Jumlah rekomendasi audit yang telah selesai ditindaklanjuti sepanjang bulan berjalan
                        </p>
                    </div>
                    <div id="chart-tren" class="echart-container"></div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Status Pelaksanaan</h3>
                        <p class="chart-subtitle">Proporsi seluruh kegiatan audit</p>
                    </div>
                    <div id="chart-status" class="echart-container"></div>
                </div>
            </div>
        </div>

        <!-- Section 2 -->
        <div class="row">
            <div class="col-xl-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Aging Keterlambatan Tindak Lanjut</h3>
                        <p class="chart-subtitle">Klasifikasi rekomendasi yang belum selesai berdasarkan batas waktu target</p>
                    </div>
                    <div id="chart-aging" class="echart-container"></div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Status Tindak Lanjut per Auditee</h3>
                        <p class="chart-subtitle">Akumulasi penyelesaian temuan pada auditee dengan isu terbuka (open) terbanyak</p>
                    </div>
                    <div id="chart-tl" class="echart-container"></div>
                </div>
            </div>
        </div>

        <!-- Section 3 & 4 -->
        <div class="row">
            <div class="col-xl-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Sebaran Temuan per Divisi/Cabang</h3>
                        <p class="chart-subtitle">Frekuensi temuan yang diidentifikasi di setiap divisi</p>
                    </div>
                    <div id="chart-divisi" class="echart-container"></div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Top Klasifikasi Risiko</h3>
                        <p class="chart-subtitle">Kode risiko yang paling dominan muncul</p>
                    </div>
                    <div id="chart-risiko" class="echart-container"></div>
                </div>
            </div>
        </div>

        <!-- Heatmap -->
        <div class="row">
            <div class="col-xl-12">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Peta Kerentanan: Divisi vs Kode Risiko</h3>
                        <p class="chart-subtitle">Intensitas temuan untuk mengidentifikasi area yang paling rentan</p>
                    </div>
                    <div id="chart-heatmap" class="echart-container-tall"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal: Aging Detail Drill-Down --}}
    <div id="aging-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9999;backdrop-filter:blur(4px);" onclick="closeAgingModal()"></div>
    <div id="aging-modal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:10000;width:min(900px,95vw);max-height:85vh;background:#fff;border-radius:16px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);overflow:hidden;font-family:'Inter',sans-serif;">
        <div id="aging-modal-header" style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f1f5f9;">
            <div>
                <h3 id="aging-modal-title" style="margin:0;font-size:16px;font-weight:700;color:#0f172a;"></h3>
                <p id="aging-modal-subtitle" style="margin:4px 0 0;font-size:12px;color:#64748b;"></p>
            </div>
            <button onclick="closeAgingModal()" style="border:none;background:#f1f5f9;border-radius:8px;width:32px;height:32px;cursor:pointer;font-size:16px;color:#64748b;display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>
        <div style="padding:0 24px 8px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px;">
            <input id="aging-modal-search" type="text" placeholder="Cari divisi atau area..." oninput="filterAgingTable()" style="margin:12px 0;border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;color:#334155;outline:none;width:260px;"/>
        </div>
        <div style="overflow-y:auto;max-height:calc(85vh - 160px);">
            <table id="aging-modal-table" style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8fafc;position:sticky;top:0;">
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #e2e8f0;">Divisi</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #e2e8f0;">Area</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #e2e8f0;">No. Surat Tugas</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #e2e8f0;">Status</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #e2e8f0;">Target</th>
                        <th style="padding:12px 16px;text-align:right;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #e2e8f0;">Terlambat</th>
                    </tr>
                </thead>
                <tbody id="aging-modal-body">
                    <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Universal styles
            const fontFamily = 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif';
            const tooltipConfig = {
                backgroundColor: '#ffffff',
                borderColor: '#e2e8f0',
                textStyle: { color: '#0f172a', fontSize: 13, fontFamily: fontFamily },
                padding: 12,
                boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
            };

            // 1. Tren Penyelesaian Audit (Line Chart)
            var trenBulan = @json($trenBulan);
            var trenSelesai = @json($trenSelesai);
            
            var trenChart = echarts.init(document.getElementById('chart-tren'));
            trenChart.setOption({
                tooltip: { 
                    trigger: 'axis',
                    ...tooltipConfig
                },
                grid: { left: '3%', right: '4%', bottom: '3%', top: '10%', containLabel: true },
                xAxis: { 
                    type: 'category', 
                    boundaryGap: false, 
                    data: trenBulan,
                    axisLine: { lineStyle: { color: '#cbd5e1' } },
                    axisLabel: { fontFamily: fontFamily, color: '#64748b' }
                },
                yAxis: { 
                    type: 'value',
                    splitLine: { lineStyle: { type: 'dashed', color: '#f1f5f9' } },
                    axisLabel: { fontFamily: fontFamily, color: '#64748b' }
                },
                series: [
                    {
                        name: 'Rekomendasi Selesai',
                        type: 'line',
                        data: trenSelesai,
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 8,
                        itemStyle: { color: '#3b82f6' },
                        lineStyle: { width: 3 },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(59, 130, 246, 0.3)' },
                                { offset: 1, color: 'rgba(59, 130, 246, 0.05)' }
                            ])
                        }
                    }
                ]
            });
            
            // 1b. Aging Keterlambatan Tindak Lanjut (Bar Chart)
            var agingCategories = @json($agingCategories);
            var agingData = @json($agingData);
            
            var agingColors = ['#10b981', '#facc15', '#fb923c', '#ef4444', '#7f1d1d'];
            var agingDetails = @json($agingDetails);
            var agingDetailKeys = @json($agingCategories);
            
            var coloredAgingData = agingData.map((val, index) => {
                return {
                    value: val,
                    itemStyle: { color: agingColors[index] || '#cbd5e1' }
                };
            });
            
            var agingChart = echarts.init(document.getElementById('chart-aging'));
            agingChart.setOption({
                tooltip: { 
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' },
                    ...tooltipConfig,
                    formatter: function(params) {
                        var p = params[0];
                        var bucket = agingDetailKeys[p.dataIndex];
                        var details = agingDetails[bucket] || [];
                        var color = agingColors[p.dataIndex] || '#94a3b8';

                        var html = `<div style="font-weight:700;font-size:13px;margin-bottom:6px;padding-bottom:5px;border-bottom:1px solid #e2e8f0;">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${color};margin-right:5px;"></span>
                            ${bucket} (${p.value})
                        </div>`;

                        if (details.length === 0) {
                            html += `<div style="color:#94a3b8;font-size:12px;">Tidak ada data</div>`;
                        } else {
                            html += `<table style="font-size:11px;border-collapse:collapse;width:100%;">
                                <tr style="color:#94a3b8;">
                                    <th style="text-align:left;padding:2px 6px 2px 0;font-weight:500;">Divisi</th>
                                    <th style="text-align:left;padding:2px 6px 2px 0;font-weight:500;">Area</th>
                                    <th style="text-align:right;padding:2px 0;font-weight:500;">Terlambat</th>
                                </tr>`;
                            details.forEach(d => {
                                var daysColor = d.days_late > 90 ? '#7f1d1d' : d.days_late > 60 ? '#ef4444' : d.days_late > 30 ? '#fb923c' : d.days_late > 0 ? '#facc15' : '#10b981';
                                var daysText = d.days_late > 0 ? `${d.days_late} hari` : 'Sesuai target';
                                html += `<tr>
                                    <td style="padding:3px 6px 3px 0;color:#1e293b;font-weight:500;">${d.divisi}</td>
                                    <td style="padding:3px 6px 3px 0;color:#475569;">${d.unit}</td>
                                    <td style="padding:3px 0;text-align:right;color:${daysColor};font-weight:600;">${daysText}</td>
                                </tr>`;
                            });
                            html += `</table>`;
                            if (p.value > details.length) {
                                html += `<div style="color:#94a3b8;font-size:11px;margin-top:4px;">+${p.value - details.length} lainnya</div>`;
                            }
                        }
                        return html;
                    }
                },
                grid: { left: '3%', right: '4%', bottom: '3%', top: '5%', containLabel: true },
                xAxis: { 
                    type: 'category', 
                    data: agingCategories,
                    axisLabel: { fontFamily: fontFamily, color: '#64748b' }
                },
                yAxis: { 
                    type: 'value',
                    splitLine: { lineStyle: { type: 'dashed', color: '#f1f5f9' } },
                    axisLabel: { fontFamily: fontFamily, color: '#64748b' }
                },
                series: [
                    {
                        name: 'Jumlah Rekomendasi',
                        type: 'bar',
                        data: coloredAgingData,
                        barWidth: '50%',
                        itemStyle: { borderRadius: [4, 4, 0, 0] },
                        label: {
                            show: true,
                            position: 'top',
                            color: '#475569',
                            fontWeight: '600',
                            fontSize: 12,
                            formatter: function(p) { return p.value > 0 ? p.value : ''; }
                        }
                    }
                ]
            });

            // 2. Status Pelaksanaan (Donut)
            var statusCounts = @json($statusCounts);
            var statusData = [
                { value: statusCounts['selesai'] || 0, name: 'Selesai', itemStyle: { color: '#10b981' } },
                { value: statusCounts['on progress'] || 0, name: 'On Progress', itemStyle: { color: '#f59e0b' } },
                { value: statusCounts['belum'] || 0, name: 'Belum', itemStyle: { color: '#ef4444' } }
            ].filter(d => d.value > 0);

            var statusChart = echarts.init(document.getElementById('chart-status'));
            statusChart.setOption({
                tooltip: { trigger: 'item', ...tooltipConfig },
                legend: { top: 'bottom', icon: 'circle', itemGap: 15 },
                series: [
                    {
                        name: 'Status',
                        type: 'pie',
                        radius: ['45%', '75%'],
                        center: ['50%', '45%'],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 6,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: { 
                            show: true, 
                            position: 'inside',
                            formatter: '{c}', // Hanya menampilkan angka
                            color: '#ffffff',
                            fontSize: 16,
                            fontWeight: 'bold',
                            textShadowColor: 'rgba(0,0,0,0.2)',
                            textShadowBlur: 3
                        },
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.2)'
                            }
                        },
                        labelLine: { show: false },
                        data: statusData.length ? statusData : [{ value: 0, name: 'Tidak ada data', itemStyle: { color: '#cbd5e1' } }]
                    }
                ]
            });

            // 3. Status Tindak Lanjut (Stacked Bar)
            var tlChart = echarts.init(document.getElementById('chart-tl'));
            tlChart.setOption({
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' }, ...tooltipConfig },
                legend: { top: 'bottom', icon: 'circle' },
                grid: { left: '3%', right: '4%', bottom: '15%', top: '5%', containLabel: true },
                xAxis: { type: 'category', data: @json($stackedCategories), axisLabel: { fontFamily: fontFamily, interval: 0, rotate: 15 } },
                yAxis: { type: 'value' },
                series: [
                    { name: 'Closed', type: 'bar', stack: 'total', data: @json($stackedClosed), itemStyle: { color: '#10b981' } },
                    { name: 'On Progress', type: 'bar', stack: 'total', data: @json($stackedProgress), itemStyle: { color: '#f59e0b' } },
                    { name: 'Open', type: 'bar', stack: 'total', data: @json($stackedOpen), itemStyle: { color: '#ef4444', borderRadius: [4, 4, 0, 0] } }
                ]
            });

            // 4. Sebaran Divisi (Bar)
            var divisiChart = echarts.init(document.getElementById('chart-divisi'));
            divisiChart.setOption({
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' }, ...tooltipConfig },
                grid: { left: '3%', right: '10%', bottom: '5%', top: '5%', containLabel: true },
                xAxis: { type: 'value' },
                yAxis: { type: 'category', data: @json($divisiCategories), axisLabel: { fontFamily: fontFamily } },
                series: [
                    {
                        name: 'Total Temuan',
                        type: 'bar',
                        data: @json($divisiData),
                        itemStyle: { color: '#6366f1', borderRadius: [0, 4, 4, 0] },
                        label: { show: true, position: 'right', color: '#64748b' }
                    }
                ]
            });

            // 5. Klasifikasi Risiko (Bar)
            var riskCategories = @json($riskCategories);
            var riskDescriptions = @json($riskDescriptions);
            var riskData = @json($riskData);
            var risikoChart = echarts.init(document.getElementById('chart-risiko'));
            risikoChart.setOption({
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' },
                    ...tooltipConfig,
                    formatter: function (params) {
                        var dataIndex = params[0].dataIndex;
                        var fullDesc = riskDescriptions[dataIndex] || riskCategories[dataIndex];
                        var kode = riskCategories[dataIndex];
                        var count = params[0].value;
                        return `<b>${kode}</b><br/><div style="max-width:300px; white-space:normal; font-size:12px; margin:4px 0;">${fullDesc}</div>Total: <b>${count} Temuan</b>`;
                    }
                },
                grid: { left: '3%', right: '4%', bottom: '5%', top: '5%', containLabel: true },
                xAxis: { type: 'category', data: riskCategories, axisLabel: { fontFamily: fontFamily, interval: 0 } },
                yAxis: { type: 'value' },
                series: [
                    {
                        name: 'Frekuensi',
                        type: 'bar',
                        barWidth: '50%',
                        data: riskData,
                        itemStyle: { color: '#8b5cf6', borderRadius: [4, 4, 0, 0] },
                        label: { show: true, position: 'top', color: '#64748b' }
                    }
                ]
            });

            // 6. Heatmap Risiko
            var heatmapData = @json($heatmapData);
            var heatmapDivisis = @json($heatmapDivisiLabels);
            var heatmapRisks = @json($heatmapRisks);

            var heatmapChart = echarts.init(document.getElementById('chart-heatmap'));
            heatmapChart.setOption({
                tooltip: {
                    position: 'top',
                    ...tooltipConfig,
                    formatter: function (params) {
                        var divisi = heatmapDivisis[params.value[1]];
                        var kode = heatmapRisks[params.value[0]];
                        var count = params.value[2];
                        var desc = params.value[3] || kode;
                        return `<b>${divisi}</b><br/><b>${kode}</b><br/><div style="max-width:300px; white-space:normal; font-size:12px; margin:4px 0;">${desc}</div>Total: <b>${count} Temuan</b>`;
                    }
                },
                grid: { top: '5%', bottom: '15%', left: '15%', right: '5%' },
                xAxis: { type: 'category', data: heatmapRisks, splitArea: { show: true } },
                yAxis: { type: 'category', data: heatmapDivisis, splitArea: { show: true } },
                visualMap: {
                    min: 0,
                    max: Math.max(1, ...heatmapData.map(d => d[2])),
                    calculable: true,
                    orient: 'horizontal',
                    left: 'center',
                    bottom: '0%',
                    dimension: 2,
                    inRange: {
                        color: ['#f8fafc', '#fecaca', '#ef4444', '#991b1b'] // Sangat modern (Red palette)
                    }
                },
                series: [{
                    name: 'Temuan',
                    type: 'heatmap',
                    data: heatmapData,
                    label: { show: true, color: '#000' },
                    emphasis: {
                        itemStyle: { shadowBlur: 10, shadowColor: 'rgba(0, 0, 0, 0.5)' }
                    }
                }]
            });

            // Resize all charts on window resize
            window.addEventListener('resize', function () {
                trenChart.resize();
                agingChart.resize();
                statusChart.resize();
                tlChart.resize();
                divisiChart.resize();
                risikoChart.resize();
                heatmapChart.resize();
            });

            // === Click handler: Aging bar -> open detail modal ===
            agingChart.on('click', function(params) {
                openAgingModal(agingDetailKeys[params.dataIndex], params.value, agingColors[params.dataIndex]);
            });
            agingChart.on('mouseover', function() { document.getElementById('chart-aging').style.cursor = 'pointer'; });
            agingChart.on('mouseout',  function() { document.getElementById('chart-aging').style.cursor = 'default'; });
        });

        // =====================================================
        // Aging Modal Functions
        // =====================================================
        var agingModalAllRows = [];

        function openAgingModal(bucket, total, color) {
            var bucketColors = { 'Sesuai Target':'#10b981','< 30 Hari':'#facc15','31-60 Hari':'#fb923c','61-90 Hari':'#ef4444','> 90 Hari':'#7f1d1d' };
            var c = bucketColors[bucket] || color || '#3b82f6';
            document.getElementById('aging-modal-title').innerHTML =
                '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:' + c + ';margin-right:8px;"></span>' + bucket;
            document.getElementById('aging-modal-subtitle').textContent = total + ' rekomendasi belum selesai dalam kategori ini';
            document.getElementById('aging-modal-header').style.borderLeft = '4px solid ' + c;
            document.getElementById('aging-modal-body').innerHTML = '<tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;">Memuat data...</td></tr>';
            document.getElementById('aging-modal-search').value = '';
            document.getElementById('aging-modal-overlay').style.display = 'block';
            document.getElementById('aging-modal').style.display = 'block';
            document.body.style.overflow = 'hidden';

            fetch('{{ route("audit.dashboard.aging-detail") }}?bucket=' + encodeURIComponent(bucket))
                .then(res => res.json())
                .then(json => { agingModalAllRows = json.data; renderAgingRows(agingModalAllRows, c); })
                .catch(() => {
                    document.getElementById('aging-modal-body').innerHTML = '<tr><td colspan="6" style="padding:40px;text-align:center;color:#ef4444;">Gagal memuat data.</td></tr>';
                });
        }

        function renderAgingRows(rows, color) {
            var statusBadge = {
                'open':        '<span style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;">Open</span>',
                'on_progress': '<span style="background:#fef9c3;color:#ca8a04;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;">On Progress</span>',
            };
            if (!rows || rows.length === 0) {
                document.getElementById('aging-modal-body').innerHTML = '<tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;">Tidak ada data.</td></tr>';
                return;
            }
            var html = '';
            rows.forEach(function(r, i) {
                var daysColor = r.days_late > 90 ? '#7f1d1d' : r.days_late > 60 ? '#dc2626' : r.days_late > 30 ? '#ea580c' : r.days_late > 0 ? '#ca8a04' : '#16a34a';
                var daysText  = r.days_late > 0 ? r.days_late + ' hari' : 'Sesuai target';
                html += '<tr style="background:' + (i%2===0?'#fff':'#f8fafc') + ';border-bottom:1px solid #f1f5f9;">';
                html += '<td style="padding:11px 16px;font-weight:600;color:#1e293b;">' + r.divisi + '</td>';
                html += '<td style="padding:11px 16px;color:#475569;">' + r.unit + '</td>';
                html += '<td style="padding:11px 16px;color:#64748b;font-size:12px;">' + (r.nomor_surat_tugas||'-') + '</td>';
                html += '<td style="padding:11px 16px;">' + (statusBadge[r.status] || r.status) + '</td>';
                html += '<td style="padding:11px 16px;color:#64748b;font-size:12px;">' + r.target + '</td>';
                html += '<td style="padding:11px 16px;text-align:right;font-weight:700;color:' + daysColor + ';">' + daysText + '</td>';
                html += '</tr>';
            });
            document.getElementById('aging-modal-body').innerHTML = html;
        }

        function filterAgingTable() {
            var keyword = document.getElementById('aging-modal-search').value.toLowerCase();
            var filtered = agingModalAllRows.filter(function(r) {
                return r.divisi.toLowerCase().includes(keyword) || r.unit.toLowerCase().includes(keyword) || (r.nomor_surat_tugas||'').toLowerCase().includes(keyword);
            });
            renderAgingRows(filtered, null);
        }

        function closeAgingModal() {
            document.getElementById('aging-modal-overlay').style.display = 'none';
            document.getElementById('aging-modal').style.display = 'none';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeAgingModal(); });
    </script>
@endsection