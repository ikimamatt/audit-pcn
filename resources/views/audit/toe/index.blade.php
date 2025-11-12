@extends('layouts.vertical', ['title' => 'Hasil TOE Audit'])

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
            <h4 class="page-title">Hasil TOE Audit</h4>
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
                    <a href="{{ route('audit.toe.index') }}" class="btn btn-secondary ms-2">
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
                <a href="{{ route('audit.toe.create') }}" class="btn btn-primary mb-3">Tambah TOE</a>
                <div class="table-responsive">
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Surat Tugas</th>
                                <th>Judul BPM</th>
                                <th>Pengendalian Eksisting</th>
                                <th>Status</th>
                                <th>Alasan Penolakan</th>
                                <th>Evaluasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->perencanaanAudit ? $item->perencanaanAudit->nomor_surat_tugas : '-' }}</td>
                                <td>{{ $item->judul_bpm }}</td>
                                <td>{{ $item->pengendalian_eksisting }}</td>
                                <td>
                                    @if($item->status_approval == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($item->status_approval == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status_approval == 'rejected' && $item->rejection_reason)
                                        <span class="text-danger" title="{{ $item->rejection_reason }}">
                                            {{ Str::limit($item->rejection_reason, 30) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm btn-evaluasi-modal" data-toe-id="{{ $item->id }}">Evaluasi ({{ $item->evaluasi->count() }})</a>
                                </td>
                                <td>
                                    <a href="{{ route('audit.toe.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('audit.toe.destroy', $item->id) }}" method="POST" style="display:inline-block" class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-delete-swal">Hapus</button>
                                    </form>
                                    @if($item->status_approval == 'pending')
                                    <form id="approval-form-{{ $item->id }}" action="{{ route('audit.toe.approval', $item->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <button type="button" class="btn btn-success btn-sm" onclick="approveData({{ $item->id }})">
                                            <i class="mdi mdi-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="rejectData({{ $item->id }})">
                                            <i class="mdi mdi-close"></i> Reject
                                        </button>
                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                    </form>
                                    @endif
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
<!-- Modal Evaluasi TOE -->
<div class="modal fade" id="evaluasiModal" tabindex="-1" aria-labelledby="evaluasiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="evaluasiModalLabel">Evaluasi TOE</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="evaluasi-modal-content">
          <div class="text-center py-5">Loading...</div>
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
                title: 'Hapus TOE?',
                text: 'Yakin ingin menghapus TOE ini?',
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

    function approveData(id) {
        Swal.fire({
            title: 'Approve TOE?',
            text: "Apakah Anda yakin ingin approve TOE ini?",
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

    function rejectData(id) {
        Swal.fire({
            title: 'Tolak TOE',
            html: `
                <div class="text-center mb-3">
                    <i class="mdi mdi-alert-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-3">Apakah Anda yakin ingin menolak TOE ini?</p>
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

    function loadEvaluasi(toeId) {
        const modalContent = document.getElementById('evaluasi-modal-content');
        modalContent.innerHTML = '<div class="text-center py-5">Loading...</div>';
        fetch(`/audit/toe-evaluasi-modal/${toeId}`)
            .then(res => res.text())
            .then(html => { modalContent.innerHTML = html; });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-evaluasi-modal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const toeId = btn.dataset.toeId;
                loadEvaluasi(toeId);
                var modal = new bootstrap.Modal(document.getElementById('evaluasiModal'));
                modal.show();
            });
        });
    });
</script>
@endsection 