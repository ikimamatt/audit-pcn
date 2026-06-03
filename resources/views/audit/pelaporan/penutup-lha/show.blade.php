@extends('layouts.vertical', ['title' => 'Detail Rekomendasi Penutup LHA/LHK'])

@section('css')
    <style>
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

        .card {
            border: 1px solid var(--corp-neutral-border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }
        
        .card-header {
            background-color: var(--corp-neutral-light) !important;
            border-bottom: 1px solid var(--corp-neutral-border);
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }

        .btn-custom {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding: 8px 16px;
            display: inline-flex;
            align-items: center;
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

        .detail-section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--corp-navy-primary);
            letter-spacing: 1px;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--corp-navy-subtle);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
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
            margin-bottom: 1.25rem;
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
            gap: 6px;
        }

        .detail-block-value {
            font-size: 13px;
            color: var(--corp-neutral-text);
            line-height: 1.6;
            white-space: pre-line;
        }

        .pic-card-wrapper {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pic-corp-card {
            background-color: #ffffff;
            border: 1px solid var(--corp-neutral-border);
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            gap: 4px;
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
            align-self: flex-start;
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
            font-size: 12px;
            line-height: 1.3;
        }

        .pic-dept {
            color: var(--corp-neutral-text-muted);
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

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

        .rejection-box {
            background-color: var(--corp-danger-subtle) !important;
            border: 1px solid rgba(220, 38, 38, 0.3) !important;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 1.25rem;
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

        .timeline-container {
            position: relative;
            padding-left: 20px;
            border-left: 2px solid var(--corp-neutral-border);
            margin-left: 10px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--corp-navy-primary);
            border: 2px solid #ffffff;
        }

        .timeline-item.closed::before {
            background-color: var(--corp-sage-primary);
        }

        .timeline-item.on-progress::before {
            background-color: var(--corp-navy-light);
        }

        .timeline-date {
            font-size: 11px;
            font-weight: 700;
            color: var(--corp-neutral-text-muted);
            margin-bottom: 2px;
        }

        .timeline-content {
            background-color: var(--corp-neutral-light);
            border: 1px solid var(--corp-neutral-border);
            border-radius: 8px;
            padding: 12px 14px;
        }

        .timeline-komentar {
            font-size: 13px;
            color: var(--corp-neutral-text);
            line-height: 1.5;
            white-space: pre-line;
        }
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
                        <li class="breadcrumb-item"><a href="{{ route('audit.penutup-lha-rekomendasi.index') }}" class="text-muted">Penutup LHA/LHK</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Detail</li>
                    </ol>
                </nav>
                <h2><i class="mdi mdi-information-outline me-2 text-primary"></i>Detail Rekomendasi Penutup LHA/LHK</h2>
                <div class="subtitle">Melihat detail rekomendasi, PIC, dan riwayat tindak lanjut.</div>
            </div>
            <div>
                <a href="{{ route('audit.penutup-lha-rekomendasi.index', ['nomor_surat_tugas' => $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas ?? '']) }}" class="btn btn-custom btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
                @canModifyData
                <a href="{{ route('audit.penutup-lha-rekomendasi.edit', $item->id) }}" class="btn btn-custom btn-primary ms-1">
                    <i class="mdi mdi-pencil"></i> Edit Rekomendasi
                </a>
                @endcanModifyData
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <!-- Card Detail Rekomendasi -->
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark font-weight-700">
                    <i class="mdi mdi-file-document-outline me-1 text-primary"></i> Data Utama Rekomendasi
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Banner Penolakan -->
                @if(in_array($item->status_approval, ['rejected', 'rejected_level1']) && ($item->rejection_reason_level2 ?? $item->rejection_reason_level1 ?? $item->alasan_reject))
                    <div class="rejection-box">
                        <div class="rejection-box-title">
                            <i class="mdi mdi-alert-circle-outline"></i> Alasan Penolakan
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

                <div class="detail-section-title">
                    <i class="mdi mdi-information-outline"></i> Ringkasan Dokumen
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
                        <div class="detail-item-value">{{ $item->target_waktu ? \Carbon\Carbon::parse($item->target_waktu)->format('d/m/Y') : '-' }}</div>
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

                <div class="detail-section-title">
                    <i class="mdi mdi-file-document-outline"></i> Deskripsi Rekomendasi
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

                <!-- Approval Actions for SPI/ASMAN -->
                @php
                    $canApproveLvl1 = \App\Helpers\ApprovalHelper::canApproveLevel1($item);
                    $canApproveLvl2 = \App\Helpers\ApprovalHelper::canApproveLevel2($item);
                    $canReject      = \App\Helpers\ApprovalHelper::canReject($item);
                @endphp

                @if($canApproveLvl1 || $canApproveLvl2 || $canReject)
                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        @if($canApproveLvl1)
                            <button type="button" class="btn btn-custom btn-outline-success" onclick="approveData()">
                                <i class="mdi mdi-check"></i> Approve Level 1
                            </button>
                        @elseif($canApproveLvl2)
                            <button type="button" class="btn btn-custom btn-outline-success" onclick="approveData()">
                                <i class="mdi mdi-check-all"></i> Approve Final
                            </button>
                        @endif

                        @if($canReject)
                            <button type="button" class="btn btn-custom btn-outline-danger" onclick="rejectData()">
                                <i class="mdi mdi-close"></i> Reject
                            </button>
                        @endif
                    </div>

                    <!-- Single Approval Form -->
                    <form id="approval-form" action="{{ route('audit.penutup-lha-rekomendasi.approval', $item->id) }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="action" id="action-input" value="">
                        <input type="hidden" name="rejection_reason" id="rejection-reason-input" value="">
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <!-- Card PIC & Tindak Lanjut -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-dark font-weight-700">
                    <i class="mdi mdi-account-group-outline me-1 text-primary"></i> Person In Charge (PIC)
                </h5>
            </div>
            <div class="card-body">
                @php
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
            </div>
        </div>

        <!-- Card Riwayat Tindak Lanjut -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 text-dark font-weight-700">
                    <i class="mdi mdi-history me-1 text-primary"></i> Riwayat Tindak Lanjut
                </h5>
                @php
                    $currentUserId = \App\Helpers\AuthHelper::getCurrentUserId();
                    $isBusinessContact = $item->picUsers()
                        ->where('master_user_id', $currentUserId)
                        ->wherePivot('pic_type', 'business_contact')
                        ->exists();
                    $isSuperAdmin = \App\Helpers\AuthHelper::isSuperAdmin();
                    $canInputTindakLanjut = $isBusinessContact || $isSuperAdmin;
                @endphp

                @if($canInputTindakLanjut)
                    <a href="{{ route('audit.penutup-lha-rekomendasi.tindak-lanjut.form', $item->id) }}" class="btn btn-xs btn-primary py-1 px-2 fs-11">
                        <i class="mdi mdi-plus-circle"></i> Input Tindak Lanjut
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($item->tindakLanjut && $item->tindakLanjut->count() > 0)
                    <div class="timeline-container">
                        @foreach($item->tindakLanjut->sortByDesc('created_at') as $tl)
                            @php
                                $tlStatusClass = '';
                                switch($tl->status_tindak_lanjut) {
                                    case 'closed':
                                        $tlStatusClass = 'closed';
                                        break;
                                    case 'on_progress':
                                        $tlStatusClass = 'on-progress';
                                        break;
                                    default:
                                        $tlStatusClass = 'open';
                                }
                            @endphp
                            <div class="timeline-item {{ $tlStatusClass }}">
                                <div class="timeline-date">
                                    {{ \Carbon\Carbon::parse($tl->created_at)->format('d F Y H:i') }}
                                    @if($tl->real_waktu)
                                        <span class="text-success ms-1">(Realisasi: {{ \Carbon\Carbon::parse($tl->real_waktu)->format('d/m/Y') }})</span>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                        <div>
                                            @if($tl->status_tindak_lanjut == 'closed')
                                                <span class="badge bg-success">Closed</span>
                                            @elseif($tl->status_tindak_lanjut == 'on_progress')
                                                <span class="badge bg-info">On Progress</span>
                                            @else
                                                <span class="badge bg-warning">Open</span>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('audit.penutup-lha-tindak-lanjut.edit', $tl->id) }}" class="btn btn-xs btn-outline-warning py-0.5 px-1.5 fs-10" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('audit.penutup-lha-tindak-lanjut.destroy', $tl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tindak lanjut ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger py-0.5 px-1.5 fs-10" title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-komentar mb-2">
                                        {{ $tl->komentar }}
                                    </div>

                                    @if($tl->file_eviden)
                                        <div class="mt-2 pt-2 border-top">
                                            <a href="{{ asset('storage/' . $tl->file_eviden) }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2 fs-11">
                                                <i class="mdi mdi-download me-1"></i>Unduh Eviden
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-history text-muted fs-24 mb-2 d-block"></i>
                        <p class="text-muted mb-0">Belum ada riwayat tindak lanjut untuk rekomendasi ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function approveData() {
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
                document.getElementById('action-input').value = 'approve';
                document.getElementById('approval-form').submit();
            }
        });
    }

    function rejectData() {
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
                document.getElementById('action-input').value = 'reject';
                document.getElementById('rejection-reason-input').value = result.value;
                document.getElementById('approval-form').submit();
            }
        });
    }
</script>
@endsection
