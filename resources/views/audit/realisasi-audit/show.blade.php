@extends('layouts.vertical', ['title' => 'Detail Realisasi Audit'])

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
                <a href="{{ route('audit.realisasi-audit.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </div>
            <h4 class="page-title">Detail Realisasi Audit</h4>
        </div>
    </div>
</div>

<!-- Header Info -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Informasi Audit</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Divisi/Satuan/Cabang:</strong></td>
                                <td>{{ $auditeeName }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Audit:</strong></td>
                                <td>{{ $jenisAuditName }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Audit:</strong></td>
                                <td><span class="badge bg-primary">{{ $realisasiData->count() }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title">Statistik Status</h5>
                        @php
                            $selesai = $realisasiData->where('status', 'selesai')->count();
                            $onProgress = $realisasiData->where('status', 'on progress')->count();
                            $belum = $realisasiData->where('status', 'belum')->count();
                            $progress = $realisasiData->count() > 0 ? round(($selesai / $realisasiData->count()) * 100, 1) : 0;
                        @endphp
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-success">
                                    <h4>{{ $selesai }}</h4>
                                    <small>Selesai</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-warning">
                                    <h4>{{ $onProgress }}</h4>
                                    <small>On Progress</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-secondary">
                                    <h4>{{ $belum }}</h4>
                                    <small>Belum</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Progress Keseluruhan:</label>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $progress }}%" 
                                     aria-valuenow="{{ $progress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $progress }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detail Realisasi Audit</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Rencana Audit</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Approved At</th>
                                <th>Approved By</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($realisasiData as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @php
                                            $planningStart = '-';
                                            $planningEnd = '-';
                                            if ($item->perencanaanAudit && $item->perencanaanAudit->programKerjaAudit && $item->perencanaanAudit->programKerjaAudit->count() > 0) {
                                                $pka = $item->perencanaanAudit->programKerjaAudit->first();
                                                if ($pka->milestones && $pka->milestones->count() > 0) {
                                                    $firstMilestone = $pka->milestones->sortBy('tanggal_mulai')->first();
                                                    $lastMilestone = $pka->milestones->sortByDesc('tanggal_selesai')->first();
                                                    if ($firstMilestone) {
                                                        $planningStart = \Carbon\Carbon::parse($firstMilestone->tanggal_mulai)->format('d M Y');
                                                    }
                                                    if ($lastMilestone) {
                                                        $planningEnd = \Carbon\Carbon::parse($lastMilestone->tanggal_selesai)->format('d M Y');
                                                    }
                                                }
                                            }
                                        @endphp
                                        <strong>{{ $planningStart }} - {{ $planningEnd }}</strong>
                                    </td>
                                    <td>
                                        @if($item->tanggal_mulai)
                                            <span class="badge bg-info">
                                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tanggal_selesai)
                                            <span class="badge bg-success">
                                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            switch($item->status) {
                                                case 'selesai':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'on progress':
                                                    $statusClass = 'bg-warning';
                                                    break;
                                                case 'belum':
                                                    $statusClass = 'bg-secondary';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td>
                                        @if($item->approved_at)
                                            <span class="badge bg-success">
                                                {{ \Carbon\Carbon::parse($item->approved_at)->format('d M Y H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->approved_by)
                                            <span class="badge bg-primary">User ID: {{ $item->approved_by }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('audit.exit-meeting.edit', $item->id) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i> Edit
                                        </a>
                                        <a href="{{ route('audit.exit-meeting.index') }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="mdi mdi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data realisasi audit.</td>
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









