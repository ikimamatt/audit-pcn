@extends('layouts.vertical', ['title' => 'Penutup LHA/LHK'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
    <style>
        .btn-custom {
            transition: all 0.3s ease;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border-color: #007bff;
            transform: translateY(-1px);
        }
        
        .btn-outline-success:hover {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border-color: #28a745;
            transform: translateY(-1px);
        }
        
        .btn-outline-warning:hover {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            border-color: #ffc107;
            transform: translateY(-1px);
        }
        
        .btn-outline-danger:hover {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border-color: #dc3545;
            transform: translateY(-1px);
        }
        
        .btn-outline-info:hover {
            background: linear-gradient(45deg, #17a2b8, #138496);
            border-color: #17a2b8;
            transform: translateY(-1px);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Audit</a></li>
                    <li class="breadcrumb-item active">Penutup LHA/LHK</li>
                </ol>
            </div>
            <h4 class="page-title">
                <i class="mdi mdi-file-document-outline me-2"></i>
                Penutup LHA/LHK
            </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-table me-2"></i>
                            Data Rekomendasi Penutup LHA/LHK
                        </h5>
                        @if(isset($nomorSuratTugas) && $nomorSuratTugas)
                            <div class="mt-2">
                                <span class="badge bg-info">
                                    <i class="mdi mdi-file-document-outline me-1"></i>
                                    Nomor Surat Tugas: {{ $nomorSuratTugas }}
                                </span>
                                @if($perencanaanAudit)
                                    <span class="badge bg-secondary ms-2">
                                        {{ $perencanaanAudit->jenis_audit }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('audit.penutup-lha-rekomendasi.select-nomor-surat-tugas') }}" class="btn btn-secondary me-2" style="border-radius: 25px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="mdi mdi-file-document-outline me-2"></i>
                            Pilih Nomor Surat Tugas
                        </a>
                        @canModifyData
                        <a href="{{ route('audit.penutup-lha-rekomendasi.create', ['pelaporan_isi_lha_id' => $isiLhaId, 'nomor_surat_tugas' => $nomorSuratTugas ?? '']) }}" class="btn btn-primary" style="border-radius: 25px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Tambah Rekomendasi
                        </a>
                        @endcanModifyData
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    @include('components.alert')
                @endif

                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('audit.penutup-lha-rekomendasi.index') }}" class="row g-3">
                            @if(isset($nomorSuratTugas) && $nomorSuratTugas)
                                <input type="hidden" name="nomor_surat_tugas" value="{{ $nomorSuratTugas }}">
                            @endif
                            <div class="col-md-3">
                                <label for="status_approval" class="form-label">Status Approval</label>
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
                                <label for="search" class="form-label">Cari</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Cari rekomendasi..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="pic" class="form-label">PIC</label>
                                <input type="text" name="pic" id="pic" class="form-control" placeholder="Cari PIC..." value="{{ request('pic') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; font-weight: 500;">
                                        <i class="mdi mdi-magnify me-2"></i>
                                        Filter Data
                                    </button>
                                    <a href="{{ route('audit.penutup-lha-rekomendasi.index') }}" class="btn btn-outline-secondary" style="border-radius: 25px; font-weight: 500;">
                                        <i class="mdi mdi-refresh me-2"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor ISS</th>
                                <th>Rekomendasi</th>
                                <th>PIC</th>
                                <th>Target Waktu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;" title="{{ $item->temuan->nomor_iss ?? '-' }}">
                                        {{ $item->temuan->nomor_iss ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $item->rekomendasi }}">
                                        {{ Str::limit($item->rekomendasi, 50) }}
                                    </div>
                                </td>
                                <td>
                                    @if($item->picUsers->count() > 0)
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($item->picUsers as $picUser)
                                                <span class="badge bg-info text-dark">
                                                    {{ $picUser->nama }} - {{ $picUser->auditee->divisi ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">{{ $item->pic_rekomendasi ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->target_waktu }}</td>
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
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm mb-1 btn-custom" 
                                                title="View Detail"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalView{{ $item->id }}">
                                            <i class="mdi mdi-eye me-1"></i>View
                                        </button>
                                        
                                        @canModifyData
                                        <a href="{{ route('audit.penutup-lha-rekomendasi.edit', $item->id) }}" 
                                           class="btn btn-outline-primary btn-sm mb-1 btn-custom" 
                                           title="Edit">
                                            <i class="mdi mdi-pencil me-1"></i>Edit
                                        </a>
                                        @endcanModifyData
                                        
                                        @canApproveReject
                                            @if($item->status_approval == 'pending')
                                                {{-- Level 1: ASMAN KSPI can approve/reject --}}
                                                @isAsmanKspi
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.penutup-lha-rekomendasi.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-success mb-1 btn-custom" onclick="approveData({{ $item->id }})">
                                                            <i class="mdi mdi-check me-1"></i> Approve Level 1
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close me-1"></i> Reject Level 1
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    </form>
                                                @endisAsmanKspi
                                                {{-- Level 2: KSPI can approve/reject from pending (if no ASMAN KSPI user exists) --}}
                                                @isKspi
                                                    @php
                                                        $hasAsmanKspi = \App\Helpers\AuthHelper::hasAsmanKspiUsers();
                                                    @endphp
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.penutup-lha-rekomendasi.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        @if($hasAsmanKspi)
                                                            <button type="button" class="btn btn-sm btn-success mb-1 btn-custom" onclick="approveDataPending({{ $item->id }})" title="Data harus diapprove oleh ASMAN KSPI terlebih dahulu">
                                                                <i class="mdi mdi-check me-1"></i> Approve Level 2
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-success mb-1 btn-custom" onclick="approveData({{ $item->id }})" title="Approve langsung (tidak ada ASMAN KSPI)">
                                                                <i class="mdi mdi-check me-1"></i> Approve
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-danger mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close me-1"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @elseif($item->status_approval == 'approved_level1')
                                                {{-- Level 2: KSPI can approve/reject after level 1 --}}
                                                @isKspi
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.penutup-lha-rekomendasi.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-success mb-1 btn-custom" onclick="approveData({{ $item->id }})">
                                                            <i class="mdi mdi-check me-1"></i> Approve Level 2
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close me-1"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @elseif($item->status_approval == 'rejected_level1')
                                                {{-- Level 2: KSPI can reject after ASMAN KSPI reject (berjenjang) --}}
                                                @isKspi
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.penutup-lha-rekomendasi.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-danger mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close me-1"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @endif
                                        @endcanApproveReject
                                        
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm btn-custom" 
                                                title="Hapus"
                                                onclick="deleteData({{ $item->id }})">
                                            <i class="mdi mdi-delete me-1"></i>Hapus
                                        </button>
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
                <h5 class="modal-title" id="modalViewLabel{{ $item->id }}">Detail Rekomendasi Penutup LHA/LHK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Nomor ISS</dt>
                    <dd class="col-sm-8">{{ $item->temuan->nomor_iss ?? '-' }}</dd>
                    <dt class="col-sm-4">Nomor LHA/LHK</dt>
                    <dd class="col-sm-8">{{ $item->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? '-' }}</dd>
                    <dt class="col-sm-4">Rekomendasi</dt>
                    <dd class="col-sm-8">{{ $item->rekomendasi }}</dd>
                    <dt class="col-sm-4">Rencana Aksi</dt>
                    <dd class="col-sm-8">{{ $item->rencana_aksi }}</dd>
                    <dt class="col-sm-4">Eviden Rekomendasi</dt>
                    <dd class="col-sm-8">{{ $item->eviden_rekomendasi }}</dd>
                    <dt class="col-sm-4">PIC Rekomendasi</dt>
                    <dd class="col-sm-8">
                        @if($item->picUsers->count() > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach($item->picUsers as $picUser)
                                    <li>
                                        <span class="badge bg-info text-dark me-1">
                                            {{ $picUser->nama }} - {{ $picUser->auditee->divisi ?? '-' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            {{ $item->pic_rekomendasi ?? '-' }}
                        @endif
                    </dd>
                    <dt class="col-sm-4">Target Waktu</dt>
                    <dd class="col-sm-8">{{ $item->target_waktu }}</dd>
                    <dt class="col-sm-4">Status Approval</dt>
                    <dd class="col-sm-8">
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
                    </dd>
                    @if(in_array($item->status_approval, ['rejected', 'rejected_level1']) && ($item->rejection_reason_level2 ?? $item->rejection_reason_level1 ?? $item->alasan_reject))
                        <dt class="col-sm-4">Alasan Reject</dt>
                        <dd class="col-sm-8 text-danger">
                            @if($item->rejection_reason_level2)
                                <strong>Level 2:</strong> {{ $item->rejection_reason_level2 }}
                            @elseif($item->rejection_reason_level1)
                                <strong>Level 1:</strong> {{ $item->rejection_reason_level1 }}
                            @else
                                {{ $item->alasan_reject }}
                            @endif
                        </dd>
                    @endif
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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