@extends('layouts.vertical', ['title' => 'Dashboard PKPT'])

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
                <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-warning">
                    <i class="mdi mdi-cog"></i> Kelola Exit Meeting
                </a>
            </div> -->
            <h4 class="page-title">Dashboard Entry PKPT</h4>
            <!-- <p class="text-muted">Menampilkan data dari Entry Meeting dan Exit Meeting</p> -->
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
                    <a href="{{ route('audit.dashboard-pkpt.index') }}" class="btn btn-secondary ms-2">
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
        $totalData = count($dashboardData);
        
        // Entry Meeting breakdown
        $entryMeetingData = collect($dashboardData)->where('source', 'entry_meeting');
        $entryMeetingCount = $entryMeetingData->count();
        
        // Exit Meeting breakdown
        $exitMeetingData = collect($dashboardData)->where('source', 'exit_meeting');
        $exitMeetingCount = $exitMeetingData->count();
        
        $totalAuditor = collect($dashboardData)->sum('jumlah_auditor');
        $totalPka = collect($dashboardData)->sum('jumlah_pka');
    @endphp
    
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-muted fw-normal mt-0" title="Total Data">Total Data</h5>
                        <h3 class="mt-3 mb-3">{{ $totalData }}</h3>
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
                        <h5 class="text-muted fw-normal mt-0" title="Entry Meeting">Entry Meeting</h5>
                        <h3 class="mt-3 mb-3 text-warning">{{ $entryMeetingCount }}</h3>
                        <small class="text-muted">Semua Status</small>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-warning rounded">
                            <i class="mdi mdi-account-group font-20 text-warning"></i>
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
                        <h5 class="text-muted fw-normal mt-0" title="Exit Meeting">Exit Meeting</h5>
                        <h3 class="mt-3 mb-3 text-success">{{ $exitMeetingCount }}</h3>
                        <small class="text-muted">Semua Status</small>
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
                        <h5 class="text-muted fw-normal mt-0" title="Total Auditor">Total Auditor</h5>
                        <h3 class="mt-3 mb-3 text-danger">{{ $totalAuditor }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-danger rounded">
                            <i class="mdi mdi-account-group font-20 text-danger"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Information -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="mdi mdi-bug"></i> Debug Information - Data Count Verification
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <small class="text-muted d-block">Entry Meeting Raw Data</small>
                            <div class="h4 text-warning mb-0">{{ $entryMeetingData->count() }}</div>
                            <small class="text-muted">Items from database</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <small class="text-muted d-block">Exit Meeting Raw Data</small>
                            <div class="h4 text-success mb-0">{{ $exitMeetingData->count() }}</div>
                            <small class="text-muted">Items from database</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <small class="text-muted d-block">Total Dashboard Items</small>
                            <div class="h4 text-primary mb-0">{{ $totalData }}</div>
                            <small class="text-muted">Displayed in table</small>
                        </div>
                    </div>
                </div>
                
                @if($entryMeetingData->count() != collect($dashboardData)->where('source', 'entry_meeting')->count())
                <div class="alert alert-danger mt-3">
                    <strong>⚠️ DATA MISMATCH DETECTED!</strong><br>
                    Entry Meeting Raw Data: <strong>{{ $entryMeetingData->count() }}</strong><br>
                    Entry Meeting in Dashboard: <strong>{{ collect($dashboardData)->where('source', 'entry_meeting')->count() }}</strong><br>
                    <small>This indicates some Entry Meeting data is being filtered out or not processed correctly.</small>
                </div>
                @else
                <div class="alert alert-success mt-3">
                    <strong>✅ DATA COUNT MATCHES!</strong><br>
                    Entry Meeting data count is consistent between raw data and dashboard display.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tabel Dashboard Entry Meeting & Exit Meeting -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Dashboard tampilan Entry Meeting & Exit Meeting:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>Auditee</th>
                                <th>Jenis Audit</th>
                                <th>Jml Auditor</th>
                                @foreach($months as $month)
                                    <th>{{ $month }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboardData as $data)
                                <tr>
                                    <td><strong>{{ $data['auditee'] }}</strong></td>
                                    <td>{{ $data['jenis_audit'] }}</td>
                                    <td>{{ $data['jumlah_auditor'] }}</td>
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
                                    <td colspan="{{ 3 + count($months) }}" class="text-center">Tidak ada data Entry Meeting atau Exit Meeting.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DASHBOARD PELAKSANAAN AUDIT Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <span class="badge bg-secondary" style="font-size: 1rem;">DASHBOARD PELAKSANAAN AUDIT</span>
                </h5>
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
                                <th>Sumber Data</th>
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
                                            switch(strtolower($data['status_realisasi'])) {
                                                case 'selesai':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'sedang berlangsung':
                                                case 'on progress':
                                                    $statusClass = 'bg-warning';
                                                    break;
                                                case 'belum dimulai':
                                                case 'belum':
                                                    $statusClass = 'bg-secondary';
                                                    break;
                                                case 'terlambat':
                                                    $statusClass = 'bg-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $data['status_realisasi'] }}</span>
                                    </td>
                                    <td>
                                        @if($data['source'] == 'entry_meeting')
                                            <span class="badge bg-warning">
                                                <i class="mdi mdi-account-group"></i> Entry Meeting
                                            </span>
                                        @elseif($data['source'] == 'exit_meeting')
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check-circle"></i> Exit Meeting
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="mdi mdi-help-circle"></i> Unknown
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['source'] == 'entry_meeting')
                                            <span class="badge bg-warning">
                                                <i class="mdi mdi-account-group"></i> Entry Meeting
                                            </span>
                                        @elseif($data['source'] == 'exit_meeting')
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check-circle"></i> Exit Meeting
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="mdi mdi-help-circle"></i> Unknown
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data pelaksanaan audit.</td>
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
@endsection
