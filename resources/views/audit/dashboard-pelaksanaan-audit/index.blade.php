@extends('layouts.vertical', ['title' => 'Dashboard Pelaksanaan Audit'])

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
            <!-- <div class="page-title-right">
                <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-info">
                    <i class="mdi mdi-cog"></i> Kelola Exit Meeting
                </a>
            </div> -->
            <h4 class="page-title">Dashboard Pelaksanaan Audit</h4>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-3 d-flex align-items-center" action="">
                    <label for="bulan" class="me-2 mb-0">Filter Bulan (Planning Start):</label>
                    <input type="month" name="bulan" id="bulan" class="form-control me-2" style="max-width:200px;" value="{{ request('bulan') }}">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('audit.dashboard-pelaksanaan-audit.index') }}" class="btn btn-secondary ms-2">
                        <i class="mdi mdi-refresh"></i> Reset
                    </a>
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
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Divisi/Satuan/Cabang</th>
                                <th>Jenis Audit</th>
                                <th>Rencana Audit</th>
                                <th>Realisasi Audit</th>
                                <th>Status</th>
                                <th>Status Approval</th>
                                @foreach($months as $month)
                                    <th>{{ $month }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboardData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['auditee'] }}</strong></td>
                                    <td>{{ $data['jenis_audit'] }}</td>
                                    <td>{{ $data['rencana_audit_mulai'] }} - {{ $data['rencana_audit_selesai'] }}</td>
                                    <td>{{ $data['realisasi_audit_mulai'] }} - {{ $data['realisasi_audit_selesai'] }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            switch($data['status_realisasi']) {
                                                case 'Selesai':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'Sedang Berlangsung':
                                                    $statusClass = 'bg-warning';
                                                    break;
                                                case 'Belum Dimulai':
                                                    $statusClass = 'bg-secondary';
                                                    break;
                                                case 'Terlambat':
                                                    $statusClass = 'bg-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $data['status_realisasi'] }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $approvalClass = '';
                                            switch($data['status_approval']) {
                                                case 'approved':
                                                    $approvalClass = 'bg-success';
                                                    break;
                                                case 'rejected':
                                                    $approvalClass = 'bg-danger';
                                                    break;
                                                case 'pending':
                                                    $approvalClass = 'bg-warning';
                                                    break;
                                                default:
                                                    $approvalClass = 'bg-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $approvalClass }}">
                                            @if($data['status_approval'] == 'approved')
                                                Approved
                                            @elseif($data['status_approval'] == 'rejected')
                                                Rejected
                                            @else
                                                Pending
                                            @endif
                                        </span>
                                    </td>
                                    @foreach($months as $month)
                                        <td>
                                            @if(!empty($data['schedule'][$month]))
                                                <span class="badge bg-success">Scheduled</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 7 + count($months) }}" class="text-center">Tidak ada data pelaksanaan audit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Tabel Analytikal Pelaksanaan Audit -->
                <div class="mt-5">
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
                                    <th>Status Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dashboardData as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $data['auditee'] }}</strong></td>
                                        <td>{{ $data['rencana_audit_mulai'] }} - {{ $data['rencana_audit_selesai'] }}</td>
                                        <td>{{ $data['realisasi_audit_mulai'] }} - {{ $data['realisasi_audit_selesai'] }}</td>
                                        <td>
                                            @php
                                                $statusClass = '';
                                                switch($data['status_realisasi']) {
                                                    case 'Selesai':
                                                        $statusClass = 'bg-success';
                                                        break;
                                                    case 'Sedang Berlangsung':
                                                        $statusClass = 'bg-warning';
                                                        break;
                                                    case 'Belum Dimulai':
                                                        $statusClass = 'bg-secondary';
                                                        break;
                                                    case 'Terlambat':
                                                        $statusClass = 'bg-danger';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-info';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $data['status_realisasi'] }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $approvalClass = '';
                                                switch($data['status_approval']) {
                                                    case 'approved':
                                                        $approvalClass = 'bg-success';
                                                        break;
                                                    case 'rejected':
                                                        $approvalClass = 'bg-danger';
                                                        break;
                                                    case 'pending':
                                                        $approvalClass = 'bg-warning';
                                                        break;
                                                    default:
                                                        $approvalClass = 'bg-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $approvalClass }}">
                                                @if($data['status_approval'] == 'approved')
                                                    Approved
                                                @elseif($data['status_approval'] == 'rejected')
                                                    Rejected
                                                @else
                                                    Pending
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data pelaksanaan audit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
