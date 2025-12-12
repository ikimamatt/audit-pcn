@extends('layouts.vertical', ['title' => 'Entry Meeting'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Entry Meeting</h4>
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
                    <a href="{{ route('audit.entry-meeting.index') }}" class="btn btn-secondary ms-2">
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
                <a href="{{ route('audit.entry-meeting.create') }}" class="btn btn-primary mb-3">Tambah Entry Meeting</a>
                <div class="table-responsive">
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat Tugas</th>
                                <th>No PKA</th>
                                <th>Planned Meeting Date</th>
                                <th>Actual Meeting Date</th>
                                <th>Auditee</th>
                                <th>Undangan</th>
                                <th>Absensi</th>
                                <th>Status</th>
                                <th>Alasan Penolakan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($item->programKerjaAudit && $item->programKerjaAudit->perencanaanAudit)
                                            {{ $item->programKerjaAudit->perencanaanAudit->nomor_surat_tugas }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->programKerjaAudit)
                                            {{ $item->programKerjaAudit->no_pka }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($item->actual_meeting_date)
                                            {{ \Carbon\Carbon::parse($item->actual_meeting_date)->format('d/m/Y') }}
                                        @else
                                            <span class="badge bg-warning">Belum Dilaksanakan</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->auditee->divisi ?? '-' }}</td>
                                    <td>
                                        @if($item->file_undangan)
                                            <a href="{{ asset('storage/' . $item->file_undangan) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="mdi mdi-download"></i> Download
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->file_absensi)
                                            <a href="{{ asset('storage/' . $item->file_absensi) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="mdi mdi-download"></i> Download
                                            </a>
                                        @else
                                            -
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
                                        <a href="{{ route('audit.entry-meeting.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteData({{ $item->id }})">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('audit.entry-meeting.destroy', $item->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        
                                        @canApproveReject
                                            @if($item->status_approval == 'pending')
                                                {{-- Level 1: ASMAN KSPI can approve/reject --}}
                                                @isAsmanKspi
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.entry-meeting.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-success" onclick="approveData({{ $item->id }})">
                                                            <i class="mdi mdi-check"></i> Approve Level 1
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 1
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    </form>
                                                @endisAsmanKspi
                                                {{-- Level 2: KSPI can reject from pending --}}
                                                @isKspi
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.entry-meeting.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @elseif($item->status_approval == 'approved_level1')
                                                {{-- Level 2: KSPI can approve/reject after level 1 --}}
                                                @isKspi
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.entry-meeting.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-success" onclick="approveData({{ $item->id }})">
                                                            <i class="mdi mdi-check"></i> Approve Level 2
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                    </form>
                                                @endisKspi
                                            @elseif($item->status_approval == 'rejected_level1')
                                                {{-- Level 2: KSPI can reject after ASMAN KSPI reject (berjenjang) --}}
                                                @isKspi
                                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.entry-meeting.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="rejectData({{ $item->id }})">
                                                            <i class="mdi mdi-close"></i> Reject Level 2
                                                        </button>
                                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
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