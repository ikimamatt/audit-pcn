@extends('layouts.base')

@section('title', 'Dashboard Monitoring Tindak Lanjut')

@section('content')
<div class="container-fluid">
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('any', 'index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard Monitoring Tindak Lanjut</li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard Monitoring Tindak Lanjut</h4>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="bg-secondary text-white p-3 rounded text-center">
                                <h4 class="mb-0 fw-bold">RINCIAN JUMLAH TEMUAN & REKOMENDASI</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <thead>
                                <tr class="table-dark">
                                    <th rowspan="3" class="text-center align-middle" style="width: 5%;">NO.</th>
                                    <th rowspan="3" class="text-center align-middle" style="width: 20%;">OBJEK PEMERIKSAAN</th>
                                    <th colspan="2" class="text-center">JUMLAH TEMUAN DAN REKOMENDASI (*)</th>
                                    <th colspan="2" class="text-center">JUMLAH TINDAK LANJUT (s/d BULAN {{ strtoupper($currentMonthName) }})</th>
                                    <th colspan="2" class="text-center">SISA TEMUAN & REKOMENDASI</th>
                                    <th colspan="24" class="text-center">RINCIAN JUMLAH TEMUAN & REKOMENDASI</th>
                                </tr>
                                <tr class="table-dark">
                                    <th class="text-center">AOI</th>
                                    <th class="text-center">REKOM</th>
                                    <th class="text-center">Target</th>
                                    <th class="text-center">Real</th>
                                    <th class="text-center">Target</th>
                                    <th class="text-center">Real</th>
                                    <th class="text-center month-jan">Jan</th>
                                    <th class="text-center month-jan">Jan</th>
                                    <th class="text-center month-feb">Feb</th>
                                    <th class="text-center month-feb">Feb</th>
                                    <th class="text-center month-mar">Mar</th>
                                    <th class="text-center month-mar">Mar</th>
                                    <th class="text-center month-apr">Apr</th>
                                    <th class="text-center month-apr">Apr</th>
                                    <th class="text-center month-mei">Mei</th>
                                    <th class="text-center month-mei">Mei</th>
                                    <th class="text-center month-jun">Jun</th>
                                    <th class="text-center month-jun">Jun</th>
                                    <th class="text-center month-jul">Jul</th>
                                    <th class="text-center month-jul">Jul</th>
                                    <th class="text-center month-ags">Ags</th>
                                    <th class="text-center month-ags">Ags</th>
                                    <th class="text-center month-sep">Sep</th>
                                    <th class="text-center month-sep">Sep</th>
                                    <th class="text-center month-okt">Okt</th>
                                    <th class="text-center month-okt">Okt</th>
                                    <th class="text-center month-nov">Nov</th>
                                    <th class="text-center month-nov">Nov</th>
                                    <th class="text-center month-des">Des</th>
                                    <th class="text-center month-des">Des</th>
                                </tr>
                                <tr class="table-dark">
                                    <th class="text-center">T</th>
                                    <th class="text-center">R</th>
                                    <th class="text-center">T</th>
                                    <th class="text-center">R</th>
                                    <th class="text-center">T</th>
                                    <th class="text-center">R</th>
                                    <th class="text-center month-jan">T</th>
                                    <th class="text-center month-jan">R</th>
                                    <th class="text-center month-feb">T</th>
                                    <th class="text-center month-feb">R</th>
                                    <th class="text-center month-mar">T</th>
                                    <th class="text-center month-mar">R</th>
                                    <th class="text-center month-apr">T</th>
                                    <th class="text-center month-apr">R</th>
                                    <th class="text-center month-mei">T</th>
                                    <th class="text-center month-mei">R</th>
                                    <th class="text-center month-jun">T</th>
                                    <th class="text-center month-jun">R</th>
                                    <th class="text-center month-jul">T</th>
                                    <th class="text-center month-jul">R</th>
                                    <th class="text-center month-ags">T</th>
                                    <th class="text-center month-ags">R</th>
                                    <th class="text-center month-sep">T</th>
                                    <th class="text-center month-sep">R</th>
                                    <th class="text-center month-okt">T</th>
                                    <th class="text-center month-okt">R</th>
                                    <th class="text-center month-nov">T</th>
                                    <th class="text-center month-nov">R</th>
                                    <th class="text-center month-des">T</th>
                                    <th class="text-center month-des">R</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditeeData as $data)
                                    @if(!$data['is_empty'])
                                    <tr class="{{ $data['no'] % 2 == 0 ? 'table-primary' : 'table-light' }}">
                                        <td class="text-center fw-bold">{{ $data['no'] }}</td>
                                        <td class="fw-bold">{{ $data['objek_pemeriksaan'] }}</td>
                                        <td class="text-center">{{ $data['aoi'] }}</td>
                                        <td class="text-center">{{ $data['rekom'] }}</td>
                                        <td class="text-center">{{ $data['tindak_lanjut_target'] }}</td>
                                        <td class="text-center">{{ $data['tindak_lanjut_real'] }}</td>
                                        <td class="text-center">{{ $data['sisa_target'] }}</td>
                                        <td class="text-center table-warning">{{ $data['sisa_real'] }}</td>
                                        
                                        <!-- Data Bulanan -->
                                        <td class="text-center month-jan">{{ $data['bulanan']['jan']['target'] ?: '' }}</td>
                                        <td class="text-center month-jan">{{ $data['bulanan']['jan']['real'] ?: '' }}</td>
                                        <td class="text-center month-feb">{{ $data['bulanan']['feb']['target'] ?: '' }}</td>
                                        <td class="text-center month-feb">{{ $data['bulanan']['feb']['real'] ?: '' }}</td>
                                        <td class="text-center month-mar">{{ $data['bulanan']['mar']['target'] ?: '' }}</td>
                                        <td class="text-center month-mar">{{ $data['bulanan']['mar']['real'] ?: '' }}</td>
                                        <td class="text-center month-apr">{{ $data['bulanan']['apr']['target'] ?: '' }}</td>
                                        <td class="text-center month-apr">{{ $data['bulanan']['apr']['real'] ?: '' }}</td>
                                        <td class="text-center month-mei">{{ $data['bulanan']['mei']['target'] ?: '' }}</td>
                                        <td class="text-center month-mei">{{ $data['bulanan']['mei']['real'] ?: '' }}</td>
                                        <td class="text-center month-jun">{{ $data['bulanan']['jun']['target'] ?: '' }}</td>
                                        <td class="text-center month-jun">{{ $data['bulanan']['jun']['real'] ?: '' }}</td>
                                        <td class="text-center month-jul">{{ $data['bulanan']['jul']['target'] ?: '' }}</td>
                                        <td class="text-center month-jul">{{ $data['bulanan']['jul']['real'] ?: '' }}</td>
                                        <td class="text-center month-ags">{{ $data['bulanan']['ags']['target'] ?: '' }}</td>
                                        <td class="text-center month-ags">{{ $data['bulanan']['ags']['real'] ?: '' }}</td>
                                        <td class="text-center month-sep">{{ $data['bulanan']['sep']['target'] ?: '' }}</td>
                                        <td class="text-center month-sep">{{ $data['bulanan']['sep']['real'] ?: '' }}</td>
                                        <td class="text-center month-okt">{{ $data['bulanan']['okt']['target'] ?: '' }}</td>
                                        <td class="text-center month-okt">{{ $data['bulanan']['okt']['real'] ?: '' }}</td>
                                        <td class="text-center month-nov">{{ $data['bulanan']['nov']['target'] ?: '' }}</td>
                                        <td class="text-center month-nov">{{ $data['bulanan']['nov']['real'] ?: '' }}</td>
                                        <td class="text-center month-des">{{ $data['bulanan']['des']['target'] ?: '' }}</td>
                                        <td class="text-center month-des">{{ $data['bulanan']['des']['real'] ?: '' }}</td>
                                </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="30" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-information-outline mdi-36px mb-3"></i>
                                                <p class="mb-0">Belum ada data untuk ditampilkan.</p>
                                                <small>Data akan muncul setelah ada perencanaan audit yang approved.</small>
                                            </div>
                                        </td>
                                </tr>
                                @endforelse

                                <!-- Total Baris -->
                                @if(!empty($totalData))
                                <tr class="table-warning">
                                    <td class="text-center fw-bold"></td>
                                    <td class="fw-bold">JUMLAH</td>
                                    <td class="text-center">{{ $totalData['aoi'] }}</td>
                                    <td class="text-center">{{ $totalData['rekom'] }}</td>
                                    <td class="text-center">{{ $totalData['tindak_lanjut_target'] }}</td>
                                    <td class="text-center">{{ $totalData['tindak_lanjut_real'] }}</td>
                                    <td class="text-center">{{ $totalData['sisa_target'] }}</td>
                                    <td class="text-center table-warning">{{ $totalData['sisa_real'] }}</td>
                                    
                                    <!-- Total Bulanan -->
                                    <td class="text-center month-jan">{{ $totalData['bulanan']['jan']['target'] ?: '' }}</td>
                                    <td class="text-center month-jan">{{ $totalData['bulanan']['jan']['real'] ?: '' }}</td>
                                    <td class="text-center month-feb">{{ $totalData['bulanan']['feb']['target'] ?: '' }}</td>
                                    <td class="text-center month-feb">{{ $totalData['bulanan']['feb']['real'] ?: '' }}</td>
                                    <td class="text-center month-mar">{{ $totalData['bulanan']['mar']['target'] ?: '' }}</td>
                                    <td class="text-center month-mar">{{ $totalData['bulanan']['mar']['real'] ?: '' }}</td>
                                    <td class="text-center month-apr">{{ $totalData['bulanan']['apr']['target'] ?: '' }}</td>
                                    <td class="text-center month-apr">{{ $totalData['bulanan']['apr']['real'] ?: '' }}</td>
                                    <td class="text-center month-mei">{{ $totalData['bulanan']['mei']['target'] ?: '' }}</td>
                                    <td class="text-center month-mei">{{ $totalData['bulanan']['mei']['real'] ?: '' }}</td>
                                    <td class="text-center month-jun">{{ $totalData['bulanan']['jun']['target'] ?: '' }}</td>
                                    <td class="text-center month-jun">{{ $totalData['bulanan']['jun']['real'] ?: '' }}</td>
                                    <td class="text-center month-jul">{{ $totalData['bulanan']['jul']['target'] ?: '' }}</td>
                                    <td class="text-center month-jul">{{ $totalData['bulanan']['jul']['real'] ?: '' }}</td>
                                    <td class="text-center month-ags">{{ $totalData['bulanan']['ags']['target'] ?: '' }}</td>
                                    <td class="text-center month-ags">{{ $totalData['bulanan']['ags']['real'] ?: '' }}</td>
                                    <td class="text-center month-sep">{{ $totalData['bulanan']['sep']['target'] ?: '' }}</td>
                                    <td class="text-center month-sep">{{ $totalData['bulanan']['sep']['real'] ?: '' }}</td>
                                    <td class="text-center month-okt">{{ $totalData['bulanan']['okt']['target'] ?: '' }}</td>
                                    <td class="text-center month-okt">{{ $totalData['bulanan']['okt']['real'] ?: '' }}</td>
                                    <td class="text-center month-nov">{{ $totalData['bulanan']['nov']['target'] ?: '' }}</td>
                                    <td class="text-center month-nov">{{ $totalData['bulanan']['nov']['real'] ?: '' }}</td>
                                    <td class="text-center month-des">{{ $totalData['bulanan']['des']['target'] ?: '' }}</td>
                                    <td class="text-center month-des">{{ $totalData['bulanan']['des']['real'] ?: '' }}</td>
                                </tr>

                                <!-- Persentase Baris -->
                                <tr class="table-warning">
                                    <td class="text-center fw-bold"></td>
                                    <td class="fw-bold"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    
                                                                        <!-- Persentase Bulanan -->
                                    @php
                                        $months = [
                                            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'jun' => 6,
                                            'jul' => 7, 'ags' => 8, 'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12
                                        ];
                                        $currentMonth = \Carbon\Carbon::now()->month;
                                    @endphp
                                    
                                    @foreach($months as $monthKey => $monthNumber)
                                        @if($monthNumber <= $currentMonth)
                                            <!-- Gabungkan T dan R dalam satu kolom -->
                                            <td class="text-center {{ $totalData['bulanan'][$monthKey]['target'] > 0 ? ($totalData['bulanan'][$monthKey]['real'] / $totalData['bulanan'][$monthKey]['target'] * 100 >= 100 ? 'table-success' : 'table-warning') : '' }}" colspan="2">
                                                {{ $totalData['bulanan'][$monthKey]['target'] > 0 ? round($totalData['bulanan'][$monthKey]['real'] / $totalData['bulanan'][$monthKey]['target'] * 100) . '%' : '' }}
                                            </td>
                                        @else
                                            <!-- Bulan yang belum tiba - kosong -->
                                            <td class="text-center" colspan="2"></td>
                                        @endif
                                    @endforeach
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Bottom Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="text-center">
                                <h5 class="mb-2">REALISASI KUMULATIF S/D {{ strtoupper($currentMonthName) }} {{ date('Y') }}</h5>
                                <div class="bg-warning text-white p-4 rounded">
                                    <h2 class="mb-0 fw-bold">{{ $realisasiKumulatif }}%</h2>
                                    <small class="text-white-50">
                                        Berdasarkan data bulan Januari - {{ strtoupper($currentMonthName) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-sm td, .table-sm th {
    padding: 0.25rem;
    vertical-align: middle;
}

.table-bordered {
    border: 2px solid #000;
}

.table-bordered td, .table-bordered th {
    border: 1px solid #000;
}

.table-dark {
    background-color: #343a40;
    color: white;
}

.table-primary {
    background-color: #cfe2ff;
}

.table-light {
    background-color: #f8f9fa;
}

.table-success {
    background-color: #d1e7dd;
}

.table-warning {
    background-color: #fff3cd;
}

.bg-warning {
    background-color: #fd7e14 !important;
}

/* Warna untuk setiap bulan */
.month-jan {
    background-color: #e3f2fd !important; /* Light Blue */
    color: #1976d2 !important;
}

.month-feb {
    background-color: #f3e5f5 !important; /* Light Purple */
    color: #7b1fa2 !important;
}

.month-mar {
    background-color: #e8f5e8 !important; /* Light Green */
    color: #388e3c !important;
}

.month-apr {
    background-color: #fff3e0 !important; /* Light Orange */
    color: #f57c00 !important;
}

.month-mei {
    background-color: #fce4ec !important; /* Light Pink */
    color: #c2185b !important;
}

.month-jun {
    background-color: #e0f2f1 !important; /* Light Teal */
    color: #00796b !important;
}

.month-jul {
    background-color: #fff8e1 !important; /* Light Yellow */
    color: #f9a825 !important;
}

.month-ags {
    background-color: #ffebee !important; /* Light Red */
    color: #d32f2f !important;
}

.month-sep {
    background-color: #e8eaf6 !important; /* Light Indigo */
    color: #303f9f !important;
}

.month-okt {
    background-color: #f1f8e9 !important; /* Light Lime */
    color: #689f38 !important;
}

.month-nov {
    background-color: #fdf2e9 !important; /* Light Deep Orange */
    color: #e64a19 !important;
}

.month-des {
    background-color: #e1f5fe !important; /* Light Cyan */
    color: #0277bd !important;
}

/* Hover effects untuk bulan */
.month-jan:hover { background-color: #bbdefb !important; }
.month-feb:hover { background-color: #e1bee7 !important; }
.month-mar:hover { background-color: #c8e6c9 !important; }
.month-apr:hover { background-color: #ffcc02 !important; }
.month-mei:hover { background-color: #f8bbd9 !important; }
.month-jun:hover { background-color: #b2dfdb !important; }
.month-jul:hover { background-color: #fff176 !important; }
.month-ags:hover { background-color: #ffcdd2 !important; }
.month-sep:hover { background-color: #c5cae9 !important; }
.month-okt:hover { background-color: #dcedc8 !important; }
.month-nov:hover { background-color: #ffccbc !important; }
.month-des:hover { background-color: #b3e5fc !important; }

@media (max-width: 768px) {
    .table-responsive {
        font-size: 10px;
    }
}
</style>
@endsection