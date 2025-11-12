@extends('layouts.vertical', ['title' => 'Realisasi Pelaksanaan Audit'])

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-3 d-flex align-items-center" action="">
                    <label for="bulan" class="me-2 mb-0">Filter Bulan:</label>
                    <input type="month" name="bulan" id="bulan" class="form-control me-2" style="max-width:200px;" value="{{ request('bulan') }}">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </form>
                <h5><span class="badge bg-secondary">DASHBOARD PELAKSANAAN AUDIT</span></h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background:#f7941d;color:#fff;">
                            <tr>
                                <th>NO</th>
                                <th>Divisi/Satuan/Cabang</th>
                                <th>Rencana Audit</th>
                                <th>Realisasi Audit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tabel as $i => $row)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $row->perencanaanAudit->auditee->divisi ?? '-' }}</td>
                                    <td>
                                        @if($row->perencanaanAudit)
                                            {{ \Carbon\Carbon::parse($row->perencanaanAudit->tanggal_audit_mulai)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($row->perencanaanAudit->tanggal_audit_sampai)->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($row->tanggal_mulai)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($row->tanggal_selesai)->translatedFormat('d M Y') }}
                                    </td>
                                    <td>{{ ucfirst($row->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">Tidak ada data realisasi audit.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center pt-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h4 class="text-center mb-4">Realisasi Pelaksanaan Audit s/d {{ $periode ?? date('F Y') }}</h4>
                <div id="pie_realisasi_audit" class="apex-charts"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        chart: {
            type: 'pie',
            height: 380,
        },
        series: [{{ $belum }}, {{ $selesai }}, {{ $onprogress }}],
        labels: ['Belum', 'Selesai', 'on progress'],
        colors: ['#4478c7', '#f47c2b', '#bdbdbd'],
        legend: {
            position: 'bottom',
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.globals.labels[opts.seriesIndex] + "\n" + Math.round(val) + "%";
            },
            style: {
                fontSize: '16px',
                fontWeight: 'bold',
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' data';
                }
            }
        },
        title: {
            text: '',
            align: 'center',
        },
    };
    var chart = new ApexCharts(document.querySelector("#pie_realisasi_audit"), options);
    chart.render();
</script>
@endsection 