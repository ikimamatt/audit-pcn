@extends('layouts.vertical', ['title' => 'Pilih Nomor Surat Tugas'])

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

        /* Clean form styling */
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

        /* No surat tugas */
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

        .btn-custom-go {
            background-color: var(--corp-navy-primary);
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.15);
        }

        .btn-custom-go:hover {
            background-color: var(--corp-navy-dark);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(15, 23, 42, 0.2);
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
    </style>
@endsection

@section('content')
<div class="row mb-1 justify-content-center">
    <div class="col-md-10">
        <div class="em-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 fs-12">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item text-muted">Audit</li>
                        <li class="breadcrumb-item text-muted">Pemantauan</li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Pilih Nomor Surat Tugas</li>
                    </ol>
                </nav>
                <h2><i class="mdi mdi-file-document-outline me-2"></i>Pilih Nomor Surat Tugas</h2>
                <div class="subtitle">Silakan pilih nomor surat tugas di bawah ini untuk melihat pemantauan hasil audit.</div>
            </div>
            <div>
                <a href="{{ url()->previous() }}" class="btn-back-em">
                    <i class="mdi mdi-arrow-left"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        @if(session('success'))
            @include('components.alert')
        @endif

        <!-- Filter Card -->
        <div class="card filter-card">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label mb-1">Cari Dokumen</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Cari nomor surat tugas atau nomor LHA/LHK..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="jenis_audit" class="form-label mb-1">Jenis Audit</label>
                        <select name="jenis_audit" id="jenis_audit" class="form-select">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisAuditList as $jenis)
                                <option value="{{ $jenis }}" {{ request('jenis_audit') == $jenis ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-custom btn-primary flex-grow-1" style="padding: 9px 14px;">
                                <i class="mdi mdi-magnify me-1"></i> Cari
                            </button>
                            <a href="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="btn btn-custom btn-secondary flex-grow-1" style="padding: 9px 14px;">
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
                        <i class="mdi mdi-table me-2 text-primary"></i> Data Surat Tugas
                    </h5>
                    @if($nomorSuratTugasList->count() > 0)
                        <span class="badge bg-info">
                            {{ $nomorSuratTugasList->count() }} Surat Tugas
                        </span>
                    @endif
                </div>

                @if($nomorSuratTugasList->count() > 0)
                    <div class="table-responsive p-3">
                        <table class="table table-hover" id="responsive-datatable">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Nomor Surat Tugas</th>
                                    <th>Jenis Audit</th>
                                    <th>Nomor LHA/LHK</th>
                                    <th>Jumlah Rekomendasi</th>
                                    <th style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nomorSuratTugasList as $index => $item)
                                <tr>
                                    <td class="text-center"><span class="row-num">{{ $index + 1 }}</span></td>
                                    <td>
                                        <span class="no-surat">{{ $item['nomor_surat_tugas'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item['jenis_audit'] }}</span>
                                    </td>
                                    <td>
                                        @if($item['nomor_lha_lhk'])
                                            <strong>{{ $item['nomor_lha_lhk'] }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="mdi mdi-clipboard-text-outline me-1"></i>
                                            {{ $item['count_rekomendasi'] }} rekomendasi
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('audit.pemantauan.index', ['nomor_surat_tugas' => $item['nomor_surat_tugas']]) }}" 
                                           class="btn-custom-go btn-sm">
                                            <i class="mdi mdi-eye"></i> Lihat Pemantauan
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center">
                        <div class="alert alert-warning border-warning border-opacity-25 mb-0" style="border-radius: 8px; display: inline-block;">
                            <i class="mdi mdi-alert-outline me-2"></i>
                            @if(request('search') || request('jenis_audit'))
                                Tidak ada nomor surat tugas yang sesuai dengan filter yang dipilih.
                                <a href="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="alert-link text-decoration-none ms-1 font-weight-700">Reset filter</a>
                            @else
                                Belum ada nomor surat tugas yang memiliki rekomendasi.
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
@endsection
