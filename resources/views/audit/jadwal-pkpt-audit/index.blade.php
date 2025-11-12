@extends('layouts.vertical', ['title' => 'Jadwal PKPT Audit'])

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
            <h4 class="page-title">Jadwal PKPT Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('audit.pkpt.create') }}" class="btn btn-primary mb-3">Tambah Jadwal PKPT</a>
                <div class="table-responsive">
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Auditee</th>
                                <th>Jenis Audit</th>
                                <th>Jumlah Auditor</th>
                                <th>Tanggal Audit</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->auditee ? $item->auditee->divisi : '-' }}</td>
                                <td>{{ $item->jenis_audit }}</td>
                                <td>{{ $item->jumlah_auditor }}</td>
                                <td>{{ $item->tanggal_mulai }} s/d {{ $item->tanggal_selesai }}</td>
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
                                    <a href="{{ route('audit.pkpt.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('audit.pkpt.destroy', $item->id) }}" method="POST" style="display:inline-block" class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-delete-swal">Hapus</button>
                                    </form>
                                    @if($item->status_approval == 'pending')
                                    <form action="{{ route('audit.pkpt.approval', $item->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                        <button type="button" class="btn btn-success btn-sm btn-approve-swal" data-id="{{ $item->id }}">Approve</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-secondary btn-sm">Reject</button>
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
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmWithSwal({ title = 'Konfirmasi', text = 'Apakah Anda yakin?', icon = 'question', confirmButtonText = 'Ya', cancelButtonText = 'Batal', onConfirm = null } = {}) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = btn.closest('form');

                Swal.fire({
                    title: 'Hapus Jadwal PKPT?',
                    text: 'Yakin ingin menghapus jadwal PKPT ini?',
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
                const form = btn.closest('form');
                const itemId = btn.dataset.id;
                const hiddenInputAction = document.getElementById(`action-${itemId}`);

                confirmWithSwal({
                    title: 'Approve Jadwal?',
                    text: 'Yakin ingin approve jadwal ini?',
                    icon: 'question',
                    confirmButtonText: 'Ya, Approve',
                    cancelButtonText: 'Batal',
                    onConfirm: function() {
                        hiddenInputAction.value = 'approve'; // Set the hidden input value
                        form.submit();
                    }
                });
            });
        });
    });
</script>
    @vite([ 'resources/js/pages/datatable.init.js'])
@endsection
