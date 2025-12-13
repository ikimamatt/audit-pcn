@extends('layouts.vertical', ['title' => 'Walkthrough Audit'])

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
            <h4 class="page-title">Walkthrough Audit</h4>
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
                    <a href="{{ route('audit.walkthrough.index') }}" class="btn btn-secondary ms-2">
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
                @canModifyData
                <a href="{{ route('audit.walkthrough.create') }}" class="btn btn-primary mb-3">
                    <i class="mdi mdi-plus me-1"></i> Tambah Walkthrough
                </a>
                @endcanModifyData
                <div class="table-responsive">
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No PKA</th>
                                <th>Surat Tugas</th>
                                <th>Planned Date</th>
                                <th>Actual Date</th>
                                <th>Auditee</th>
                                <th>Hasil</th>
                                <th>File BPM</th>
                                <th>Status</th>
                                <th>Alasan Penolakan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->programKerjaAudit ? $item->programKerjaAudit->no_pka : '-' }}</td>
                                <td>{{ $item->programKerjaAudit && $item->programKerjaAudit->perencanaanAudit ? $item->programKerjaAudit->perencanaanAudit->nomor_surat_tugas : '-' }}</td>
                                <td>{{ $item->planned_walkthrough_date ? \Carbon\Carbon::parse($item->planned_walkthrough_date)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($item->actual_walkthrough_date)
                                        {{ \Carbon\Carbon::parse($item->actual_walkthrough_date)->format('d/m/Y') }}
                                    @else
                                        <span class="badge bg-secondary">Belum Dilaksanakan</span>
                                    @endif
                                </td>
                                <td>{{ $item->auditee_nama }}</td>
                                <td>
                                    @if($item->hasil_walkthrough)
                                        <span title="{{ $item->hasil_walkthrough }}">{{ Str::limit($item->hasil_walkthrough, 50) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->file_bpm)
                                        <a href="{{ asset('storage/' . $item->file_bpm) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-eye"></i> View
                                        </a>
                                        <a href="{{ asset('storage/' . $item->file_bpm) }}" download class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status_approval == 'approved')
                                        <span class="badge bg-success">Approved (Final)</span>
                                    @elseif($item->status_approval == 'approved_level1')
                                        <span class="badge bg-info">Approved Level 1</span>
                                    @elseif($item->status_approval == 'rejected')
                                        <span class="badge bg-danger">Rejected (Final)</span>
                                    @elseif($item->status_approval == 'rejected_level1')
                                        <span class="badge bg-warning">Rejected Level 1</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status_approval == 'rejected' && $item->rejection_reason_level2)
                                        <span class="text-danger" title="{{ $item->rejection_reason_level2 }}">
                                            Level 2: {{ Str::limit($item->rejection_reason_level2, 30) }}
                                        </span>
                                    @elseif($item->status_approval == 'rejected_level1' && $item->rejection_reason_level1)
                                        <span class="text-danger" title="{{ $item->rejection_reason_level1 }}">
                                            Level 1: {{ Str::limit($item->rejection_reason_level1, 30) }}
                                        </span>
                                    @elseif($item->status_approval == 'rejected' && $item->rejection_reason)
                                        <span class="text-danger" title="{{ $item->rejection_reason }}">
                                            {{ Str::limit($item->rejection_reason, 30) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('audit.walkthrough.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('audit.walkthrough.destroy', $item->id) }}" method="POST" style="display:inline-block" class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-delete-swal">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </form>
                                    @canApproveReject
                                        @if($item->status_approval == 'pending')
                                            {{-- Level 1: ASMAN KSPI can approve/reject --}}
                                            @isAsmanKspi
                                                <form action="{{ route('audit.walkthrough.approval', $item->id) }}" method="POST" style="display:inline-block" id="approval-form-{{ $item->id }}">
                                                    @csrf
                                                    <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    <button type="button" class="btn btn-success btn-sm btn-approve-swal" data-id="{{ $item->id }}">
                                                        <i class="mdi mdi-check"></i> Approve Level 1
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm btn-reject-swal" data-id="{{ $item->id }}">
                                                        <i class="mdi mdi-close"></i> Reject Level 1
                                                    </button>
                                                </form>
                                            @endisAsmanKspi
                                            {{-- Level 2: KSPI can approve/reject from pending (if no ASMAN KSPI user exists) --}}
                                            @isKspi
                                                @php
                                                    $hasAsmanKspi = \App\Helpers\AuthHelper::hasAsmanKspiUsers();
                                                @endphp
                                                <form action="{{ route('audit.walkthrough.approval', $item->id) }}" method="POST" style="display:inline-block" id="approval-form-{{ $item->id }}">
                                                    @csrf
                                                    <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    @if($hasAsmanKspi)
                                                        <button type="button" class="btn btn-success btn-sm btn-approve-pending-swal" data-id="{{ $item->id }}" title="Data harus diapprove oleh ASMAN KSPI terlebih dahulu">
                                                            <i class="mdi mdi-check"></i> Approve Level 2
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-success btn-sm btn-approve-swal" data-id="{{ $item->id }}" title="Approve langsung (tidak ada ASMAN KSPI)">
                                                            <i class="mdi mdi-check"></i> Approve
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-danger btn-sm btn-reject-swal" data-id="{{ $item->id }}">
                                                        <i class="mdi mdi-close"></i> Reject Level 2
                                                    </button>
                                                </form>
                                            @endisKspi
                                        @elseif($item->status_approval == 'approved_level1')
                                            {{-- Level 2: KSPI can approve/reject after level 1 --}}
                                            @isKspi
                                                <form action="{{ route('audit.walkthrough.approval', $item->id) }}" method="POST" style="display:inline-block" id="approval-form-{{ $item->id }}">
                                                    @csrf
                                                    <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    <button type="button" class="btn btn-success btn-sm btn-approve-swal" data-id="{{ $item->id }}">
                                                        <i class="mdi mdi-check"></i> Approve Level 2
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm btn-reject-swal" data-id="{{ $item->id }}">
                                                        <i class="mdi mdi-close"></i> Reject Level 2
                                                    </button>
                                                </form>
                                            @endisKspi
                                        @elseif($item->status_approval == 'rejected_level1')
                                            {{-- Level 2: KSPI can reject after ASMAN KSPI reject (berjenjang) --}}
                                            @isKspi
                                                <form action="{{ route('audit.walkthrough.approval', $item->id) }}" method="POST" style="display:inline-block" id="approval-form-{{ $item->id }}">
                                                    @csrf
                                                    <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    <button type="button" class="btn btn-danger btn-sm btn-reject-swal" data-id="{{ $item->id }}">
                                                        <i class="mdi mdi-close"></i> Reject Level 2
                                                    </button>
                                                </form>
                                            @endisKspi
                                        @endif
                                    @endcanApproveReject
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
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = btn.closest('form');

                Swal.fire({
                    title: 'Hapus Walkthrough?',
                    text: 'Yakin ingin menghapus walkthrough ini?',
                    icon: 'warning',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Approve confirmation
        document.querySelectorAll('.btn-approve-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const itemId = btn.dataset.id;
                const form = document.getElementById(`approval-form-${itemId}`);
                const hiddenInputAction = document.getElementById(`action-${itemId}`);

                Swal.fire({
                    title: 'Approve Walkthrough?',
                    text: 'Yakin ingin approve walkthrough ini?',
                    icon: 'question',
                    confirmButtonText: 'Ya, Approve',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                }).then((result) => {
                    if (result.isConfirmed) {
                        hiddenInputAction.value = 'approve';
                        form.submit();
                    }
                });
            });
        });

        // Approve pending notification (when ASMAN KSPI exists)
        document.querySelectorAll('.btn-approve-pending-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
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
            });
        });

        // Reject confirmation with reason input
        document.querySelectorAll('.btn-reject-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const itemId = btn.dataset.id;
                const form = document.getElementById(`approval-form-${itemId}`);
                const hiddenInputAction = document.getElementById(`action-${itemId}`);

                Swal.fire({
                    title: 'Tolak Walkthrough',
                    html: `
                        <div class="text-center mb-3">
                            <i class="mdi mdi-alert-circle text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <p class="mb-3">Apakah Anda yakin ingin menolak walkthrough ini?</p>
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
                        const reasonInput = document.createElement('input');
                        reasonInput.type = 'hidden';
                        reasonInput.name = 'rejection_reason';
                        reasonInput.value = result.value;
                        form.appendChild(reasonInput);
                        
                        hiddenInputAction.value = 'reject';
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection 