@extends('layouts.vertical', ['title' => 'Detail Pelaporan Hasil Audit'])

@section('css')
    <style>
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            transition: none !important;
        }
        .card-header {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }
        .card-header h4 {
            color: #1a3a5c !important;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .table-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }
        .table-card thead th {
            background: #f8fafd !important;
            color: #6b7a99 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            border-bottom: 2px solid #e8edf5 !important;
            padding: 12px 14px !important;
        }
        .table-card tbody td {
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-color: #f0f3f9 !important;
            font-size: 0.875rem !important;
        }
        .btn-primary {
            background-color: #1e3a8a !important;
            border-color: #1e3a8a !important;
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 18px;
            font-size: 0.85rem;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.15);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-primary:hover {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(15, 23, 42, 0.2);
        }
        .btn-secondary {
            background-color: #ffffff !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 18px;
            font-size: 0.85rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
            transform: translateY(-1px);
        }
        .detail-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
        }
        .badge {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
        }
        .badge.bg-success {
            background-color: #f0fdf4 !important;
            color: #166534 !important;
            border: 1px solid rgba(22, 101, 52, 0.15);
        }
        .badge.bg-warning {
            background-color: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid rgba(180, 83, 9, 0.15);
        }
        .badge.bg-danger {
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            border: 1px solid rgba(153, 27, 27, 0.15);
        }
        .bg-detail-box {
            background-color: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px;
        }
        .row-num {
            font-size: 0.78rem;
            font-weight: 700;
            color: #9ca3af;
            background: #f9fafb;
            border-radius: 6px;
            padding: 3px 8px;
            display: inline-block;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="mdi mdi-information-outline me-2 text-primary"></i>Detail Pelaporan Hasil Audit
                    </h4>
                    <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="bg-detail-box mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="detail-title">Nomor LHA/LHK</div>
                                    <div class="detail-value">{{ $item->nomor_lha_lhk ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="detail-title">Jenis</div>
                                    <div class="detail-value">{{ $item->jenis_lha_lhk ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="detail-title">Jenis Audit</div>
                                    <div class="detail-value">
                                        @php
                                            $jenisAudit = $item->jenisAudit ?? \App\Models\MasterData\MasterJenisAudit::where('kode', $item->kode_spi)->first();
                                        @endphp
                                        {{ $jenisAudit ? $jenisAudit->nama_jenis_audit : '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="detail-title">Status Approval</div>
                                    <div class="mt-1">
                                        @if($item->status_approval == 'approved')
                                            <span class="badge bg-success">Approved (Final)</span>
                                        @elseif($item->status_approval == 'rejected')
                                            <span class="badge bg-danger">Rejected (Final)</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="detail-title">Kode SPI</div>
                                    <div class="detail-value">{{ $item->kode_spi ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="detail-title">Tanggal Pembuatan</div>
                                    <div class="detail-value">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($item->temuan && $item->temuan->count() > 0)
                    <div class="mt-4">
                        <h5 class="mb-3 text-dark font-weight-700">
                            <i class="mdi mdi-table me-2 text-primary"></i>Daftar ISS (Ikhtisar Saldo Sementara)
                        </h5>
                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Nomor ISS</th>
                                            <th>Hasil Temuan</th>
                                            <th style="width: 150px; text-align: center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($item->temuan as $index => $temuan)
                                        <tr>
                                            <td><span class="row-num">{{ $index + 1 }}</span></td>
                                            <td><strong class="text-primary">{{ $temuan->nomor_iss ?? '-' }}</strong></td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 450px;" title="{{ $temuan->hasil_temuan }}">
                                                    {{ Str::limit($temuan->hasil_temuan, 120) ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($temuan->status_approval == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($temuan->status_approval == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        @canModifyData
                        <a href="{{ route('audit.pelaporan-hasil-audit.edit', $item->id) }}" class="btn btn-primary">
                            <i class="mdi mdi-pencil me-1"></i>Edit Data
                        </a>
                        @endcanModifyData
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
