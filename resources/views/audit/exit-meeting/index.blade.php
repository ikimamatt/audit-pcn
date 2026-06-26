@extends('layouts.vertical', ['title' => 'Exit Meeting'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
        'node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'
     ])
<style>
.em-hero { background:#fff; border-radius:16px; padding:24px 28px; color:#1a3a5c; margin-bottom:24px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,0.07); }
.em-hero h2 { font-size:1.4rem; font-weight:700; margin-bottom:4px; letter-spacing:-0.5px; color:#1a3a5c; }
.em-hero .subtitle { font-size:0.85rem; color:#6b7a99; }
.btn-add-em { background:#1a3a5c; color:#fff; font-weight:600; border:none; border-radius:10px; padding:10px 22px; font-size:0.9rem; box-shadow:0 2px 10px rgba(26,58,92,0.18); transition:all .2s; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
.btn-add-em:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(26,58,92,0.25); color:#fff; background:#2d6a9f; }
.filter-card { border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,0.04); margin-bottom:24px; background:#fff; }
.table-card { border-radius:16px; border:none; box-shadow:0 2px 20px rgba(0,0,0,0.06); overflow:hidden; }
.table-card .card-header-custom { background:#fff; padding:20px 24px 0; border-bottom:1px solid #f0f0f0; }
.table-card .card-header-custom h5 { font-size:1rem; font-weight:700; color:#1a3a5c; }
#responsive-datatable thead th { background:#f8fafd; color:#6b7a99; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; border-bottom:2px solid #e8edf5; padding:13px 14px; white-space:nowrap; }
#responsive-datatable tbody tr { transition:background .15s; }
#responsive-datatable tbody tr:hover { background:#f4f8ff !important; }
#responsive-datatable tbody td { padding:12px 14px; vertical-align:middle; border-color:#f0f3f9; font-size:0.875rem; color:#374151; }
.row-num { font-size:0.78rem; font-weight:700; color:#9ca3af; background:#f9fafb; border-radius:6px; padding:3px 8px; display:inline-block; }
.no-surat { font-weight:600; font-size:0.82rem; color:#1a3a5c; background:#eef3fb; border-radius:8px; padding:4px 10px; display:inline-block; max-width:200px; word-break:break-word; line-height:1.4; }
.auditee-chip { font-size:0.82rem; font-weight:600; color:#374151; display:flex; align-items:center; gap:6px; }
.auditee-chip .dot { width:8px; height:8px; border-radius:50%; background:linear-gradient(135deg,#2d6a9f,#3a8dcc); flex-shrink:0; }
.date-pair { display:flex; flex-direction:column; gap:3px; }
.date-plan { font-size:0.75rem; color:#6b7280; }
.date-plan span { font-weight:600; color:#374151; }
.date-actual { font-size:0.75rem; }
.action-wrap { display:flex; gap:5px; align-items:center; flex-wrap:wrap; }
.btn-act { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.85rem; border:none; transition:all .2s; cursor:pointer; text-decoration:none; flex-shrink:0; }
.btn-act:hover { transform:translateY(-1px); box-shadow:0 3px 10px rgba(0,0,0,0.15); }
.btn-act-edit { background:#fef3c7; color:#d97706; }
.btn-act-edit:hover { background:#fde68a; color:#b45309; }
.btn-act-delete { background:#fee2e2; color:#dc2626; }
.btn-act-delete:hover { background:#fecaca; color:#b91c1c; }
.btn-act-approve { background:#dcfce7; color:#16a34a; }
.btn-act-approve:hover { background:#bbf7d0; color:#15803d; }
.btn-act-reject { background:#f3f4f6; color:#4b5563; }
.btn-act-reject:hover { background:#e5e7eb; color:#374151; }
.file-link { font-size:0.75rem; border-radius:6px; padding:2px 8px; display:inline-flex; align-items:center; gap:4px; text-decoration:none; border:1px solid; margin-bottom:2px; transition:all .15s; }
.file-link:hover { opacity:.8; }
.file-link-primary { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
.file-link-success { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }
.empty-state { text-align:center; padding:60px 20px; color:#9ca3af; }
.empty-state i { font-size:3rem; margin-bottom:12px; display:block; opacity:.4; }
</style>
@endsection

@section('content')

{{-- Hero --}}
<div class="em-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-account-group-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Exit Meeting</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Audit &rsaquo; Exit Meeting
            </div>
        </div>
        @canModifyData
        <a href="{{ route('audit.exit-meeting.create') }}" class="btn-add-em">
            <i class="mdi mdi-plus-circle"></i> Tambah Exit Meeting
        </a>
        @endcanModifyData
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px;">
        <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter --}}
<div class="card filter-card">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap" action="">
            <label for="bulan" class="mb-0 fw-semibold text-muted" style="font-size:0.85rem;">Filter Bulan (Planning Start):</label>
            <input type="month" name="bulan" id="bulan" class="form-control form-control-sm" style="max-width:200px; border-radius:8px;" value="{{ request('bulan') }}">
            <button type="submit" class="btn btn-sm" style="background:#1a3a5c; color:#fff; border-radius:8px; padding:5px 12px;">
                <i class="mdi mdi-filter-outline me-1"></i> Filter
            </button>
            <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-sm btn-light" style="border-radius:8px; padding:5px 12px;">
                <i class="mdi mdi-refresh me-1"></i> Reset
            </a>
        </form>
    </div>
</div>

{{-- Table --}}
@php $total = $realisasiAudits->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Exit Meeting</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} Data
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-centered dt-responsive w-100 mb-0" id="responsive-datatable">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Surat Tugas & Auditee</th>
                        <th>Jenis Audit</th>
                        <th>Tanggal Planning</th>
                        <th>Tanggal Aktual</th>
                        <th>Status Realisasi</th>
                        <th>Status Approval</th>
                        <th>Dokumen</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($realisasiAudits as $index => $ra)
                    <tr>
                        <td><span class="row-num">{{ $index + 1 }}</span></td>
                        <td>
                            <div class="no-surat mb-2">
                                <i class="mdi mdi-file-document-outline me-1" style="color:#2d6a9f;"></i>
                                {{ $ra->perencanaanAudit->nomor_surat_tugas ?? '-' }}
                            </div>
                            @if($ra->perencanaanAudit && $ra->perencanaanAudit->auditee)
                                <div class="auditee-chip">
                                    <span class="dot"></span>
                                    {{ $ra->perencanaanAudit->auditee->divisi }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:0.82rem; font-weight:600; color:#374151;">
                                {{ $ra->perencanaanAudit->jenis_audit ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @php
                                $firstMilestoneDate = '-'; $lastMilestoneDate = '-';
                                try {
                                    if($ra->perencanaanAudit && $ra->perencanaanAudit->programKerjaAudit && $ra->perencanaanAudit->programKerjaAudit->count() > 0) {
                                        $pka = $ra->perencanaanAudit->programKerjaAudit->first();
                                        $milestones = \App\Models\Models\Audit\PkaMilestone::where('program_kerja_audit_id', $pka->id)->get();
                                        if($milestones->count() > 0) {
                                            $firstMilestone = $milestones->sortBy('tanggal_mulai')->first();
                                            $lastMilestone  = $milestones->sortByDesc('tanggal_selesai')->first();
                                            if($firstMilestone) $firstMilestoneDate = \Carbon\Carbon::parse($firstMilestone->tanggal_mulai)->format('d M Y');
                                            if($lastMilestone)  $lastMilestoneDate  = \Carbon\Carbon::parse($lastMilestone->tanggal_selesai)->format('d M Y');
                                        }
                                    }
                                } catch(Exception $e) {}
                            @endphp
                            <div class="date-pair">
                                <div class="date-plan"><i class="mdi mdi-calendar-start text-info me-1"></i><span>{{ $firstMilestoneDate }}</span></div>
                                <div class="date-plan"><i class="mdi mdi-calendar-end text-warning me-1"></i><span>{{ $lastMilestoneDate }}</span></div>
                            </div>
                        </td>
                        <td>
                            <div class="date-pair">
                                <div class="date-plan"><i class="mdi mdi-calendar-check text-success me-1"></i>
                                    <span>{{ $ra->tanggal_mulai ? \Carbon\Carbon::parse($ra->tanggal_mulai)->format('d M Y') : '-' }}</span>
                                </div>
                                <div class="date-plan"><i class="mdi mdi-calendar-check text-success me-1"></i>
                                    <span>{{ $ra->tanggal_selesai ? \Carbon\Carbon::parse($ra->tanggal_selesai)->format('d M Y') : '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                [$stClass, $stIcon, $stText] = match($ra->status ?? '') {
                                    'selesai'     => ['bg-success-subtle text-success',   'mdi-check-circle-outline',  'Selesai'],
                                    'on progress' => ['bg-warning-subtle text-warning',   'mdi-progress-clock',        'Sedang Berlangsung'],
                                    'belum'       => ['bg-secondary-subtle text-secondary','mdi-clock-outline',         'Belum Dimulai'],
                                    default       => ['bg-info-subtle text-info',          'mdi-information-outline',   ucfirst($ra->status ?? '-')],
                                };
                            @endphp
                            <span class="badge {{ $stClass }} px-2 py-1" style="border-radius:6px; white-space:nowrap;">
                                <i class="mdi {{ $stIcon }} me-1"></i>{{ $stText }}
                            </span>
                        </td>
                        <td>
                            @php $statusApproval = $ra->status_approval ?? 'pending'; @endphp
                            @if($statusApproval == 'approved')
                                <span class="badge bg-success-subtle text-success px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-check-circle me-1"></i>Approved (Final)</span>
                            @elseif($statusApproval == 'approved_level1')
                                <span class="badge bg-info-subtle text-info px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-check me-1"></i>Approved Level 1</span>
                            @elseif($statusApproval == 'rejected')
                                @php $reason = htmlspecialchars($ra->rejection_reason_level2 ?? $ra->alasan_penolakan ?? '', ENT_QUOTES); @endphp
                                <span class="badge bg-danger-subtle text-danger px-2 py-1" style="border-radius:6px; cursor:pointer;" onclick="showRejectionReason('{{ $reason }}')" title="Klik untuk melihat alasan">
                                    <i class="mdi mdi-close-circle me-1"></i>Rejected (Final)
                                </span>
                            @elseif($statusApproval == 'rejected_level1')
                                @php $reason1 = htmlspecialchars($ra->rejection_reason_level1 ?? '', ENT_QUOTES); @endphp
                                <span class="badge bg-warning-subtle text-warning px-2 py-1" style="border-radius:6px; cursor:pointer;" onclick="showRejectionReason('{{ $reason1 }}')" title="Klik untuk melihat alasan">
                                    <i class="mdi mdi-close me-1"></i>Rejected Level 1
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if($ra->file_undangan)
                                    <a href="{{ asset('storage/' . $ra->file_undangan) }}" target="_blank" class="file-link file-link-primary">
                                        <i class="mdi mdi-file-pdf-box"></i> Undangan
                                    </a>
                                @endif
                                @if($ra->file_absensi)
                                    <a href="{{ asset('storage/' . $ra->file_absensi) }}" target="_blank" class="file-link file-link-success">
                                        <i class="mdi mdi-file-pdf-box"></i> Absensi
                                    </a>
                                @endif
                                @if(!$ra->file_undangan && !$ra->file_absensi)
                                    <span class="text-muted" style="font-size:0.8rem;">-</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="action-wrap">
                                @canModifyData
                                <a href="{{ route('audit.exit-meeting.edit', $ra->id) }}" class="btn-act btn-act-edit" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <form action="{{ route('audit.exit-meeting.destroy', $ra->id) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-act btn-act-delete btn-delete-swal" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                                @endcanModifyData
                                @php
                                    $canApproveLvl1 = \App\Helpers\ApprovalHelper::canApproveLevel1($ra);
                                    $canApproveLvl2 = \App\Helpers\ApprovalHelper::canApproveLevel2($ra);
                                    $canReject      = \App\Helpers\ApprovalHelper::canReject($ra);
                                @endphp

                                @if($canApproveLvl1 || $canApproveLvl2 || $canReject)
                                    <form id="approval-form-{{ $ra->id }}" action="{{ route('audit.exit-meeting.approval', $ra->id) }}" method="POST" class="m-0" style="display:inline-flex;gap:5px;">
                                        @csrf
                                        <input type="hidden" name="action" id="action-{{ $ra->id }}" value="">
                                        
                                        @if($canApproveLvl1)
                                            <button type="button" class="btn-act btn-act-approve" onclick="approveData('{{ $ra->id }}')" title="Approve (Ketua Tim)">
                                                <i class="mdi mdi-check"></i>
                                            </button>
                                        @elseif($canApproveLvl2)
                                            <button type="button" class="btn-act btn-act-approve" onclick="approveData('{{ $ra->id }}')" title="Approve Final (Koordinator)">
                                                <i class="mdi mdi-check-all"></i>
                                            </button>
                                        @endif

                                        @if($canReject)
                                            <button type="button" class="btn-act btn-act-reject" onclick="rejectData('{{ $ra->id }}')" title="Reject">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
            @if(count($realisasiAudits) === 0)
                <div class="empty-state">
                    <i class="mdi mdi-account-group-outline"></i>
                    <p class="mb-0 fw-semibold">Belum ada data Exit Meeting</p>
                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah Exit Meeting</strong> untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite(['resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showRejectionReason(reason) {
        Swal.fire({ title:'Alasan Penolakan', text:reason||'Tidak ada alasan yang diberikan', icon:'info', confirmButtonColor:'#1a3a5c', confirmButtonText:'Tutup' });
    }

    document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({ title:'Hapus Exit Meeting?', text:'Yakin ingin menghapus data ini?', icon:'warning',
                confirmButtonText:'Ya, Hapus', cancelButtonText:'Batal', showCancelButton:true,
                confirmButtonColor:'#d33', cancelButtonColor:'#3085d6'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    function approveData(id) {
        Swal.fire({ title:'Approve Exit Meeting?', text:'Apakah Anda yakin ingin approve Exit Meeting ini?', icon:'question',
            showCancelButton:true, confirmButtonColor:'#28a745', cancelButtonColor:'#6c757d',
            confirmButtonText:'Ya, Approve!', cancelButtonText:'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                document.getElementById('action-' + id).value = 'approve';
                document.getElementById('approval-form-' + id).submit();
            }
        });
    }

    function approveDataPending(id) {
        Swal.fire({ title:'Tidak Dapat Approve',
            html:'<p><strong>Data belum diapprove oleh ASMAN SPI!</strong></p><p>Approval Level 2 membutuhkan approval Level 1 terlebih dahulu.</p>',
            icon:'warning', confirmButtonColor:'#3085d6', confirmButtonText:'Mengerti' });
    }

    function rejectData(id) {
        Swal.fire({
            title:'Tolak Exit Meeting',
            html:`<p class="mb-3">Apakah Anda yakin ingin menolak Exit Meeting ini?</p>
                <div class="form-group"><label class="form-label text-start d-block">Alasan Penolakan *</label>
                <textarea id="rejection_reason" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea></div>`,
            icon:'warning', showCancelButton:true, confirmButtonText:'Tolak', cancelButtonText:'Batal',
            confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d', focusConfirm:false,
            preConfirm: () => {
                const r = document.getElementById('rejection_reason').value.trim();
                if (!r) { Swal.showValidationMessage('Alasan penolakan harus diisi'); return false; }
                return r;
            }
        }).then(r => {
            if (r.isConfirmed) {
                const form = document.getElementById('approval-form-' + id);
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'rejection_reason'; inp.value = r.value;
                form.appendChild(inp);
                document.getElementById('action-' + id).value = 'reject';
                form.submit();
            }
        });
    }
</script>
@endsection