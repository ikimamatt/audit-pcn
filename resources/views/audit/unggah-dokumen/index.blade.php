@extends('layouts.vertical', ['title' => 'Daftar Upload Dokumen Audit'])

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
            <h4 class="page-title">Daftar Upload Dokumen Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#unggahDokumenModal">Tambah Upload</button>
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Exit Meeting</th>
                                <th>Auditee</th>
                                <th>File Undangan & Absensi</th>
                                <th>Nomor LHA/LHK</th>
                                <th>File LHA/LHK</th>
                                <th>Nota Dinas (Tujuan)</th>
                                <th>File Nota Dinas</th>
                                <th>Status LHA/LHK</th>
                                <th>Status Undangan & Absensi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->exitMeeting->tanggal_exit_meeting ?? '-' }}</td>
                                <td>
                                    @if($item->exitMeeting && $item->exitMeeting->auditee)
                                        {{ $item->exitMeeting->auditee->divisi }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->exitMeeting && ($item->exitMeeting->file_undangan || $item->exitMeeting->file_absensi))
                                        <button type="button" class="btn btn-sm btn-info view-document-btn"
                                                data-bs-toggle="modal" data-bs-target="#documentViewerModal"
                                                data-file-undangan="{{ $item->exitMeeting->file_undangan ? asset('storage/' . $item->exitMeeting->file_undangan) : '' }}"
                                                data-file-absensi="{{ $item->exitMeeting->file_absensi ? asset('storage/' . $item->exitMeeting->file_absensi) : '' }}"
                                                data-document-type="Undangan & Absensi Exit Meeting"
                                                data-item-id="{{ $item->id }}">View</button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->lhaLhk->pelaporanHasilAudit->nomor_lha_lhk ?? '-' }}</td>
                                <td>
                                    @if($item->lhaLhk && $item->lhaLhk->file_lha_lhk)
                                        <button type="button" class="btn btn-sm btn-info view-lha-btn"
                                                data-bs-toggle="modal" data-bs-target="#documentViewerModal"
                                                data-file-lha="{{ asset('storage/' . $item->lhaLhk->file_lha_lhk) }}"
                                                data-document-type="LHA/LHK"
                                                data-item-id="{{ $item->id }}">View</button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->notaDinas)
                                        {{ strtoupper($item->notaDinas->tujuan_nota_dinas) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->notaDinas && $item->notaDinas->file_nota_dinas)
                                        <a href="{{ asset('storage/' . $item->notaDinas->file_nota_dinas) }}" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->lhaLhk)
                                        @if($item->lhaLhk->approve)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->exitMeeting)
                                        @if($item->exitMeeting->status_approval_undangan == 'approved' && $item->exitMeeting->status_approval_absensi == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($item->exitMeeting->status_approval_undangan == 'rejected' || $item->exitMeeting->status_approval_absensi == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('audit.unggah-dokumen.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('audit.unggah-dokumen.destroy', $item->id) }}" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-swal" data-id="{{ $item->id }}">Hapus</button>
                                    </form>
                                    <button class="btn btn-success btn-sm btn-approve-swal" data-id="{{ $item->id }}" data-type="exit_meeting">Approve Exit Meeting</button>
                                    <button class="btn btn-success btn-sm btn-approve-swal" data-id="{{ $item->id }}" data-type="lha_lhk">Approve LHA/LHK</button>
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

<!-- Modal Tambah/Edit Upload Dokumen -->
<div class="modal fade" id="unggahDokumenModal" tabindex="-1" aria-labelledby="unggahDokumenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="unggahDokumenModalLabel">Unggah Dokumen Audit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="unggahDokumenModalBody">
        <!-- Form will be loaded here via AJAX -->
      </div>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Approve -->
<div class="modal fade" id="approveConfirmationModal" tabindex="-1" aria-labelledby="approveConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approveConfirmationModalLabel">Konfirmasi Approve</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin approve data upload ini?</p>
        <form id="approveForm" method="POST" action="">
            @csrf
            <input type="hidden" name="id" id="approveId">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Approve</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Document Viewer with Approve -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="documentViewerModalLabel">View Dokumen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="docTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="undangan-tab" data-bs-toggle="tab" data-bs-target="#undangan" type="button" role="tab" aria-controls="undangan" aria-selected="true">Undangan</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="absensi-tab" data-bs-toggle="tab" data-bs-target="#absensi" type="button" role="tab" aria-controls="absensi" aria-selected="false">Absensi</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="lha-tab" data-bs-toggle="tab" data-bs-target="#lha" type="button" role="tab" aria-controls="lha" aria-selected="false">LHA/LHK</button>
          </li>
        </ul>
        <div class="tab-content mt-3" id="docTabContent">
          <div class="tab-pane fade show active" id="undangan" role="tabpanel" aria-labelledby="undangan-tab">
            <iframe id="documentViewerIframeUndangan" src="" frameborder="0" width="100%" height="500px"></iframe>
          </div>
          <div class="tab-pane fade" id="absensi" role="tabpanel" aria-labelledby="absensi-tab">
            <iframe id="documentViewerIframeAbsensi" src="" frameborder="0" width="100%" height="500px"></iframe>
          </div>
          <div class="tab-pane fade" id="lha" role="tabpanel" aria-labelledby="lha-tab">
            <iframe id="documentViewerIframeLha" src="" frameborder="0" width="100%" height="500px"></iframe>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-success" id="approveDocumentButton">Approve Dokumen</button>
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
                const id = btn.dataset.id;
                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Yakin ingin menghapus data upload ini?',
                    icon: 'warning',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form DELETE
                        btn.closest('form').submit();
                    }
                });
            });
        });
        // Approve confirmation (open modal)
        document.querySelectorAll('.btn-approve-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = btn.dataset.id;
                const type = btn.dataset.type; // Ambil type dari tombol
                document.getElementById('approveId').value = id;
                document.getElementById('approveType').value = type; // Set type di hidden input
                var approveModal = new bootstrap.Modal(document.getElementById('approveConfirmationModal'));
                approveModal.show();
            });
        });

        // Handle approve form submission via AJAX
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('approveId').value;
            const type = document.getElementById('approveType').value; // Ambil type dari hidden input
            const form = this;

            fetch(`{{ route('audit.unggah-dokumen.approve', '') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id, type: type }) // Kirim ID dan Type di body JSON
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat approve data.',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan jaringan atau server.',
                });
            })
            .finally(() => {
                var approveModal = bootstrap.Modal.getInstance(document.getElementById('approveConfirmationModal'));
                approveModal.hide();
            });
        });
        // Load create form via AJAX when modal is shown
        var unggahDokumenModal = document.getElementById('unggahDokumenModal');
        unggahDokumenModal.addEventListener('show.bs.modal', function (event) {
            // Only load if it's the "Tambah Upload" button, not edit
            if (event.relatedTarget && event.relatedTarget.dataset.bsToggle === 'modal') {
                document.getElementById('unggahDokumenModalLabel').innerText = 'Unggah Dokumen Audit';
                fetch(`{{ route('audit.unggah-dokumen.create') }}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('unggahDokumenModalBody').innerHTML = html;
                })
                .catch(error => console.error('Error loading create form:', error));
            }
        });

        // Document Viewer Modal Logic
        var documentViewerModal = document.getElementById('documentViewerModal');
        documentViewerModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var fileUndangan = button.getAttribute('data-file-undangan');
            var fileAbsensi = button.getAttribute('data-file-absensi');
            var fileLha = button.getAttribute('data-file-lha');
            var documentType = button.getAttribute('data-document-type');
            var itemId = button.getAttribute('data-item-id');

            var modalTitle = documentViewerModal.querySelector('#documentViewerModalLabel');
            var iframeUndangan = documentViewerModal.querySelector('#documentViewerIframeUndangan');
            var iframeAbsensi = documentViewerModal.querySelector('#documentViewerIframeAbsensi');
            var iframeLha = documentViewerModal.querySelector('#documentViewerIframeLha');
            var approveButton = documentViewerModal.querySelector('#approveDocumentButton');

            modalTitle.textContent = 'View ' + documentType;
            iframeUndangan.src = fileUndangan || '';
            iframeAbsensi.src = fileAbsensi || '';
            iframeLha.src = fileLha || '';
            approveButton.dataset.itemId = itemId; // Store item ID for approval

            // Hide tab if file not available
            document.getElementById('undangan-tab').style.display = fileUndangan ? '' : 'none';
            document.getElementById('undangan').style.display = fileUndangan ? '' : 'none';
            document.getElementById('absensi-tab').style.display = fileAbsensi ? '' : 'none';
            document.getElementById('absensi').style.display = fileAbsensi ? '' : 'none';
            document.getElementById('lha-tab').style.display = fileLha ? '' : 'none';
            document.getElementById('lha').style.display = fileLha ? '' : 'none';

            // Set active tab to the first available
            if (fileUndangan) {
                document.getElementById('undangan-tab').classList.add('active');
                document.getElementById('undangan').classList.add('show', 'active');
                document.getElementById('absensi-tab').classList.remove('active');
                document.getElementById('absensi').classList.remove('show', 'active');
                document.getElementById('lha-tab').classList.remove('active');
                document.getElementById('lha').classList.remove('show', 'active');
            } else if (fileAbsensi) {
                document.getElementById('absensi-tab').classList.add('active');
                document.getElementById('absensi').classList.add('show', 'active');
                document.getElementById('undangan-tab').classList.remove('active');
                document.getElementById('undangan').classList.remove('show', 'active');
                document.getElementById('lha-tab').classList.remove('active');
                document.getElementById('lha').classList.remove('show', 'active');
            } else if (fileLha) {
                document.getElementById('lha-tab').classList.add('active');
                document.getElementById('lha').classList.add('show', 'active');
                document.getElementById('undangan-tab').classList.remove('active');
                document.getElementById('undangan').classList.remove('show', 'active');
                document.getElementById('absensi-tab').classList.remove('active');
                document.getElementById('absensi').classList.remove('show', 'active');
            }
        });

        // Handle approve button click inside document viewer modal
        document.getElementById('approveDocumentButton').addEventListener('click', function() {
            const id = this.dataset.itemId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            // Tentukan type berdasarkan tab aktif di modal viewer
            let type = '';
            if (document.getElementById('undangan-tab').classList.contains('active')) {
                type = 'exit_meeting'; // Menggunakan 'exit_meeting' untuk undangan/absensi
            } else if (document.getElementById('absensi-tab').classList.contains('active')) {
                type = 'exit_meeting'; // Menggunakan 'exit_meeting' untuk undangan/absensi
            } else if (document.getElementById('lha-tab').classList.contains('active')) {
                type = 'lha_lhk';
            }
            Swal.fire({
                title: 'Approve Dokumen?',
                text: 'Yakin ingin approve dokumen ini?',
                icon: 'question',
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/audit/unggah-dokumen/${id}/approve?type=${type}`, {
                        method: 'POST', // Menggunakan POST untuk operasi perubahan data
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: id, type: type })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan saat approve dokumen.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan jaringan atau server.',
                        });
                    })
                    .finally(() => {
                        var modalInstance = bootstrap.Modal.getInstance(document.getElementById('documentViewerModal'));
                        modalInstance.hide();
                    });
                }
            });
        });
    });
</script>
@endsection
