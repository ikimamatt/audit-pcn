@extends('layouts.vertical', ['title' => 'Program Kerja Audit'])

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
            <h4 class="page-title">Program Kerja Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('audit.pka.create') }}" class="btn btn-primary mb-3">Tambah PKA</a>
                <div class="table-responsive">
                    <table class="table table-bordered table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Surat Tugas</th>
                                <th>No PKA</th>
                                <th>Tanggal PKA</th>
                                <th>Resiko</th>
                                <th>Milestone</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->perencanaanAudit->nomor_surat_tugas ?? '-' }}</td>
                                <td>{{ $item->no_pka }}</td>
                                <td>{{ $item->tanggal_pka }}</td>
                                <td>
                                    @if($item->risks && $item->risks->count() > 0)
                                        <ul class="mb-0 ps-3">
                                        @foreach($item->risks as $risk)
                                            <li>{{ $risk->deskripsi_resiko }}</li>
                                        @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <ul class="mb-0 ps-3">
                                    @foreach($item->milestones as $m)
                                        <li>{{ $m->nama_milestone }}: {{ $m->tanggal_mulai }} s/d {{ $m->tanggal_selesai }}</li>
                                    @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <a href="{{ route('audit.pka.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                                    <a href="{{ route('audit.pka.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('audit.pka.destroy', $item->id) }}" method="POST" style="display:inline-block" class="delete-form" id="delete-form-{{ $item->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            class="btn btn-danger btn-sm btn-delete-swal"
                                            data-id="{{ $item->id }}"
                                            data-check-url="{{ route('audit.pka.check-relations', $item->id) }}">
                                            Hapus
                                        </button>
                                    </form>
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
        document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = btn.dataset.id;
                const checkUrl = btn.dataset.checkUrl;
                const form = document.getElementById('delete-form-' + id);

                // Tampilkan loading sementara fetch data relasi
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(checkUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = 'Hapus';

                    let htmlContent = '';
                    if (data.has_relations) {
                        const relList = data.relations.map(r => `<li>${r}</li>`).join('');
                        htmlContent = `
                            <p class="mb-2">PKA <strong>${data.no_pka}</strong> (Surat Tugas: <strong>${data.surat_tugas}</strong>) memiliki data terkait yang akan <strong class="text-danger">ikut terhapus permanen</strong>:</p>
                            <ul class="text-start text-danger mb-2">${relList}</ul>
                            <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                        `;
                    } else {
                        htmlContent = `<p>Yakin ingin menghapus PKA <strong>${data.no_pka}</strong>? Tindakan ini tidak dapat dibatalkan.</p>`;
                    }

                    Swal.fire({
                        title: data.has_relations ? '⚠️ Peringatan! Data Terkait Akan Terhapus' : 'Hapus Data PKA?',
                        html: htmlContent,
                        icon: data.has_relations ? 'warning' : 'question',
                        confirmButtonText: 'Ya, Hapus Semua',
                        cancelButtonText: 'Batal',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Hapus';
                    // Fallback: tampilkan konfirmasi sederhana jika AJAX gagal
                    Swal.fire({
                        title: 'Hapus Data?',
                        text: 'Yakin ingin menghapus data PKA ini beserta seluruh proses audit terkait?',
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
        });
    });
</script>
@endsection 