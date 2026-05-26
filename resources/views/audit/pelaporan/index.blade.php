@extends('layouts.vertical', ['title' => 'Pelaporan Hasil Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
    <style>
        /* Modern High-Contrast Corporate Theme Variables */
        :root {
            --corp-navy-dark: #0f172a;
            --corp-navy-primary: #1e3a8a;
            --corp-navy-light: #2563eb;
            --corp-navy-subtle: #eff6ff;
            
            --corp-sage-dark: #166534;
            --corp-sage-primary: #15803d;
            --corp-sage-subtle: #f0fdf4;
            
            --corp-amber-dark: #9a3412;
            --corp-amber-primary: #d97706;
            --corp-amber-subtle: #fffbeb;
            
            --corp-danger-dark: #991b1b;
            --corp-danger-primary: #dc2626;
            --corp-danger-subtle: #fef2f2;
            
            --corp-neutral-light: #f8fafc;
            --corp-neutral-border: #e2e8f0;
            --corp-neutral-text: #334155;
            --corp-neutral-text-muted: #64748b;
        }

        /* Base Card Styling */
        .card {
            border: 1px solid var(--corp-neutral-border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            transition: box-shadow 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: var(--corp-neutral-light) !important;
            border-bottom: 1px solid var(--corp-neutral-border);
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }

        /* High-Contrast Corporate Buttons (No Gradients) */
        .btn-custom {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding: 6px 14px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary {
            background-color: var(--corp-navy-primary) !important;
            color: #ffffff !important;
            border: 1px solid var(--corp-navy-primary) !important;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.15) !important;
        }

        .btn-primary:hover {
            background-color: var(--corp-navy-dark) !important;
            border-color: var(--corp-navy-dark) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(15, 23, 42, 0.2) !important;
        }

        .btn-secondary {
            background-color: #ffffff !important;
            color: var(--corp-neutral-text) !important;
            border: 1px solid var(--corp-neutral-border) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }

        .btn-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            color: var(--corp-navy-primary) !important;
            border-color: var(--corp-navy-primary) !important;
            background-color: transparent !important;
        }

        .btn-outline-primary:hover {
            background-color: var(--corp-navy-subtle) !important;
            transform: translateY(-1px);
        }

        .btn-outline-success {
            color: var(--corp-sage-primary) !important;
            border-color: var(--corp-sage-primary) !important;
            background-color: transparent !important;
        }

        .btn-outline-success:hover {
            background-color: var(--corp-sage-subtle) !important;
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            color: var(--corp-danger-primary) !important;
            border-color: var(--corp-danger-primary) !important;
            background-color: transparent !important;
        }

        .btn-outline-danger:hover {
            background-color: var(--corp-danger-subtle) !important;
            transform: translateY(-1px);
        }

        .btn-outline-info {
            color: var(--corp-navy-light) !important;
            border-color: var(--corp-navy-light) !important;
            background-color: transparent !important;
        }

        .btn-outline-info:hover {
            background-color: var(--corp-navy-subtle) !important;
            transform: translateY(-1px);
        }

        /* Table Styling */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--corp-neutral-border);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--corp-navy-dark) !important;
            color: #ffffff !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border: none;
            vertical-align: middle;
        }

        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            color: var(--corp-neutral-text);
            font-size: 13px;
            border-bottom: 1px solid var(--corp-neutral-border);
        }

        .table-hover tbody tr {
            transition: background-color 0.15s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Solid Color Badge Styles (No Gradients) */
        .badge {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge.bg-success, .iss-status.bg-success {
            background-color: var(--corp-sage-subtle) !important;
            color: var(--corp-sage-dark) !important;
            border: 1px solid rgba(22, 101, 52, 0.2) !important;
        }

        .badge.bg-info, .iss-status.bg-info {
            background-color: var(--corp-navy-subtle) !important;
            color: var(--corp-navy-dark) !important;
            border: 1px solid rgba(30, 58, 138, 0.2) !important;
        }

        .badge.bg-warning, .iss-status.bg-warning {
            background-color: var(--corp-amber-subtle) !important;
            color: var(--corp-amber-dark) !important;
            border: 1px solid rgba(154, 52, 18, 0.2) !important;
        }

        .badge.bg-danger, .iss-status.bg-danger {
            background-color: var(--corp-danger-subtle) !important;
            color: var(--corp-danger-dark) !important;
            border: 1px solid rgba(153, 27, 27, 0.2) !important;
        }

        .badge.bg-secondary, .iss-status.bg-secondary {
            background-color: #f1f5f9 !important;
            color: var(--corp-neutral-text-muted) !important;
            border: 1px solid rgba(100, 116, 139, 0.2) !important;
        }

        .badge.bg-primary, .iss-status.bg-primary {
            background-color: var(--corp-navy-subtle) !important;
            color: var(--corp-navy-primary) !important;
            border: 1px solid rgba(30, 58, 138, 0.2) !important;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--corp-neutral-border);
            padding: 8px 12px;
            font-size: 13px;
            color: var(--corp-neutral-text);
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--corp-navy-light);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--corp-neutral-text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        /* Modern ISS Circular Count Badge */
        .iss-count-badge {
            background-color: var(--corp-navy-subtle);
            color: var(--corp-navy-primary);
            border: 1px solid rgba(30, 58, 138, 0.2);
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .iss-count-badge:hover {
            transform: scale(1.1);
            background-color: var(--corp-navy-primary);
            color: #ffffff;
            border-color: var(--corp-navy-primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Premium Modal Styling */
        .modal-content {
            border: 1px solid var(--corp-neutral-border) !important;
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15) !important;
        }

        .modal-header {
            background-color: var(--corp-navy-dark) !important;
            color: #ffffff !important;
            border-bottom: 1px solid var(--corp-neutral-border) !important;
            padding: 1.25rem 1.5rem !important;
        }

        .modal-header .modal-title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(1) brightness(2);
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 2rem !important;
            background-color: #ffffff;
        }

        .modal-footer {
            background-color: var(--corp-neutral-light) !important;
            border-top: 1px solid var(--corp-neutral-border) !important;
            padding: 1rem 2rem !important;
        }

        /* ISS Item Card Layout inside Modal */
        .iss-item {
            border: 1px solid var(--corp-neutral-border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: var(--corp-neutral-light);
            transition: all 0.2s ease;
        }

        .iss-item:hover {
            box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .iss-header {
            background-color: var(--corp-navy-dark) !important;
            color: #ffffff !important;
            padding: 10px 18px;
            margin: -20px -20px 20px -20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .iss-number {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .iss-status {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--corp-neutral-text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .field-value {
            font-size: 13px;
            color: var(--corp-neutral-text);
            line-height: 1.5;
        }

        .significance-badge {
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
        }

        /* Row transition animations */
        @starting-style {
            .table tbody tr {
                opacity: 0;
                transform: translateY(4px);
            }
        }

        .table tbody tr {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* ===== HERO HEADER ===== */
        .em-hero {
            background: #fff;
            border-radius: 16px;
            padding: 24px 28px;
            color: #1a3a5c;
            margin-bottom: 24px;
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
        .em-hero h2 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
            color: #1a3a5c;
        }
        .em-hero .subtitle {
            font-size: 0.85rem;
            color: #6b7a99;
        }
        .btn-add-em {
            background: #1a3a5c;
            color: #fff !important;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 0.9rem;
            box-shadow: 0 2px 10px rgba(26,58,92,0.18);
            transition: all .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add-em:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(26,58,92,0.25);
            color: #fff !important;
            background: #2d6a9f;
        }

        /* ===== FILTER CARD ===== */
        .filter-card {
            border-radius: 16px;
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            margin-bottom: 24px;
            background: #fff;
        }

        /* ===== TABLE CARD ===== */
        .table-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 2px 20px rgba(0,0,0,0.06);
            overflow: hidden;
            background: #fff;
        }
        .table-card .card-header-custom {
            background: #fff;
            padding: 20px 24px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .table-card .card-header-custom h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #1a3a5c;
        }

        /* ===== TABLE STYLING OVERRIDES ===== */
        .table-card thead th {
            background: #f8fafd !important;
            color: #6b7a99 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            border-bottom: 2px solid #e8edf5 !important;
            padding: 13px 14px !important;
            white-space: nowrap;
        }
        .table-card tbody tr:hover {
            background: #f4f8ff !important;
        }
        .table-card tbody td {
            padding: 13px 14px !important;
            vertical-align: middle !important;
            border-color: #f0f3f9 !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
        }

        /* No baris */
        .row-num {
            font-size: 0.78rem;
            font-weight: 700;
            color: #9ca3af;
            background: #f9fafb;
            border-radius: 6px;
            padding: 3px 8px;
            display: inline-block;
        }

        /* No surat tugas / LHA */
        .no-surat {
            font-weight: 600;
            font-size: 0.82rem;
            color: #1a3a5c;
            background: #eef3fb;
            border-radius: 8px;
            padding: 5px 10px;
            display: inline-block;
            word-break: break-word;
            line-height: 1.4;
        }

        /* Action buttons */
        .action-wrap {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .btn-act {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            border: none;
            transition: all .2s;
            cursor: pointer;
            text-decoration: none;
            flex-shrink: 0;
        }
        .btn-act:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.15); }
        .btn-act-view     { background: #e0f2fe; color: #0369a1; }
        .btn-act-view:hover   { background: #bae6fd; color: #0284c7; }
        .btn-act-edit     { background: #fef3c7; color: #d97706; }
        .btn-act-edit:hover   { background: #fde68a; color: #b45309; }
        .btn-act-delete   { background: #fee2e2; color: #dc2626; }
        .btn-act-delete:hover { background: #fecaca; color: #b91c1c; }
        .btn-act-approve { background: #dcfce7; color: #16a34a; }
        .btn-act-approve:hover { background: #bbf7d0; color: #15803d; }
        .btn-act-reject { background: #f3f4f6; color: #4b5563; }
        .btn-act-reject:hover { background: #e5e7eb; color: #374151; }
    </style>
@endsection

@section('content')
<div class="row mb-1">
    <div class="col-12">
        <div class="em-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 fs-12">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item text-muted">Audit</li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Pelaporan Hasil Audit</li>
                    </ol>
                </nav>
                <h2><i class="mdi mdi-file-document-outline me-2"></i>Pelaporan Hasil Audit</h2>
                <div class="subtitle">Kelola dan pantau seluruh pelaporan hasil audit, ISS, dan status approval.</div>
            </div>
            <div>
                @canModifyData
                <a href="{{ route('audit.pelaporan-hasil-audit.create') }}" class="btn-add-em">
                    <i class="mdi mdi-plus-circle"></i>
                    Tambah Pelaporan
                </a>
                @endcanModifyData
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            @include('components.alert')
        @endif

        <!-- Filter Card -->
        <div class="card filter-card">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('audit.pelaporan-hasil-audit.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="jenis_lha_lhk" class="form-label mb-1">Jenis LHA/LHK</label>
                        <select name="jenis_lha_lhk" id="jenis_lha_lhk" class="form-select">
                            <option value="">Semua Jenis</option>
                            <option value="LHA" {{ request('jenis_lha_lhk') == 'LHA' ? 'selected' : '' }}>LHA</option>
                            <option value="LHK" {{ request('jenis_lha_lhk') == 'LHK' ? 'selected' : '' }}>LHK</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="kode_spi" class="form-label mb-1">Jenis Audit</label>
                        <select name="kode_spi" id="kode_spi" class="form-select">
                            <option value="">Semua</option>
                            @php
                                $jenisAudit = \App\Models\MasterData\MasterJenisAudit::all();
                            @endphp
                            @foreach($jenisAudit as $ja)
                                <option value="{{ $ja->kode }}" {{ request('kode_spi') == $ja->kode ? 'selected' : '' }}>{{ $ja->nama_jenis_audit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_approval" class="form-label mb-1">Status Approval</label>
                        <select name="status_approval" id="status_approval" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_approval') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved_level1" {{ request('status_approval') == 'approved_level1' ? 'selected' : '' }}>Approved Level 1</option>
                            <option value="approved" {{ request('status_approval') == 'approved' ? 'selected' : '' }}>Approved (Final)</option>
                            <option value="rejected_level1" {{ request('status_approval') == 'rejected_level1' ? 'selected' : '' }}>Rejected Level 1</option>
                            <option value="rejected" {{ request('status_approval') == 'rejected' ? 'selected' : '' }}>Rejected (Final)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-custom btn-primary flex-grow-1" style="padding: 9px 14px;">
                                <i class="mdi mdi-magnify me-1"></i> Filter
                            </button>
                            <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-custom btn-secondary flex-grow-1" style="padding: 9px 14px;">
                                <i class="mdi mdi-refresh me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card table-card">
            <div class="card-body p-0">
                <div class="px-4 py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 text-dark font-weight-700">
                        <i class="mdi mdi-table me-2 text-primary"></i> Data Pelaporan Hasil Audit
                    </h5>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-hover" id="pelaporanTable">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Surat Tugas</th>
                                <th>Nomor LHA/LHK</th>
                                <th style="width: 80px;">Jenis</th>
                                <th style="width: 150px;">Jenis Audit</th>
                                <th style="width: 100px;">Kode SPI</th>
                                <th style="width: 80px;">ISS</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $item)
                                <tr>
                                    <td class="text-center"><span class="row-num">{{ $index + 1 }}</span></td>
                                    <td>
                                        @if($item->perencanaanAudit && $item->perencanaanAudit->nomor_surat_tugas)
                                            <span class="no-surat">{{ $item->perencanaanAudit->nomor_surat_tugas }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item->nomor_lha_lhk }}</strong>
                                        <br>
                                        <small class="text-muted">Urut: {{ $item->nomor_urut }} | Tahun: {{ $item->tahun }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->jenis_lha_lhk == 'LHA' ? 'bg-primary' : 'bg-info' }}">
                                            {{ $item->jenis_lha_lhk }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $jenisAudit = \App\Models\MasterData\MasterJenisAudit::where('kode', $item->kode_spi)->first();
                                            $namaJenisAudit = $jenisAudit ? $jenisAudit->nama_jenis_audit : '-';
                                        @endphp
                                        <span class="badge bg-info">
                                            {{ $namaJenisAudit }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <code>{{ $item->kode_spi }}</code>
                                    </td>
                                    <td class="text-center">
                                        @if($item->temuan && $item->temuan->count() > 0)
                                            <span class="iss-count-badge" onclick="showIssModal({{ $item->id }}, '{{ $item->nomor_lha_lhk }}')" title="Klik untuk lihat detail ISS">
                                                {{ $item->temuan->count() }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusApprovalClass = '';
                                            $statusApprovalText = '';
                                            switch($item->status_approval) {
                                                case 'approved':
                                                    $statusApprovalClass = 'bg-success';
                                                    $statusApprovalText = 'Approved (Final)';
                                                    break;
                                                case 'approved_level1':
                                                    $statusApprovalClass = 'bg-info';
                                                    $statusApprovalText = 'Approved Level 1';
                                                    break;
                                                case 'rejected':
                                                    $statusApprovalClass = 'bg-danger';
                                                    $statusApprovalText = 'Rejected (Final)';
                                                    break;
                                                case 'rejected_level1':
                                                    $statusApprovalClass = 'bg-warning';
                                                    $statusApprovalText = 'Rejected Level 1';
                                                    break;
                                                default:
                                                    $statusApprovalClass = 'bg-secondary';
                                                    $statusApprovalText = 'Pending';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusApprovalClass }}">{{ $statusApprovalText }}</span>
                                    </td>
                                    <td>
                                        <div class="action-wrap">
                                            @canModifyData
                                            <a href="{{ route('audit.pelaporan-hasil-audit.edit', $item->id) }}" 
                                               class="btn-act btn-act-edit" 
                                               title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            @endcanModifyData
                                            
                                            @php
                                                $canApproveLvl1 = \App\Helpers\ApprovalHelper::canApproveLevel1($item);
                                                $canApproveLvl2 = \App\Helpers\ApprovalHelper::canApproveLevel2($item);
                                                $canReject      = \App\Helpers\ApprovalHelper::canReject($item);
                                            @endphp
                                
                                            @if($canApproveLvl1 || $canApproveLvl2 || $canReject)
                                                <form id="approval-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.approval', $item->id) }}" method="POST" style="display:inline-block; margin: 0;">
                                                    @csrf
                                                    <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    
                                                    @if($canApproveLvl1)
                                                        <button type="button" class="btn-act btn-act-approve" onclick="approveData({{ $item->id }})" title="Approve">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                    @elseif($canApproveLvl2)
                                                        <button type="button" class="btn-act btn-act-approve" onclick="approveData({{ $item->id }})" title="Approve Final">
                                                            <i class="mdi mdi-check-all"></i>
                                                        </button>
                                                    @endif
                                
                                                    @if($canReject)
                                                        <button type="button" class="btn-act btn-act-reject" onclick="rejectData({{ $item->id }})" title="Reject">
                                                            <i class="mdi mdi-close"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                            
                                            @canModifyData
                                            <button type="button" 
                                                    class="btn-act btn-act-delete" 
                                                    title="Hapus"
                                                    onclick="deleteData({{ $item->id }})">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                            @endcanModifyData
                                        </div>
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

<!-- Modal untuk Detail ISS -->
<div class="modal fade" id="issModal" tabindex="-1" aria-labelledby="issModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="issModalLabel">
                    <i class="mdi mdi-file-document-outline me-2"></i>
                    Detail ISS - <span id="modalLhaLhkTitle"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="issModalBody">
                <!-- ISS details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-custom btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Edit ISS -->
<div class="modal fade" id="editIssModal" tabindex="-1" aria-labelledby="editIssModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIssModalLabel">
                    <i class="mdi mdi-pencil me-2 text-warning"></i>
                    Edit ISS
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editIssForm">
                    <input type="hidden" id="editTemuanId" name="temuan_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editHasilTemuan" class="form-label">Hasil Temuan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="editHasilTemuan" name="hasil_temuan" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editPermasalahan" class="form-label">Permasalahan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="editPermasalahan" name="permasalahan" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editKodeAoi" class="form-label">Kode AOI <span class="text-danger">*</span></label>
                                <select class="form-select" id="editKodeAoi" name="kode_aoi_id" required>
                                    <option value="">Pilih Kode AOI</option>
                                    @foreach($kodeAoi as $aoi)
                                        <option value="{{ $aoi->id }}">{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editKodeRisk" class="form-label">Kode Risiko <span class="text-danger">*</span></label>
                                <select class="form-select" id="editKodeRisk" name="kode_risk_id" required>
                                    <option value="">Pilih Kode Risiko</option>
                                    @foreach($kodeRisk as $risk)
                                        <option value="{{ $risk->id }}">{{ $risk->kode_risiko }} - {{ $risk->deskripsi_risiko }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editKriteria" class="form-label">Kriteria <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="editKriteria" name="kriteria" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editSignifikan" class="form-label">Signifikansi <span class="text-danger">*</span></label>
                                <select class="form-select" id="editSignifikan" name="signifikan" required>
                                    <option value="">Pilih Signifikansi</option>
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Rendah">Rendah</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editDampakTerjadi" class="form-label">Dampak yang Terjadi</label>
                                <textarea class="form-control" id="editDampakTerjadi" name="dampak_terjadi" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editDampakPotensi" class="form-label">Dampak Potensial</label>
                                <textarea class="form-control" id="editDampakPotensi" name="dampak_potensi" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editPenyebab" class="form-label">Penyebab (Root Cause Analysis) <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editPenyebab" name="penyebab" rows="4" required placeholder="Jelaskan penyebab masalah berdasarkan analisis 5M (Man, Machine, Method, Material, Environment)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-custom btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-custom btn-primary" onclick="saveEditIss()">
                    <i class="mdi mdi-content-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const canModifyData = @json(\App\Helpers\AuthHelper::canModifyData());

document.addEventListener('DOMContentLoaded', function() {
    function initPage() {
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) {
            const $ = window.jQuery;
            console.log('jQuery and DataTables are loaded, running initialization...');
            
            // Initialize DataTable
            $('#pelaporanTable').DataTable({
                responsive: false,
                pageLength: 15,
                lengthMenu: [[15, 30, 50, 100], [15, 30, 50, 100]],
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [8] } // Disable sorting for action column
                ],
                language: {
                    emptyTable: "Tidak ada data."
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Logout handler for profile menu in card header
            $('#logout-link-card').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah Anda yakin ingin keluar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('logout') }}';
                        form.style.display = 'none';
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        const csrfInput = $('<input>').attr({
                            type: 'hidden',
                            name: '_token',
                            value: csrfToken
                        });
                        form.appendChild(csrfInput[0]);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        } else {
            console.log('Waiting for jQuery and DataTables to be ready...');
            setTimeout(initPage, 50);
        }
    }
    initPage();
});

function showIssModal(id, nomorLhaLhk) {
    console.log('showIssModal called with:', { id, nomorLhaLhk });
    
    // Store current audit ID
    $('#issModal').data('current-audit-id', id);
    
    // Show loading state
    $('#modalLhaLhkTitle').text(nomorLhaLhk);
    $('#issModalBody').html('<div class="text-center py-4"><i class="mdi mdi-loading mdi-spin mdi-36px"></i><p class="mt-2">Memuat data ISS...</p></div>');
    
    // Show modal using Bootstrap 5
    const modal = new bootstrap.Modal(document.getElementById('issModal'));
    modal.show();
    
    console.log('Modal should be visible now');
    
    // Load ISS data via AJAX using new endpoint
    $.ajax({
        url: `/audit/pelaporan-hasil-audit/${id}/temuan`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('AJAX response:', response);
            
            if (!response.success) {
                $('#issModalBody').html('<div class="text-center py-4"><i class="mdi mdi-alert-circle mdi-36px text-danger"></i><p class="mt-2">Gagal memuat data: ' + (response.message || 'Unknown error') + '</p></div>');
                return;
            }
            
            const temuanData = response.data || [];
            
            if (temuanData.length === 0) {
                $('#issModalBody').html('<div class="text-center py-4"><i class="mdi mdi-alert-circle mdi-36px text-warning"></i><p class="mt-2">Tidak ada data ISS untuk dokumen ini.</p></div>');
                return;
            }
            
            let html = '';
            temuanData.forEach((temuan, index) => {
                html += `
                    <div class="iss-item">
                        <div class="iss-header">
                            <div>
                                <span class="iss-number">ISS ${temuan.nomor_urut_iss}</span>
                                <span class="ms-2">${temuan.nomor_iss}</span>
                            </div>
                            <span class="iss-status ${temuan.status_approval === 'approved' ? 'bg-success' : (temuan.status_approval === 'rejected' ? 'bg-danger' : 'bg-warning')}">
                                ${temuan.status_approval === 'approved' ? 'Approved' : (temuan.status_approval === 'rejected' ? 'Rejected' : 'Pending')}
                            </span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Hasil Temuan:</div>
                                    <div class="field-value">${temuan.hasil_temuan || '-'}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Permasalahan:</div>
                                    <div class="field-value">${temuan.permasalahan || '-'}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Kode AOI:</div>
                                    <div class="field-value">
                                        <strong>${temuan.kode_aoi?.kode_area_of_improvement || '-'}</strong><br>
                                        <small>${temuan.kode_aoi?.deskripsi_area_of_improvement || '-'}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Kode Risiko:</div>
                                    <div class="field-value">
                                        <strong>${temuan.kode_risk?.kode_risiko || '-'}</strong><br>
                                        <small>${temuan.kode_risk?.deskripsi_risiko || '-'}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Kriteria:</div>
                                    <div class="field-value">${temuan.kriteria || '-'}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Signifikansi:</div>
                                    <div class="field-value">
                                        <span class="badge ${temuan.signifikan === 'Tinggi' ? 'bg-danger' : (temuan.signifikan === 'Medium' ? 'bg-warning' : 'bg-success')} significance-badge">
                                            ${temuan.signifikan || '-'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Dampak yang Terjadi:</div>
                                    <div class="field-value">${temuan.dampak_terjadi || '-'}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Dampak Potensial:</div>
                                    <div class="field-value">${temuan.dampak_potensi || '-'}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <div class="field-label">Penyebab (Root Cause Analysis):</div>
                            <div class="field-value">${temuan.penyebab || '-'}</div>
                        </div>
                        
                        ${canModifyData ? `
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editIss(${temuan.id})">
                                <i class="mdi mdi-pencil me-1"></i>Edit ISS
                            </button>
                        </div>
                        ` : ''}
                    </div>
                `;
            });
            
            $('#issModalBody').html(html);
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', { xhr, status, error });
            let errorMessage = 'Gagal memuat data ISS. Silakan coba lagi.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Data tidak ditemukan.';
            } else if (xhr.status === 500) {
                errorMessage = 'Terjadi kesalahan server.';
            }
            
            $('#issModalBody').html(`
                <div class="text-center py-4">
                    <i class="mdi mdi-alert-circle mdi-36px text-danger"></i>
                    <p class="mt-2">${errorMessage}</p>
                    <small class="text-muted">Status: ${xhr.status} | Error: ${error}</small>
                </div>
            `);
        }
    });
}

function editIss(temuanId) {
    console.log('Edit ISS with ID:', temuanId);
    console.log('URL:', `/audit/pelaporan-hasil-audit/temuan/${temuanId}`);
    
    // Fetch temuan data for editing
    $.ajax({
        url: `/audit/pelaporan-hasil-audit/temuan/${temuanId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const temuan = response.data;
                
                // Populate form fields
                $('#editTemuanId').val(temuan.id);
                $('#editHasilTemuan').val(temuan.hasil_temuan);
                $('#editPermasalahan').val(temuan.permasalahan);
                $('#editPenyebab').val(temuan.penyebab);
                $('#editKodeAoi').val(temuan.kode_aoi_id);
                $('#editKodeRisk').val(temuan.kode_risk_id);
                $('#editKriteria').val(temuan.kriteria);
                $('#editDampakTerjadi').val(temuan.dampak_terjadi);
                $('#editDampakPotensi').val(temuan.dampak_potensi);
                $('#editSignifikan').val(temuan.signifikan);
                
                // Show edit modal
                const editModal = new bootstrap.Modal(document.getElementById('editIssModal'));
                editModal.show();
                
                // Hide detail modal
                const detailModal = bootstrap.Modal.getInstance(document.getElementById('issModal'));
                if (detailModal) {
                    detailModal.hide();
                }
                
            } else {
                Swal.fire('Error', response.message || 'Gagal mengambil data ISS', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching temuan data:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            Swal.fire('Error', 'Gagal mengambil data ISS untuk diedit', 'error');
        }
    });
}

function saveEditIss() {
    const temuanId = $('#editTemuanId').val();
    
    // Collect form data manually
    const data = {
        hasil_temuan: $('#editHasilTemuan').val(),
        permasalahan: $('#editPermasalahan').val(),
        penyebab: $('#editPenyebab').val(),
        kode_aoi_id: $('#editKodeAoi').val(),
        kode_risk_id: $('#editKodeRisk').val(),
        kriteria: $('#editKriteria').val(),
        dampak_terjadi: $('#editDampakTerjadi').val(),
        dampak_potensi: $('#editDampakPotensi').val(),
        signifikan: $('#editSignifikan').val(),
        _token: $('meta[name="csrf-token"]').attr('content'),
        _method: 'PUT'
    };
    
    // Client-side validation
    const requiredFields = ['hasil_temuan', 'permasalahan', 'penyebab', 'kode_aoi_id', 'kode_risk_id', 'kriteria', 'signifikan'];
    const missingFields = [];
    
    requiredFields.forEach(field => {
        if (!data[field] || data[field].trim() === '') {
            missingFields.push(field);
        }
    });
    
    if (missingFields.length > 0) {
        Swal.fire('Error', `Field berikut harus diisi: ${missingFields.join(', ')}`, 'error');
        return;
    }
    
    // Show loading state
    const saveBtn = document.querySelector('button[onclick="saveEditIss()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...';
    saveBtn.disabled = true;
    
    // Log data being sent
    console.log('Data being sent:', data);
    console.log('CSRF Token:', data._token);
    console.log('Method:', data._method);
    
    $.ajax({
        url: `/audit/pelaporan-hasil-audit/temuan/${temuanId}`,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            console.log('Success response:', response);
            if (response.success) {
                // Show success message
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Hide edit modal
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editIssModal'));
                    editModal.hide();
                    
                    // Refresh ISS data in detail modal
                    const currentAuditId = $('#issModal').data('current-audit-id');
                    if (currentAuditId) {
                        showIssModal(currentAuditId, $('#modalLhaLhkTitle').text());
                    }
                });
                
            } else {
                Swal.fire('Error', response.message || 'Gagal menyimpan perubahan', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving temuan:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            let errorMessage = 'Gagal menyimpan perubahan';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 422) {
                errorMessage = 'Data tidak valid. Silakan periksa input Anda.';
            }
            
            Swal.fire('Error', errorMessage, 'error');
        },
        complete: function() {
            // Restore button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });
}

function deleteData(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data pelaporan hasil audit yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

function approveData(id) {
    Swal.fire({
        title: 'Approve Data',
        text: 'Anda yakin ingin approve data pelaporan hasil audit ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('approval-form-' + id);
            const actionInput = document.getElementById('action-' + id);
            if (actionInput) {
                actionInput.value = 'approve';
            }
            form.submit();
        }
    });
}

function approveDataPending(id) {
    Swal.fire({
        title: 'Tidak Dapat Approve',
        html: '<div class="text-start">' +
              '<p><strong>Data belum diapprove oleh ASMAN KSPI!</strong></p>' +
              '<p>Untuk melakukan approval Level 2, data harus diapprove oleh ASMAN KSPI terlebih dahulu (Level 1).</p>' +
              '<p class="text-muted">Status saat ini: <strong>Pending</strong></p>' +
              '</div>',
        icon: 'warning',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Mengerti'
    });
}

function rejectData(id) {
    Swal.fire({
        title: 'Reject Data',
        text: 'Masukkan alasan reject (minimal 10 karakter):',
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Ketik alasan reject di sini...',
        inputAttributes: {
            'aria-label': 'Alasan reject',
            'minlength': 10
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reject!',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Alasan reject harus diisi!'
            }
            if (value.length < 10) {
                return 'Alasan reject minimal 10 karakter!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('approval-form-' + id);
            const actionInput = document.getElementById('action-' + id);
            if (actionInput) {
                actionInput.value = 'reject';
            }
            const rejectionInput = document.createElement('input');
            rejectionInput.type = 'hidden';
            rejectionInput.name = 'rejection_reason';
            rejectionInput.value = result.value;
            form.appendChild(rejectionInput);
            form.submit();
        }
    });
}
</script>

<!-- Hidden Forms -->
@foreach($data as $item)
    <form id="delete-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.destroy', $item->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection 