@extends('layouts.vertical', ['title' => 'Penutup LHA/LHK'])

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
            text-transform: capitalize;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge.bg-success {
            background-color: var(--corp-sage-subtle) !important;
            color: var(--corp-sage-dark) !important;
            border: 1px solid rgba(22, 101, 52, 0.2);
        }

        .badge.bg-info {
            background-color: var(--corp-navy-subtle) !important;
            color: var(--corp-navy-dark) !important;
            border: 1px solid rgba(30, 58, 138, 0.2);
        }

        .badge.bg-warning {
            background-color: var(--corp-amber-subtle) !important;
            color: var(--corp-amber-dark) !important;
            border: 1px solid rgba(154, 52, 18, 0.2);
        }

        .badge.bg-danger {
            background-color: var(--corp-danger-subtle) !important;
            color: var(--corp-danger-dark) !important;
            border: 1px solid rgba(153, 27, 27, 0.2);
        }

        .badge.bg-secondary {
            background-color: #f1f5f9 !important;
            color: var(--corp-neutral-text-muted) !important;
            border: 1px solid rgba(100, 116, 139, 0.2);
        }

        /* PIC Corporate Mini-Cards */
        .pic-card-wrapper {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 190px;
        }

        .pic-corp-card {
            background-color: #ffffff;
            border: 1px solid var(--corp-neutral-border);
            border-radius: 8px;
            padding: 8px 10px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .pic-corp-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .pic-role-tag {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 4px;
        }

        .pic-role-tag.business-contact {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid rgba(30, 64, 175, 0.15);
        }

        .pic-role-tag.approval-1 {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid rgba(180, 83, 9, 0.15);
        }

        .pic-role-tag.approval-2 {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid rgba(22, 101, 52, 0.15);
        }

        .pic-name {
            font-weight: 700;
            color: var(--corp-navy-dark);
            font-size: 11px;
            line-height: 1.3;
        }

        .pic-dept {
            color: var(--corp-neutral-text-muted);
            font-size: 10px;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 3px;
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

        /* Detail Sections */
        .detail-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--corp-navy-primary);
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--corp-navy-subtle);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        .detail-item {
            background-color: var(--corp-neutral-light);
            border: 1px solid var(--corp-neutral-border);
            border-radius: 8px;
            padding: 12px 16px;
        }

        .detail-item-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--corp-neutral-text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .detail-item-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--corp-navy-dark);
        }

        .detail-block {
            background-color: var(--corp-neutral-light);
            border: 1px solid var(--corp-neutral-border);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 1.5rem;
        }

        .detail-block-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--corp-neutral-text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .detail-block-value {
            font-size: 13px;
            color: var(--corp-neutral-text);
            line-height: 1.6;
            white-space: pre-line;
        }

        /* Rejection Banner - Solid corporate red style with full border */
        .rejection-box {
            background-color: var(--corp-danger-subtle) !important;
            border: 1px solid rgba(220, 38, 38, 0.3) !important;
            border-radius: 8px;
            padding: 16px;
            margin-top: 1.5rem;
        }

        .rejection-box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--corp-danger-dark);
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rejection-box-content {
            font-size: 13px;
            color: var(--corp-danger-dark);
            line-height: 1.5;
            font-weight: 500;
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
        .btn-back-em {
            background: #ffffff;
            color: var(--corp-neutral-text) !important;
            border: 1px solid var(--corp-neutral-border) !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 0.9rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back-em:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            transform: translateY(-1px);
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
                        <li class="breadcrumb-item text-muted">Pelaporan</li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Penutup LHA/LHK</li>
                    </ol>
                </nav>
                <h2><i class="mdi mdi-file-document-outline me-2"></i>Penutup LHA/LHK</h2>
                @if(isset($nomorSuratTugas) && $nomorSuratTugas)
                    <div class="subtitle d-flex align-items-center flex-wrap gap-2 mt-1">
                        <span>Mengelola rekomendasi untuk Surat Tugas:</span>
                        <span class="no-surat"><i class="mdi mdi-file-document-outline me-1"></i>{{ $nomorSuratTugas }}</span>
                        @if($perencanaanAudit)
                            <span class="badge bg-secondary">{{ $perencanaanAudit->jenis_audit }}</span>
                        @endif
                    </div>
                @else
                    <div class="subtitle">Kelola dan pantau seluruh rekomendasi penutup LHA/LHK.</div>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('audit.penutup-lha-rekomendasi.select-nomor-surat-tugas') }}" class="btn btn-custom btn-secondary">
                    <i class="mdi mdi-file-document-outline me-1"></i>
                    Pilih Nomor Surat Tugas
                </a>
                @canModifyData
                <a href="{{ route('audit.penutup-lha-rekomendasi.create', ['pelaporan_isi_lha_id' => $isiLhaId, 'nomor_surat_tugas' => $nomorSuratTugas ?? '']) }}" class="btn-add-em">
                    <i class="mdi mdi-plus-circle"></i>
                    Tambah Rekomendasi
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
                <form method="GET" action="{{ route('audit.penutup-lha-rekomendasi.index') }}" class="row g-2 align-items-end">
                    @if(isset($nomorSuratTugas) && $nomorSuratTugas)
                        <input type="hidden" name="nomor_surat_tugas" value="{{ $nomorSuratTugas }}">
                    @endif
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
                        <label for="search" class="form-label mb-1">Cari</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari rekomendasi..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="pic" class="form-label mb-1">PIC</label>
                        <input type="text" name="pic" id="pic" class="form-control" placeholder="Cari PIC..." value="{{ request('pic') }}">
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-custom btn-primary flex-grow-1" style="padding: 9px 14px;">
                                <i class="mdi mdi-magnify me-1"></i> Filter
                            </button>
                            <a href="{{ route('audit.penutup-lha-rekomendasi.index', isset($nomorSuratTugas) ? ['nomor_surat_tugas' => $nomorSuratTugas] : []) }}" class="btn btn-custom btn-secondary flex-grow-1" style="padding: 9px 14px;">
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
                        <i class="mdi mdi-table me-2 text-primary"></i> Data Rekomendasi Penutup LHA/LHK
                    </h5>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-hover" id="penutupLhaTable">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nomor ISS</th>
                                <th>Rekomendasi</th>
                                <th>PIC</th>
                                <th style="white-space: nowrap;">Target Waktu</th>
                                <th>Status</th>
                                <th style="width: 150px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                            <tr>
                                <td><span class="row-num">{{ $index + 1 }}</span></td>
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;" title="{{ $item->temuan->nomor_iss ?? '-' }}">
                                        {{ $item->temuan->nomor_iss ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $item->rekomendasi }}">
                                        {{ Str::limit($item->rekomendasi, 50) }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        // Compile and normalize PIC list for uniform display
                                        $picItems = [];
                                        if (isset($item->picUsers) && $item->picUsers->count() > 0) {
                                            foreach ($item->picUsers as $user) {
                                                $typeLabel = 'PIC';
                                                $typeClass = 'business-contact';
                                                $icon = 'mdi-account';
                                                if ($user->pivot->pic_type === 'business_contact') {
                                                    $typeLabel = 'Business Contact';
                                                    $typeClass = 'business-contact';
                                                    $icon = 'mdi-account-tie';
                                                } elseif ($user->pivot->pic_type === 'approval_1_spi') {
                                                    $typeLabel = 'Business Reviewer 1';
                                                    $typeClass = 'approval-1';
                                                    $icon = 'mdi-account-check';
                                                } elseif ($user->pivot->pic_type === 'approval_2_spi') {
                                                    $typeLabel = 'Business Reviewer 2';
                                                    $typeClass = 'approval-2';
                                                    $icon = 'mdi-shield-check';
                                                }
                                                $picItems[] = [
                                                    'role' => $typeLabel,
                                                    'class' => $typeClass,
                                                    'icon' => $icon,
                                                    'name' => ucwords(strtolower($user->nama)),
                                                    'dept' => $user->auditee->divisi ?? $user->jabatan ?? '-'
                                                ];
                                            }
                                        } elseif ($item->pic_rekomendasi) {
                                            if (strpos($item->pic_rekomendasi, ':') !== false) {
                                                $parts = explode('|', $item->pic_rekomendasi);
                                                foreach ($parts as $part) {
                                                    $subParts = explode(':', trim($part), 2);
                                                    if (count($subParts) == 2) {
                                                        $role = trim($subParts[0]);
                                                        $personDetails = explode('-', trim($subParts[1]), 2);
                                                        $name = trim($personDetails[0]);
                                                        $dept = isset($personDetails[1]) ? trim($personDetails[1]) : '-';
                                                        
                                                        $typeClass = 'business-contact';
                                                        $icon = 'mdi-account';
                                                        if (stripos($role, 'BUSINESS CONTACT') !== false) {
                                                            $role = 'Business Contact';
                                                            $typeClass = 'business-contact';
                                                            $icon = 'mdi-account-tie';
                                                        } elseif (stripos($role, 'APPROVAL 1') !== false || stripos($role, 'APPROVAL_1') !== false || stripos($role, 'BUSINESS REVIEWER 1') !== false || stripos($role, 'BUSINESS_REVIEWER_1') !== false) {
                                                            $role = 'Business Reviewer 1';
                                                            $typeClass = 'approval-1';
                                                            $icon = 'mdi-account-check';
                                                        } elseif (stripos($role, 'APPROVAL 2') !== false || stripos($role, 'APPROVAL_2') !== false || stripos($role, 'BUSINESS REVIEWER 2') !== false || stripos($role, 'BUSINESS_REVIEWER_2') !== false) {
                                                            $role = 'Business Reviewer 2';
                                                            $typeClass = 'approval-2';
                                                            $icon = 'mdi-shield-check';
                                                        }

                                                        $picItems[] = [
                                                            'role' => $role,
                                                            'class' => $typeClass,
                                                            'icon' => $icon,
                                                            'name' => ucwords(strtolower($name)),
                                                            'dept' => ucwords(strtolower($dept))
                                                        ];
                                                    }
                                                }
                                            } else {
                                                $picItems[] = [
                                                    'role' => 'PIC',
                                                    'class' => 'business-contact',
                                                    'icon' => 'mdi-account',
                                                    'name' => $item->pic_rekomendasi,
                                                    'dept' => '-'
                                                ];
                                            }
                                        }
                                    @endphp

                                    @if(count($picItems) > 0)
                                        <div class="pic-card-wrapper">
                                            @foreach($picItems as $pic)
                                                <div class="pic-corp-card">
                                                    <span class="pic-role-tag {{ $pic['class'] }}">
                                                        <i class="mdi {{ $pic['icon'] }} me-0.5"></i>{{ $pic['role'] }}
                                                    </span>
                                                    <div class="pic-name">{{ $pic['name'] }}</div>
                                                    <div class="pic-dept">
                                                        <i class="mdi mdi-office-building-outline text-muted"></i>
                                                        <span>{{ $pic['dept'] }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">{{ $item->target_waktu }}</td>
                                <td>
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
                                    <div class="action-wrap justify-content-center">
                                        <button type="button" 
                                                class="btn-act btn-act-view" 
                                                title="View Detail"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalView{{ $item->id }}">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        
                                        @canModifyData
                                        <a href="{{ route('audit.penutup-lha-rekomendasi.edit', $item->id) }}" 
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
                                            <form id="approval-form-{{ $item->id }}" action="{{ route('audit.penutup-lha-rekomendasi.approval', $item->id) }}" method="POST" style="display:inline-block; margin:0;">
                                                @csrf
                                                <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                
                                                @if($canApproveLvl1)
                                                    <button type="button" class="btn-act btn-act-approve" title="Approve" onclick="approveData({{ $item->id }})">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                @elseif($canApproveLvl2)
                                                    <button type="button" class="btn-act btn-act-approve" title="Approve Final" onclick="approveData({{ $item->id }})">
                                                        <i class="mdi mdi-check-all"></i>
                                                    </button>
                                                @endif

                                                @if($canReject)
                                                    <button type="button" class="btn-act btn-act-reject" title="Reject" onclick="rejectData({{ $item->id }})">
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
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data rekomendasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Data -->
@foreach($data as $item)
<div class="modal fade" id="modalView{{ $item->id }}" tabindex="-1" aria-labelledby="modalViewLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalViewLabel{{ $item->id }}">
                    <i class="mdi mdi-file-document-outline me-1"></i>
                    Detail Rekomendasi Penutup LHA/LHK
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Section 1: Informasi Dokumen -->
                <div class="detail-section-title">
                    <i class="mdi mdi-information-outline"></i> Informasi Dokumen & Audit
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-item-label">Nomor ISS</div>
                        <div class="detail-item-value">{{ $item->temuan->nomor_iss ?? '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Nomor LHA/LHK</div>
                        <div class="detail-item-value">{{ $item->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Target Waktu</div>
                        <div class="detail-item-value">{{ $item->target_waktu }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Status Approval</div>
                        <div class="detail-item-value">
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
                        </div>
                    </div>
                </div>

                <!-- Section 2: Konten Rekomendasi -->
                <div class="detail-section-title">
                    <i class="mdi mdi-file-document-outline"></i> Detail Rekomendasi
                </div>
                <div class="detail-block">
                    <div class="detail-block-label">
                        <i class="mdi mdi-text-box-search-outline text-muted"></i> Rekomendasi
                    </div>
                    <div class="detail-block-value">{{ $item->rekomendasi }}</div>
                </div>
                <div class="detail-block">
                    <div class="detail-block-label">
                        <i class="mdi mdi-lightbulb-on-outline text-muted"></i> Rencana Aksi
                    </div>
                    <div class="detail-block-value">{{ $item->rencana_aksi }}</div>
                </div>
                <div class="detail-block">
                    <div class="detail-block-label">
                        <i class="mdi mdi-attachment-outline text-muted"></i> Eviden Rekomendasi
                    </div>
                    <div class="detail-block-value">{{ $item->eviden_rekomendasi }}</div>
                </div>

                <!-- Section 3: Person in Charge (PIC) -->
                <div class="detail-section-title">
                    <i class="mdi mdi-account-group-outline"></i> Person In Charge (PIC) Rekomendasi
                </div>
                @php
                    // Compile and normalize PIC list for uniform display
                    $modalPicItems = [];
                    if (isset($item->picUsers) && $item->picUsers->count() > 0) {
                        foreach ($item->picUsers as $user) {
                            $typeLabel = 'PIC';
                            $typeClass = 'business-contact';
                            $icon = 'mdi-account';
                            if ($user->pivot->pic_type === 'business_contact') {
                                $typeLabel = 'Business Contact';
                                $typeClass = 'business-contact';
                                $icon = 'mdi-account-tie';
                            } elseif ($user->pivot->pic_type === 'approval_1_spi') {
                                $typeLabel = 'Business Reviewer 1';
                                $typeClass = 'approval-1';
                                $icon = 'mdi-account-check';
                            } elseif ($user->pivot->pic_type === 'approval_2_spi') {
                                $typeLabel = 'Business Reviewer 2';
                                $typeClass = 'approval-2';
                                $icon = 'mdi-shield-check';
                            }
                            $modalPicItems[] = [
                                'role' => $typeLabel,
                                'class' => $typeClass,
                                'icon' => $icon,
                                'name' => ucwords(strtolower($user->nama)),
                                'dept' => $user->auditee->divisi ?? $user->jabatan ?? '-'
                            ];
                        }
                    } elseif ($item->pic_rekomendasi) {
                        if (strpos($item->pic_rekomendasi, ':') !== false) {
                            $parts = explode('|', $item->pic_rekomendasi);
                            foreach ($parts as $part) {
                                $subParts = explode(':', trim($part), 2);
                                if (count($subParts) == 2) {
                                    $role = trim($subParts[0]);
                                    $personDetails = explode('-', trim($subParts[1]), 2);
                                    $name = trim($personDetails[0]);
                                    $dept = isset($personDetails[1]) ? trim($personDetails[1]) : '-';
                                    
                                    $typeClass = 'business-contact';
                                    $icon = 'mdi-account';
                                    if (stripos($role, 'BUSINESS CONTACT') !== false) {
                                        $role = 'Business Contact';
                                        $typeClass = 'business-contact';
                                        $icon = 'mdi-account-tie';
                                    } elseif (stripos($role, 'APPROVAL 1') !== false || stripos($role, 'APPROVAL_1') !== false || stripos($role, 'BUSINESS REVIEWER 1') !== false || stripos($role, 'BUSINESS_REVIEWER_1') !== false) {
                                        $role = 'Business Reviewer 1';
                                        $typeClass = 'approval-1';
                                        $icon = 'mdi-account-check';
                                    } elseif (stripos($role, 'APPROVAL 2') !== false || stripos($role, 'APPROVAL_2') !== false || stripos($role, 'BUSINESS REVIEWER 2') !== false || stripos($role, 'BUSINESS_REVIEWER_2') !== false) {
                                        $role = 'Business Reviewer 2';
                                        $typeClass = 'approval-2';
                                        $icon = 'mdi-shield-check';
                                    }

                                    $modalPicItems[] = [
                                        'role' => $role,
                                        'class' => $typeClass,
                                        'icon' => $icon,
                                        'name' => ucwords(strtolower($name)),
                                        'dept' => ucwords(strtolower($dept))
                                    ];
                                }
                            }
                        } else {
                            $modalPicItems[] = [
                                'role' => 'PIC',
                                'class' => 'business-contact',
                                'icon' => 'mdi-account',
                                'name' => $item->pic_rekomendasi,
                                'dept' => '-'
                            ];
                        }
                    }
                @endphp

                @if(count($modalPicItems) > 0)
                    <div class="row g-2">
                        @foreach($modalPicItems as $pic)
                            <div class="col-md-6">
                                <div class="pic-corp-card h-100 mb-0">
                                    <span class="pic-role-tag {{ $pic['class'] }}">
                                        <i class="mdi {{ $pic['icon'] }} me-0.5"></i>{{ $pic['role'] }}
                                    </span>
                                    <div class="pic-name">{{ $pic['name'] }}</div>
                                    <div class="pic-dept">
                                        <i class="mdi mdi-office-building-outline text-muted"></i>
                                        <span>{{ $pic['dept'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted">-</span>
                @endif

                <!-- Section 4: Rejection Reason (If any) -->
                @if(in_array($item->status_approval, ['rejected', 'rejected_level1']) && ($item->rejection_reason_level2 ?? $item->rejection_reason_level1 ?? $item->alasan_reject))
                    <div class="rejection-box">
                        <div class="rejection-box-title">
                            <i class="mdi mdi-alert-circle-outline"></i> Alasan Rejection / Penolakan
                        </div>
                        <div class="rejection-box-content">
                            @if($item->rejection_reason_level2)
                                <strong>Level 2 (Final):</strong> {{ $item->rejection_reason_level2 }}
                            @elseif($item->rejection_reason_level1)
                                <strong>Level 1 (ASMAN KSPI):</strong> {{ $item->rejection_reason_level1 }}
                            @else
                                {{ $item->alasan_reject }}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-custom btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

function deleteData(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data rekomendasi yang dihapus tidak dapat dikembalikan!",
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
        title: 'Approve Rekomendasi',
        text: 'Anda yakin ingin approve rekomendasi ini?',
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
        title: 'Reject Rekomendasi',
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
    <form id="delete-form-{{ $item->id }}" action="{{ route('audit.penutup-lha-rekomendasi.destroy', $item->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection 