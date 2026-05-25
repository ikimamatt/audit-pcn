@extends('layouts.vertical', ['title' => 'Pemantauan Hasil Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
    <style>
        .btn-update-status {
            font-size: 0.8rem;
            padding: 0.35rem 0.5rem;
            white-space: nowrap;
        }

        .badge.w-100 {
            display: block;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
        }

        .modal-body .badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            width: fit-content;
        }

        .btn-grid .btn {
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
        }

        .btn-grid .btn i {
            margin: 0 !important;
            font-size: 1.1rem;
        }

        .btn-grid form {
            margin: 0;
            padding: 0;
            display: flex;
        }

        .btn-send-reminder {
            background-color: #8b5cf6 !important;
            border: none;
            color: #ffffff !important;
        }
        .btn-send-reminder i {
            color: #ffffff !important;
        }
        .btn-send-reminder:hover {
            background-color: #7c3aed !important;
            color: #ffffff !important;
        }
        .btn-send-reminder:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        /* Badge notified */
        .badge-notified {
            font-size: 0.65rem;
            background: #e0e7ff;
            color: #4338ca;
            border-radius: 6px;
            padding: 2px 6px;
            display: inline-block;
            margin-top: 3px;
        }
        /* Toast */
        #reminder-toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .reminder-toast {
            min-width: 300px;
            max-width: 380px;
            background: #1e293b;
            color: #f1f5f9;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 13px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: slideInToast 0.3s ease;
        }
        .reminder-toast.success { border-left: 4px solid #22c55e; }
        .reminder-toast.error   { border-left: 4px solid #ef4444; }
        .reminder-toast .toast-icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .reminder-toast .toast-body strong { display: block; margin-bottom: 2px; }
        @keyframes slideInToast {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">PEMANTAUAN HASIL AUDIT</h4>
                    <a href="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="btn btn-secondary">
                        <i class="mdi mdi-file-document-outline me-2"></i>Pilih Nomor Surat Tugas
                    </a>
                </div>
                <div class="card-body">
                    @if($nomorSuratTugas && $perencanaanAudit)
                        <div class="alert alert-info mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Nomor Surat Tugas:</strong> {{ $nomorSuratTugas }}<br>
                                    <strong>Jenis Audit:</strong> {{ $perencanaanAudit->jenis_audit }}
                                </div>
                                <a href="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="mdi mdi-refresh me-1"></i>Ganti Nomor Surat Tugas
                                </a>
                            </div>
                        </div>
                    @endif

                    <form method="GET" class="mb-3 d-flex align-items-center" action="">
                        <input type="hidden" name="nomor_surat_tugas" value="{{ $nomorSuratTugas }}">
                        <label for="bulan" class="me-2 mb-0">Filter Bulan:</label>
                        <input type="month" name="bulan" id="bulan" class="form-control me-2" style="max-width:200px;"
                            value="{{ request('bulan') }}">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive" style="overflow-x:auto;">
                        <table id="scroll-horizontal-datatable" class="table table-bordered table-hover w-100 nowrap" style="min-width:1200px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Auditee</th>
                                    <th>Nomor Tugas</th>
                                    <th>Nomor ISS</th>

                                    <th>Target Waktu</th>
                                    <th>PIC Rekomendasi</th>
                                    <th>Status Tindak Lanjut</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $row)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($row->temuan && $row->temuan->pelaporanHasilAudit && $row->temuan->pelaporanHasilAudit->perencanaanAudit && $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee)
                                                <strong>{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi ?? 'N/A' }}</strong>
                                                @if($row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat)
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat }}</small>
                                                @endif
                                                @if($row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang)
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->temuan && $row->temuan->pelaporanHasilAudit && $row->temuan->pelaporanHasilAudit->perencanaanAudit)
                                                <strong>{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas ?? 'N/A' }}</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->jenis_audit ?? 'N/A' }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->temuan)
                                                <strong>{{ $row->temuan->nomor_iss ?? 'N/A' }}</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $row->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? 'N/A' }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge {{ $row->target_waktu < now() ? 'bg-danger' : 'bg-success' }}">
                                                {{ \Carbon\Carbon::parse($row->target_waktu)->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td style="min-width: 180px; max-width: 240px;">
                                            @php
                                                $picEntries = $row->pic_rekomendasi
                                                    ? array_filter(array_map('trim', explode('|', $row->pic_rekomendasi)))
                                                    : [];

                                                $picColors = ['bg-primary', 'bg-info', 'bg-secondary', 'bg-dark'];
                                            @endphp
                                            @if(count($picEntries) > 0)
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach(array_values($picEntries) as $i => $entry)
                                                        @php
                                                            $parts = explode(':', $entry, 2);
                                                            $role  = trim($parts[0] ?? '');
                                                            $name  = trim($parts[1] ?? '');
                                                            $color = $picColors[$i % count($picColors)];

                                                            if (stripos($role, 'BUSINESS CONTACT') !== false) {
                                                                $role = 'Business Contact';
                                                            } elseif (stripos($role, 'APPROVAL 1') !== false || stripos($role, 'APPROVAL_1') !== false || stripos($role, 'BUSINESS REVIEWER 1') !== false || stripos($role, 'BUSINESS_REVIEWER_1') !== false) {
                                                                $role = 'Business Reviewer 1';
                                                            } elseif (stripos($role, 'APPROVAL 2') !== false || stripos($role, 'APPROVAL_2') !== false || stripos($role, 'BUSINESS REVIEWER 2') !== false || stripos($role, 'BUSINESS_REVIEWER_2') !== false) {
                                                                $role = 'Business Reviewer 2';
                                                            }
                                                        @endphp
                                                        <div class="rounded px-2 py-1 border" style="background:#f8f9fa; font-size:0.72rem; line-height:1.4;">
                                                            <div class="fw-semibold text-muted" style="font-size:0.68rem; text-transform:uppercase; letter-spacing:.03em;">
                                                                <i class="mdi mdi-tag-outline me-1"></i>{{ $role }}
                                                            </div>
                                                            @if($name)
                                                                <div class="text-dark fw-bold" style="word-break:break-word;">
                                                                    <i class="mdi mdi-account-outline me-1"></i>{{ $name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="min-width: 150px;">
                                            @php
                                                // Ambil status tindak lanjut terbaru jika ada
                                                $latestTindakLanjut = $row->tindakLanjut->sortByDesc('created_at')->first();
                                                $statusTindakLanjut = $latestTindakLanjut ? $latestTindakLanjut->status_tindak_lanjut : $row->status_tindak_lanjut;

                                                // Check if user can update status
                                                $canUpdateStatus = false;
                                                $user = Auth::user();
                                                if ($user && $user->akses) {
                                                    $namaAkses = $user->akses->nama_akses;
                                                    // AUDITOR, ASMAN SPI, KSPI bisa update status
                                                    if (in_array($namaAkses, ['AUDITOR', 'Auditor', 'ASMAN SPI', 'KSPI'])) {
                                                        $canUpdateStatus = true;
                                                    } else {
                                                        // Check if user is PIC APPROVAL 1 or PIC APPROVAL 2
                                                        $isPicApproval = $row->picUsers()
                                                            ->where('master_user_id', $user->id)
                                                            ->whereIn('pic_type', ['approval_1_spi', 'approval_2_spi'])
                                                            ->exists();
                                                        if ($isPicApproval) {
                                                            $canUpdateStatus = true;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <div id="status-container-{{ $row->id }}" class="mb-2">
                                                @if($statusTindakLanjut == 'closed')
                                                    <span class="badge bg-success w-100 py-2">
                                                        <i class="mdi mdi-check-circle me-1"></i>Closed
                                                    </span>
                                                @elseif($statusTindakLanjut == 'on_progress')
                                                    <span class="badge bg-info w-100 py-2">
                                                        <i class="mdi mdi-clock me-1"></i>On Progress
                                                    </span>
                                                @elseif($statusTindakLanjut == 'open')
                                                    <span class="badge bg-warning w-100 py-2">
                                                        <i class="mdi mdi-alert-circle me-1"></i>Open
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary w-100 py-2">
                                                        <i
                                                            class="mdi mdi-help-circle me-1"></i>{{ ucfirst($statusTindakLanjut ?? 'Unknown') }}
                                                    </span>
                                                @endif
                                            </div>

                                            @php
                                                $canApproveLvl1 = \App\Helpers\ApprovalHelper::canApproveLevel1($row);
                                                $canApproveLvl2 = \App\Helpers\ApprovalHelper::canApproveLevel2($row);
                                                $canReject = \App\Helpers\ApprovalHelper::canReject($row);
                                            @endphp

                                            @if($canApproveLvl1)
                                                <button type="button" class="btn btn-sm btn-success w-100 btn-action-approve-index mb-1" 
                                                    data-rekomendasi-id="{{ $row->id }}" data-action="approve" data-level="1">
                                                    <i class="mdi mdi-check-circle me-1"></i>Approve Lvl 1
                                                </button>
                                            @endif

                                            @if($canApproveLvl2)
                                                <button type="button" class="btn btn-sm btn-success w-100 btn-action-approve-index mb-1" 
                                                    data-rekomendasi-id="{{ $row->id }}" data-action="approve" data-level="2">
                                                    <i class="mdi mdi-check-decagram me-1"></i>Approve Lvl 2
                                                </button>
                                            @endif

                                            @if($canReject)
                                                <button type="button" class="btn btn-sm btn-danger w-100 btn-action-reject-trigger mb-1" 
                                                    data-rekomendasi-id="{{ $row->id }}" data-nomor-iss="{{ $row->temuan->nomor_iss ?? '-' }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalRejectTindakLanjutIndex">
                                                    <i class="mdi mdi-close-circle me-1"></i>Reject
                                                </button>
                                            @endif

                                            @if($row->tindakLanjut->count() > 0)
                                                <small class="text-muted d-block mt-2">
                                                    <i class="mdi mdi-history me-1"></i>{{ $row->tindakLanjut->count() }} tindak
                                                    lanjut
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                // $canSendReminder sudah dihitung di controller & dikirim ke view
                                                $latestStatus    = $row->tindakLanjut->sortByDesc('created_at')->first()?->status_tindak_lanjut ?? $row->status_tindak_lanjut;
                                                // Cek email: pastikan tidak null DAN tidak string kosong
                                                $hasPicWithEmail = $row->picUsers->filter(fn($u) => !empty($u->email))->isNotEmpty();
                                                
                                                // Cek business contact untuk tombol tindak lanjut
                                                $currentUserId = \App\Helpers\AuthHelper::getCurrentUserId();
                                                $isBusinessContact = $row->picUsers()
                                                    ->where('master_user_id', $currentUserId)
                                                    ->wherePivot('pic_type', 'business_contact')
                                                    ->exists();
                                                $canAddTindakLanjut = $isBusinessContact || \App\Helpers\AuthHelper::isSuperAdmin();
                                            @endphp
                                            <div class="btn-grid">
                                                <a href="{{ route('audit.pemantauan.tindak-lanjut.index', $row->id) }}"
                                                    class="btn btn-info text-white" title="View Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                
                                                @if(\App\Helpers\AuthHelper::canModifyData())
                                                <a href="{{ route('audit.pemantauan.edit', $row->id) }}"
                                                    class="btn btn-warning text-white" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                @endif
                                                
                                                @if($canAddTindakLanjut)
                                                <a href="{{ route('audit.penutup-lha-rekomendasi.tindak-lanjut.form', $row->id) }}"
                                                    class="btn btn-success" title="Tambah Tindak Lanjut" @if($latestStatus == 'closed')
                                                    data-bs-toggle="tooltip" data-bs-original-title="Meski status sudah closed, Anda masih bisa menambah tindak lanjut" @endif>
                                                    <i class="mdi mdi-plus"></i>
                                                </a>
                                                @endif
                                                
                                                {{-- Tombol Kirim Reminder: hanya tampil untuk SPI --}}
                                                @if($canSendReminder && $latestStatus !== 'closed')
                                                    <button type="button"
                                                        class="btn btn-send-reminder text-white"
                                                        title="{{ $hasPicWithEmail ? 'Kirim Email Pengingat ke Semua PIC' : 'Tidak ada PIC dengan email terdaftar' }}"
                                                        data-rekomendasi-id="{{ $row->id }}"
                                                        data-nomor-iss="{{ $row->temuan->nomor_iss ?? '-' }}"
                                                        {{ !$hasPicWithEmail ? 'disabled' : '' }}
                                                        id="btn-reminder-{{ $row->id }}">
                                                        <i class="mdi mdi-bell-ring text-white"></i>
                                                    </button>
                                                @endif
                                                
                                                @if(\App\Helpers\AuthHelper::canModifyData())
                                                <form action="{{ route('audit.penutup-lha-rekomendasi.destroy', $row->id) }}"
                                                    method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                            {{-- Info terakhir dinotifikasi --}}
                                            @if($row->last_notified_at)
                                                <div class="badge-notified mt-1">
                                                    <i class="mdi mdi-email-check-outline"></i>
                                                    Terkirim: {{ \Carbon\Carbon::parse($row->last_notified_at)->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Modals removed in favor of single reject modal at the bottom --}}

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

    <!-- Single Modal Reject Tindak Lanjut Index -->
    <div class="modal fade" id="modalRejectTindakLanjutIndex" tabindex="-1" aria-labelledby="modalRejectTindakLanjutIndexLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRejectTindakLanjutIndexLabel">
                        <i class="mdi mdi-close-circle me-2 text-danger"></i>Tolak Tindak Lanjut
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <input type="hidden" id="reject_rekomendasi_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor ISS:</label>
                        <p id="reject_iss_placeholder" class="form-control-plaintext text-muted mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason_index" class="form-label fw-bold">Alasan Penolakan: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason_index" rows="4" placeholder="Masukkan alasan penolakan (minimal 10 karakter)..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger btn-confirm-reject-index">
                        <i class="mdi mdi-check me-1"></i>Tolak Tindak Lanjut
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/pages/datatable.init.js'])

    {{-- Toast container untuk notifikasi reminder --}}
    <div id="reminder-toast-container"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle Approve from Index Page
            document.querySelectorAll('.btn-action-approve-index').forEach(function (button) {
                button.addEventListener('click', function () {
                    const rekomendasiId = this.dataset.rekomendasiId;
                    const level = this.dataset.level;

                    Swal.fire({
                        title: 'Konfirmasi Approval',
                        text: `Apakah Anda yakin ingin menyetujui (Approve) Tindak Lanjut ini untuk Level ${level}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Setujui',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Disable button
                            this.disabled = true;
                            const originalHtml = this.innerHTML;
                            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>...';

                            fetch(`/audit/pemantauan/${rekomendasiId}/update-status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    action: 'approve'
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#198754'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: data.message || 'Gagal menyetujui tindak lanjut.',
                                        icon: 'error',
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Terjadi Kesalahan!',
                                    text: 'Terjadi kesalahan saat menyetujui tindak lanjut.',
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            })
                            .finally(() => {
                                this.disabled = false;
                                this.innerHTML = originalHtml;
                            });
                        }
                    });
                });
            });

            // Handle Reject Trigger (populates the single modal)
            document.querySelectorAll('.btn-action-reject-trigger').forEach(function (button) {
                button.addEventListener('click', function () {
                    const rekomendasiId = this.dataset.rekomendasiId;
                    const nomorIss = this.dataset.nomorIss;

                    document.getElementById('reject_rekomendasi_id').value = rekomendasiId;
                    document.getElementById('reject_iss_placeholder').innerText = nomorIss;
                    document.getElementById('rejection_reason_index').value = '';
                });
            });

            // Handle Reject Confirmation from the Single Modal
            const btnConfirmRejectIndex = document.querySelector('.btn-confirm-reject-index');
            if (btnConfirmRejectIndex) {
                btnConfirmRejectIndex.addEventListener('click', function () {
                    const rekomendasiId = document.getElementById('reject_rekomendasi_id').value;
                    const reasonTextarea = document.getElementById('rejection_reason_index');
                    const reason = reasonTextarea.value.trim();

                    if (!reason || reason.length < 10) {
                        Swal.fire({
                            title: 'Validasi Gagal!',
                            text: 'Alasan penolakan harus diisi minimal 10 karakter!',
                            icon: 'warning',
                            confirmButtonColor: '#dc3545'
                        });
                        return;
                    }

                    // Disable button
                    this.disabled = true;
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

                    fetch(`/audit/pemantauan/${rekomendasiId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            action: 'reject',
                            rejection_reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal first
                            const modalEl = document.getElementById('modalRejectTindakLanjutIndex');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();

                            Swal.fire({
                                title: 'Berhasil Menolak!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#dc3545'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message || 'Gagal menolak tindak lanjut.',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Terjadi Kesalahan!',
                            text: 'Terjadi kesalahan saat menolak tindak lanjut.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    });
                });
            }

            // ─────────────────────────────────────────────
            //  Handler: Tombol Kirim Reminder Email
            // ─────────────────────────────────────────────
            function showReminderToast(type, title, message) {
                const container = document.getElementById('reminder-toast-container');
                const icon = type === 'success' ? '✅' : '❌';
                const toast = document.createElement('div');
                toast.className = `reminder-toast ${type}`;
                toast.innerHTML = `
                    <span class="toast-icon">${icon}</span>
                    <div class="toast-body">
                        <strong>${title}</strong>
                        ${message}
                    </div>`;
                container.appendChild(toast);
                // Auto remove after 5s
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.4s';
                    setTimeout(() => toast.remove(), 400);
                }, 5000);
            }

            document.querySelectorAll('.btn-send-reminder').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const rekomendasiId = this.dataset.rekomendasiId;
                    const nomorIss      = this.dataset.nomorIss;

                    Swal.fire({
                        title: 'Kirim Email Pengingat?',
                        text: `Kirim email pengingat untuk rekomendasi ISS: ${nomorIss}? Email akan dikirim ke semua PIC yang memiliki alamat email terdaftar.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#8b5cf6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Kirim!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Loading state
                            const btnEl = this;
                            btnEl.disabled = true;
                            btnEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                            fetch(`/audit/pemantauan/${rekomendasiId}/kirim-reminder`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    showReminderToast(
                                        'success',
                                        'Email Terkirim!',
                                        `Pengingat berhasil dikirim ke ${data.sent_to ? data.sent_to.length : '?'} PIC.`
                                    );
                                    // Update badge notified di bawah tombol
                                    const td = btnEl.closest('td');
                                    const existing = td.querySelector('.badge-notified');
                                    const now = new Date().toLocaleString('id-ID', {day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'});
                                    if (existing) {
                                        existing.innerHTML = `<i class="mdi mdi-email-check-outline"></i> Terkirim: ${now}`;
                                    } else {
                                        const badge = document.createElement('div');
                                        badge.className = 'badge-notified mt-1';
                                        badge.innerHTML = `<i class="mdi mdi-email-check-outline"></i> Terkirim: ${now}`;
                                        td.appendChild(badge);
                                    }
                                } else {
                                    showReminderToast('error', 'Gagal!', data.message || 'Terjadi kesalahan.');
                                }
                            })
                            .catch(() => showReminderToast('error', 'Gagal!', 'Koneksi bermasalah. Coba lagi.'))
                            .finally(() => {
                                btnEl.disabled = false;
                                btnEl.innerHTML = '<i class="mdi mdi-bell-ring"></i>';
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection