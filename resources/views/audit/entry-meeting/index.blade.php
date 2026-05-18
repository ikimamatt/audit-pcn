@extends('layouts.vertical', ['title' => 'Entry Meeting'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
<style>
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
    color: #fff;
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
    color: #fff;
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

#responsive-datatable thead th {
    background: #f8fafd;
    color: #6b7a99;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 2px solid #e8edf5;
    padding: 13px 14px;
    white-space: nowrap;
}
#responsive-datatable tbody tr {
    transition: background .15s;
}
#responsive-datatable tbody tr:hover {
    background: #f4f8ff !important;
}
#responsive-datatable tbody td {
    padding: 13px 14px;
    vertical-align: middle;
    border-color: #f0f3f9;
    font-size: 0.875rem;
    color: #374151;
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
    max-width: 200px;
    word-break: break-word;
    line-height: 1.4;
}

/* No PKA */
.no-pka-badge {
    font-weight: 700;
    font-size: 0.82rem;
    color: #6366f1;
    background: #eef2ff;
    border-radius: 8px;
    padding: 4px 10px;
    display: inline-block;
    white-space: nowrap;
}

/* Auditee chip */
.auditee-chip {
    font-size: 0.82rem;
    font-weight: 600;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 7px;
}
.auditee-chip .dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2d6a9f, #3a8dcc);
    flex-shrink: 0;
}

/* Tanggal PKA */
.tgl-pka {
    font-size: 0.8rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
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
.btn-act-download { background: #dcfce7; color: #16a34a; }
.btn-act-download:hover { background: #bbf7d0; color: #15803d; }
.btn-act-delete   { background: #fee2e2; color: #dc2626; }
.btn-act-delete:hover { background: #fecaca; color: #b91c1c; }
.btn-act-approve { background: #dcfce7; color: #16a34a; }
.btn-act-approve:hover { background: #bbf7d0; color: #15803d; }
.btn-act-reject { background: #f3f4f6; color: #4b5563; }
.btn-act-reject:hover { background: #e5e7eb; color: #374151; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}
.empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; opacity: .4; }
</style>
@endsection

@section('content')

{{-- ===== HERO HEADER ===== --}}
<div class="em-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-account-group-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Entry Meeting</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Audit &rsaquo; Entry Meeting
            </div>
        </div>
        @canModifyData
        <a href="{{ route('audit.entry-meeting.create') }}" class="btn-add-em">
            <i class="mdi mdi-plus-circle"></i> Tambah Entry Meeting
        </a>
        @endcanModifyData
    </div>
</div>

{{-- ===== FILTER SECTION ===== --}}
<div class="card filter-card">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap" action="">
            <label for="bulan" class="mb-0 fw-semibold text-muted" style="font-size:0.85rem;">Filter Bulan (Planning Start):</label>
            <input type="month" name="bulan" id="bulan" class="form-control form-control-sm" style="max-width:200px; border-radius:8px;" value="{{ request('bulan') }}">
            <button type="submit" class="btn btn-sm" style="background:#1a3a5c; color:#fff; border-radius:8px; padding:5px 12px;">
                <i class="mdi mdi-filter-outline me-1"></i> Filter
            </button>
            <a href="{{ route('audit.entry-meeting.index') }}" class="btn btn-sm btn-light" style="border-radius:8px; padding:5px 12px;">
                <i class="mdi mdi-refresh me-1"></i> Reset
            </a>
        </form>
    </div>
</div>

{{-- ===== TABLE ===== --}}
@php $total = $data->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Entry Meeting</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} Data
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="responsive-datatable" class="table table-centered dt-responsive w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Surat Tugas</th>
                        <th>No PKA</th>
                        <th>Planned / Actual Date</th>
                        <th>Auditee</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td><span class="row-num">{{ $index + 1 }}</span></td>
                            <td>
                                <div class="no-surat">
                                    <i class="mdi mdi-file-document-outline me-1" style="color:#2d6a9f;"></i>
                                    @if($item->programKerjaAudit && $item->programKerjaAudit->perencanaanAudit)
                                        {{ $item->programKerjaAudit->perencanaanAudit->nomor_surat_tugas }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($item->programKerjaAudit)
                                    <span class="no-pka-badge">{{ $item->programKerjaAudit->no_pka }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="tgl-pka mb-1">
                                    <span class="badge bg-light text-dark border me-1" title="Planned Date" style="width:20px; text-align:center;">P</span>
                                    <i class="mdi mdi-calendar-outline" style="color:#9ca3af;"></i>
                                    {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}
                                </div>
                                <div class="tgl-pka">
                                    <span class="badge bg-light text-dark border me-1" title="Actual Date" style="width:20px; text-align:center;">A</span>
                                    @if($item->actual_meeting_date)
                                        <i class="mdi mdi-calendar-check" style="color:#10b981;"></i>
                                        {{ \Carbon\Carbon::parse($item->actual_meeting_date)->format('d M Y') }}
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border-0" style="font-size:0.7rem; padding:2px 6px;">Belum Dilaksanakan</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="auditee-chip">
                                    <span class="dot"></span>
                                    {{ $item->auditee->divisi ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($item->file_undangan)
                                        <a href="{{ asset('storage/' . $item->file_undangan) }}" target="_blank" class="btn btn-sm btn-light border d-flex align-items-center gap-1" style="font-size:0.75rem; border-radius:6px; padding: 2px 6px; width:fit-content;">
                                            <i class="mdi mdi-download text-primary"></i> Undangan
                                        </a>
                                    @endif
                                    @if($item->file_absensi)
                                        <a href="{{ asset('storage/' . $item->file_absensi) }}" target="_blank" class="btn btn-sm btn-light border d-flex align-items-center gap-1" style="font-size:0.75rem; border-radius:6px; padding: 2px 6px; width:fit-content;">
                                            <i class="mdi mdi-download text-success"></i> Absensi
                                        </a>
                                    @endif
                                    @if(!$item->file_undangan && !$item->file_absensi)
                                        <span class="text-muted" style="font-size:0.8rem;">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($item->status_approval == 'approved')
                                    <span class="badge bg-success-subtle text-success px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-check-circle me-1"></i>Approved (Final)</span>
                                @elseif($item->status_approval == 'approved_level1')
                                    <span class="badge bg-info-subtle text-info px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-check me-1"></i>Approved Level 1</span>
                                @elseif($item->status_approval == 'rejected')
                                    @php $reason = htmlspecialchars($item->rejection_reason_level2 ?? $item->rejection_reason, ENT_QUOTES); @endphp
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1" style="border-radius:6px; cursor:pointer;" onclick="showRejectionReason('{{ $reason }}')" title="Klik untuk melihat alasan">
                                        <i class="mdi mdi-close-circle me-1"></i>Rejected (Final)
                                    </span>
                                @elseif($item->status_approval == 'rejected_level1')
                                    @php $reason1 = htmlspecialchars($item->rejection_reason_level1, ENT_QUOTES); @endphp
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1" style="border-radius:6px; cursor:pointer;" onclick="showRejectionReason('{{ $reason1 }}')" title="Klik untuk melihat alasan">
                                        <i class="mdi mdi-close me-1"></i>Rejected Level 1
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1" style="border-radius:6px;"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-wrap">
                                    @canModifyData
                                    <a href="{{ route('audit.entry-meeting.edit', $item->id) }}"
                                       class="btn-act btn-act-edit" title="Edit Entry Meeting">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('audit.entry-meeting.destroy', $item->id) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn-act btn-act-delete"
                                                onclick="deleteData({{ $item->id }})"
                                                title="Hapus Entry Meeting">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                    @endcanModifyData
                                    
                                        @php
                                            $canApproveLvl1 = \App\Helpers\ApprovalHelper::canApproveLevel1($item);
                                            $canApproveLvl2 = \App\Helpers\ApprovalHelper::canApproveLevel2($item);
                                            $canReject      = \App\Helpers\ApprovalHelper::canReject($item);
                                        @endphp

                                        @if($canApproveLvl1 || $canApproveLvl2 || $canReject)
                                            <form id="approval-form-{{ $item->id }}" action="{{ route('audit.entry-meeting.approval', $item->id) }}" method="POST" class="m-0" style="display:inline-flex; gap:5px;">
                                                @csrf
                                                <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                
                                                @if($canApproveLvl1)
                                                    <button type="button" class="btn-act btn-act-approve" onclick="approveData({{ $item->id }})" title="Approve (Ketua Tim)">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                @elseif($canApproveLvl2)
                                                    <button type="button" class="btn-act btn-act-approve" onclick="approveData({{ $item->id }})" title="Approve Final (Koordinator)">
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty state handled below --}}
                    @endforelse
                </tbody>
            </table>
            @if($data->isEmpty())
                <div class="empty-state">
                    <i class="mdi mdi-account-group-outline"></i>
                    <p class="mb-0 fw-semibold">Belum ada data Entry Meeting</p>
                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah Entry Meeting</strong> untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showRejectionReason(reason) {
        Swal.fire({
            title: 'Alasan Penolakan',
            text: reason || 'Tidak ada alasan yang diberikan',
            icon: 'info',
            confirmButtonColor: '#1a3a5c',
            confirmButtonText: 'Tutup'
        });
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
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
            title: 'Approve Entry Meeting?',
            text: "Apakah Anda yakin ingin approve Entry Meeting ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Approve!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('action-' + id).value = 'approve';
                document.getElementById('approval-form-' + id).submit();
            }
        });
    }

    function approveDataPending(id) {
        Swal.fire({
            title: 'Tidak Dapat Approve',
            html: '<div class="text-start">' +
                  '<p><strong>Data belum diapprove oleh ASMAN SPI!</strong></p>' +
                  '<p>Untuk melakukan approval Level 2, data harus diapprove oleh ASMAN SPI terlebih dahulu (Level 1).</p>' +
                  '<p class="text-muted">Status saat ini: <strong>Pending</strong></p>' +
                  '</div>',
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Mengerti'
        });
    }

    function rejectData(id) {
        Swal.fire({
            title: 'Tolak Entry Meeting',
            html: `
                <div class="text-center mb-3">
                    <i class="mdi mdi-alert-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-3">Apakah Anda yakin ingin menolak Entry Meeting ini?</p>
                <div class="form-group">
                    <label for="rejection_reason" class="form-label text-start d-block">Alasan Penolakan *</label>
                    <textarea id="rejection_reason" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            focusConfirm: false,
            preConfirm: () => {
                const reason = document.getElementById('rejection_reason').value.trim();
                if (!reason) {
                    Swal.showValidationMessage('Alasan penolakan harus diisi');
                    return false;
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Add rejection reason to form
                const form = document.getElementById('approval-form-' + id);
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'rejection_reason';
                reasonInput.value = result.value;
                form.appendChild(reasonInput);
                
                document.getElementById('action-' + id).value = 'reject';
                form.submit();
            }
        });
    }
</script>
@endsection 