@extends('layouts.vertical', ['title' => 'Exit Meeting'])

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
                @canModifyData
                <a href="{{ route('audit.exit-meeting.create') }}" class="btn btn-primary">
                @endcanModifyData
                    <i class="mdi mdi-plus"></i> Tambah Exit Meeting
                </a>
            </div>
            <h4 class="page-title">Exit Meeting</h4>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-3 d-flex align-items-center" action="">
                    <label for="bulan" class="me-2 mb-0">Filter Bulan (Planning Start):</label>
                    <input type="month" name="bulan" id="bulan" class="form-control me-2" style="max-width:200px;" value="{{ request('bulan') }}">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-secondary ms-2">
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
                <div class="table-responsive">
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat Tugas</th>
                                <th>Auditee</th>
                                <th>Jenis Audit</th>
                                <th>Planning Start</th>
                                <th>Planning Finish</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Status Approval</th>
                                <th>Alasan Penolakan</th>
                                <th>File Undangan & Absensi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($realisasiAudits as $index => $realisasiAudit)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($realisasiAudit->perencanaanAudit)
                                            <strong>{{ $realisasiAudit->perencanaanAudit->nomor_surat_tugas }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($realisasiAudit->perencanaanAudit && $realisasiAudit->perencanaanAudit->auditee)
                                            <strong>{{ $realisasiAudit->perencanaanAudit->auditee->divisi }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $realisasiAudit->perencanaanAudit ? $realisasiAudit->perencanaanAudit->jenis_audit : '-' }}</td>
                                    <td>
                                        @php
                                            $firstMilestoneDate = '-';
                                            try {
                                                if($realisasiAudit->perencanaanAudit && $realisasiAudit->perencanaanAudit->programKerjaAudit && $realisasiAudit->perencanaanAudit->programKerjaAudit->count() > 0) {
                                                    $pka = $realisasiAudit->perencanaanAudit->programKerjaAudit->first();
                                                    // Load milestones manually
                                                    $milestones = \App\Models\Models\Audit\PkaMilestone::where('program_kerja_audit_id', $pka->id)->get();
                                                    if($milestones->count() > 0) {
                                                        $firstMilestone = $milestones->sortBy('tanggal_mulai')->first();
                                                        if($firstMilestone) {
                                                            $firstMilestoneDate = \Carbon\Carbon::parse($firstMilestone->tanggal_mulai)->format('d M Y');
                                                        }
                                                    }
                                                }
                                            } catch(Exception $e) {
                                                $firstMilestoneDate = '-';
                                            }
                                        @endphp
                                        @if($firstMilestoneDate != '-')
                                            <span class="badge bg-info">{{ $firstMilestoneDate }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $lastMilestoneDate = '-';
                                            try {
                                                if($realisasiAudit->perencanaanAudit && $realisasiAudit->perencanaanAudit->programKerjaAudit && $realisasiAudit->perencanaanAudit->programKerjaAudit->count() > 0) {
                                                    $pka = $realisasiAudit->perencanaanAudit->programKerjaAudit->first();
                                                    // Load milestones manually
                                                    $milestones = \App\Models\Models\Audit\PkaMilestone::where('program_kerja_audit_id', $pka->id)->get();
                                                    if($milestones->count() > 0) {
                                                        $lastMilestone = $milestones->sortByDesc('tanggal_selesai')->first();
                                                        if($lastMilestone) {
                                                            $lastMilestoneDate = \Carbon\Carbon::parse($lastMilestone->tanggal_selesai)->format('d M Y');
                                                        }
                                                    }
                                                }
                                            } catch(Exception $e) {
                                                $lastMilestoneDate = '-';
                                            }
                                        @endphp
                                        @if($lastMilestoneDate != '-')
                                            <span class="badge bg-info">{{ $lastMilestoneDate }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $realisasiAudit->tanggal_mulai ? \Carbon\Carbon::parse($realisasiAudit->tanggal_mulai)->format('d M Y') : '-' }}</td>
                                    <td>{{ $realisasiAudit->tanggal_selesai ? \Carbon\Carbon::parse($realisasiAudit->tanggal_selesai)->format('d M Y') : '-' }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch($realisasiAudit->status) {
                                                case 'selesai':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Selesai';
                                                    break;
                                                case 'on progress':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Sedang Berlangsung';
                                                    break;
                                                case 'belum':
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = 'Belum Dimulai';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-info';
                                                    $statusText = ucfirst($realisasiAudit->status);
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusApprovalClass = '';
                                            $statusApprovalText = '';
                                            $currentStatusApproval = $realisasiAudit->status_approval ?? 'pending';
                                            switch($currentStatusApproval) {
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
                                                case 'pending':
                                                    $statusApprovalClass = 'bg-warning';
                                                    $statusApprovalText = 'Pending';
                                                    break;
                                                default:
                                                    $statusApprovalClass = 'bg-secondary';
                                                    $statusApprovalText = 'Pending';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusApprovalClass }}">{{ $statusApprovalText }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $currentStatusApproval = $realisasiAudit->status_approval ?? 'pending';
                                        @endphp
                                        @if($currentStatusApproval == 'rejected' && $realisasiAudit->rejection_reason_level2)
                                            <span class="text-danger" title="{{ $realisasiAudit->rejection_reason_level2 }}">
                                                Level 2: {{ Str::limit($realisasiAudit->rejection_reason_level2, 30) }}
                                            </span>
                                        @elseif($currentStatusApproval == 'rejected_level1' && $realisasiAudit->rejection_reason_level1)
                                            <span class="text-danger" title="{{ $realisasiAudit->rejection_reason_level1 }}">
                                                Level 1: {{ Str::limit($realisasiAudit->rejection_reason_level1, 30) }}
                                            </span>
                                        @elseif($realisasiAudit->alasan_penolakan)
                                            <span class="badge bg-danger">{{ Str::limit($realisasiAudit->alasan_penolakan, 30) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($realisasiAudit->file_undangan || $realisasiAudit->file_absensi)
                                            <div class="btn-group" role="group">
                                                @if($realisasiAudit->file_undangan)
                                                                    <a href="{{ asset('storage/' . $realisasiAudit->file_undangan) }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="mdi mdi-file-pdf-box"></i> Undangan
                </a>
                                                @endif
                                                @if($realisasiAudit->file_absensi)
                                                                    <a href="{{ asset('storage/' . $realisasiAudit->file_absensi) }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline-success">
                    <i class="mdi mdi-file-pdf-box"></i> Absensi
                </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @canModifyData
                                        <a href="{{ route('audit.exit-meeting.edit', $realisasiAudit->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="mdi mdi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('audit.exit-meeting.destroy', $realisasiAudit->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-delete-swal">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                        </form>
                                        @endcanModifyData
                                        @canApproveReject
                                            @php
                                                $statusApproval = $realisasiAudit->status_approval ?? 'pending';
                                            @endphp
                                            @if($statusApproval == 'pending')
                                                {{-- Level 1: ASMAN SPI can approve/reject --}}
                                                @isAsmanSpi
                                                    <form id="approval-form-{{ $realisasiAudit->id }}" action="{{ route('audit.exit-meeting.approval', $realisasiAudit->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-success" onclick="approveData({{ $realisasiAudit->id }})">
                                                            <i class="mdi mdi-check"></i> Approve Level 1
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary" onclick="rejectData({{ $realisasiAudit->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 1
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $realisasiAudit->id }}" value="">
                                                    </form>
                                                @endisAsmanSpi
                                                {{-- Level 2: KSPI can approve/reject from pending (if no ASMAN SPI user exists) --}}
                                                @isKspi
                                                    @php
                                                        $hasAsmanSpi = \App\Helpers\AuthHelper::hasAsmanSpiUsers();
                                                    @endphp
                                                    <form id="approval-form-{{ $realisasiAudit->id }}" action="{{ route('audit.exit-meeting.approval', $realisasiAudit->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        @if($hasAsmanSpi)
                                                            <button type="button" class="btn btn-sm btn-success" onclick="approveDataPending({{ $realisasiAudit->id }})" title="Data harus diapprove oleh ASMAN SPI terlebih dahulu">
                                                                <i class="mdi mdi-check"></i> Approve Level 2
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-success" onclick="approveData({{ $realisasiAudit->id }})" title="Approve langsung (tidak ada ASMAN SPI)">
                                                                <i class="mdi mdi-check"></i> Approve
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="rejectData({{ $realisasiAudit->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $realisasiAudit->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @elseif($statusApproval == 'approved_level1')
                                                {{-- Level 2: KSPI can approve/reject after level 1 --}}
                                                @isKspi
                                                    <form id="approval-form-{{ $realisasiAudit->id }}" action="{{ route('audit.exit-meeting.approval', $realisasiAudit->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-success" onclick="approveData({{ $realisasiAudit->id }})">
                                                            <i class="mdi mdi-check"></i> Approve Level 2
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary" onclick="rejectData({{ $realisasiAudit->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $realisasiAudit->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @elseif($statusApproval == 'rejected_level1')
                                                {{-- Level 2: KSPI can reject after ASMAN SPI reject (berjenjang) --}}
                                                @isKspi
                                                    <form id="approval-form-{{ $realisasiAudit->id }}" action="{{ route('audit.exit-meeting.approval', $realisasiAudit->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="rejectData({{ $realisasiAudit->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $realisasiAudit->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @endif
                                        @endcanApproveReject
                                    </td>
                                </tr>
                            @empty
                                {{-- DataTables will show emptyTable message automatically --}}
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
    @vite([ 'resources/js/pages/datatable.init.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Delete confirmation
        document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = btn.closest('form');
        
                Swal.fire({
                    title: 'Hapus Exit Meeting?',
                    text: 'Yakin ingin menghapus data exit meeting ini?',
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

        // Approval functions
        function approveData(id) {
            Swal.fire({
                title: 'Approve Exit Meeting?',
                text: "Apakah Anda yakin ingin approve Exit Meeting ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('approval-form-' + id);
                    document.getElementById('action-' + id).value = 'approve';
                    form.submit();
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
                title: 'Tolak Exit Meeting',
                html: `
                    <div class="text-center mb-3">
                        <i class="mdi mdi-alert-circle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="mb-3">Apakah Anda yakin ingin menolak Exit Meeting ini?</p>
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
                    const form = document.getElementById('approval-form-' + id);
                    document.getElementById('action-' + id).value = 'reject';
                    
                    // Tambahkan input untuk rejection reason
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'rejection_reason';
                    reasonInput.value = result.value;
                    form.appendChild(reasonInput);
                    
                    form.submit();
                }
            });
        }
    </script>
@endsection 