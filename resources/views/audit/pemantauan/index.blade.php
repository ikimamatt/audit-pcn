@extends('layouts.vertical', ['title' => 'Pemantauan Hasil Audit'])

@section('css')
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
                            <a href="{{ route('audit.pemantauan.select-nomor-surat-tugas') }}" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-refresh me-1"></i>Ganti Nomor Surat Tugas
                            </a>
                        </div>
                    </div>
                @endif
                
                <form method="GET" class="mb-3 d-flex align-items-center" action="">
                    <input type="hidden" name="nomor_surat_tugas" value="{{ $nomorSuratTugas }}">
                    <label for="bulan" class="me-2 mb-0">Filter Bulan:</label>
                    <input type="month" name="bulan" id="bulan" class="form-control me-2" style="max-width:200px;" value="{{ request('bulan') }}">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </form>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="table-responsive" style="overflow-x:auto;">
                    <table class="table table-bordered table-hover" style="min-width:1200px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Auditee</th>
                                <th>Nomor Tugas</th>
                                <th>Nomor ISS</th>
                                <th>Temuan</th>
                                <th>Rekomendasi</th>
                                <th>Eviden</th>
                                <th>Target Waktu</th>
                                <th>PIC Rekomendasi</th>
                                <th>Status Tindak Lanjut</th>
                                <th>Aksi</th>
                                <th>View</th>
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
                                            <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat }}</small>
                                        @endif
                                        @if($row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang)
                                            <br>
                                            <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->temuan && $row->temuan->pelaporanHasilAudit && $row->temuan->pelaporanHasilAudit->perencanaanAudit)
                                        <strong>{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->jenis_audit ?? 'N/A' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->temuan)
                                        <strong>{{ $row->temuan->nomor_iss ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? 'N/A' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->temuan)
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $row->temuan->permasalahan ?? 'N/A' }}">
                                            <strong>Permasalahan:</strong><br>
                                            {{ Str::limit($row->temuan->permasalahan ?? 'N/A', 100) }}
                                        </div>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $row->temuan->penyebab ?? 'N/A' }}">
                                            <strong>Penyebab:</strong><br>
                                            {{ Str::limit($row->temuan->penyebab ?? 'N/A', 100) }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $row->rekomendasi }}">
                                        {{ Str::limit($row->rekomendasi, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $row->eviden_rekomendasi }}">
                                        {{ Str::limit($row->eviden_rekomendasi, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $row->target_waktu < now() ? 'bg-danger' : 'bg-success' }}">
                                        {{ \Carbon\Carbon::parse($row->target_waktu)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>{{ $row->pic_rekomendasi }}</td>
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
                                                // Check if user is PIC APPROVAL 2
                                                $isPicApproval2 = $row->picUsers()
                                                    ->where('master_user_id', $user->id)
                                                    ->wherePivot('pic_type', 'approval_2_spi')
                                                    ->exists();
                                                if ($isPicApproval2) {
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
                                                <i class="mdi mdi-help-circle me-1"></i>{{ ucfirst($statusTindakLanjut ?? 'Unknown') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($canUpdateStatus)
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-update-status" 
                                                data-rekomendasi-id="{{ $row->id }}" 
                                                data-current-status="{{ $statusTindakLanjut }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalUpdateStatus{{ $row->id }}">
                                            <i class="mdi mdi-pencil me-1"></i>Ubah Status
                                        </button>
                                    @endif
                                    
                                    @if($row->tindakLanjut->count() > 0)
                                        <small class="text-muted d-block mt-2">
                                            <i class="mdi mdi-history me-1"></i>{{ $row->tindakLanjut->count() }} tindak lanjut
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <a href="{{ route('audit.pemantauan.edit', $row->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>
                                        @php
                                            $latestStatus = $row->tindakLanjut->sortByDesc('created_at')->first()?->status_tindak_lanjut ?? $row->status_tindak_lanjut;
                                        @endphp
                                        <a href="{{ route('audit.penutup-lha-rekomendasi.tindak-lanjut.form', $row->id) }}" 
                                           class="btn btn-success btn-sm mb-1"
                                           @if($latestStatus == 'closed')
                                               title="Meski status sudah closed, Anda masih bisa menambah tindak lanjut"
                                               data-bs-toggle="tooltip"
                                           @endif>
                                            @if($latestStatus == 'closed')
                                                <i class="mdi mdi-plus-circle me-1"></i>
                                            @endif
                                            Tindak Lanjut
                                        </a>
                                        <form action="{{ route('audit.penutup-lha-rekomendasi.destroy', $row->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('audit.pemantauan.tindak-lanjut.index', $row->id) }}" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                            
                            <!-- Modal Update Status -->
                            @php
                                $latestTindakLanjut = $row->tindakLanjut->sortByDesc('created_at')->first();
                                $statusTindakLanjut = $latestTindakLanjut ? $latestTindakLanjut->status_tindak_lanjut : $row->status_tindak_lanjut;
                                
                                $canUpdateStatus = false;
                                $user = Auth::user();
                                if ($user && $user->akses) {
                                    $namaAkses = $user->akses->nama_akses;
                                    if (in_array($namaAkses, ['AUDITOR', 'Auditor', 'ASMAN SPI', 'KSPI'])) {
                                        $canUpdateStatus = true;
                                    } else {
                                        $isPicApproval2 = $row->picUsers()
                                            ->where('master_user_id', $user->id)
                                            ->wherePivot('pic_type', 'approval_2_spi')
                                            ->exists();
                                        if ($isPicApproval2) {
                                            $canUpdateStatus = true;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if($canUpdateStatus)
                            <div class="modal fade" id="modalUpdateStatus{{ $row->id }}" tabindex="-1" aria-labelledby="modalUpdateStatusLabel{{ $row->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalUpdateStatusLabel{{ $row->id }}">
                                                <i class="mdi mdi-pencil-circle me-2"></i>Update Status Tindak Lanjut
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nomor ISS:</label>
                                                <p>{{ $row->temuan->nomor_iss ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Rekomendasi:</label>
                                                <p class="text-muted">{{ Str::limit($row->rekomendasi, 150) }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status Saat Ini:</label>
                                                <div>
                                                    @if($statusTindakLanjut == 'closed')
                                                        <span class="badge bg-success">
                                                            <i class="mdi mdi-check-circle me-1"></i>Closed
                                                        </span>
                                                    @elseif($statusTindakLanjut == 'on_progress')
                                                        <span class="badge bg-info">
                                                            <i class="mdi mdi-clock me-1"></i>On Progress
                                                        </span>
                                                    @elseif($statusTindakLanjut == 'open')
                                                        <span class="badge bg-warning">
                                                            <i class="mdi mdi-alert-circle me-1"></i>Open
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <label for="newStatus{{ $row->id }}" class="form-label fw-bold">Pilih Status Baru: <span class="text-danger">*</span></label>
                                                <select class="form-select" id="newStatus{{ $row->id }}" required>
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="open" {{ $statusTindakLanjut == 'open' ? 'selected' : '' }}>
                                                        🟡 Open
                                                    </option>
                                                    <option value="on_progress" {{ $statusTindakLanjut == 'on_progress' ? 'selected' : '' }}>
                                                        🔵 On Progress
                                                    </option>
                                                    <option value="closed" {{ $statusTindakLanjut == 'closed' ? 'selected' : '' }}>
                                                        🟢 Closed
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="mdi mdi-close me-1"></i>Batal
                                            </button>
                                            <button type="button" class="btn btn-primary btn-confirm-update" data-rekomendasi-id="{{ $row->id }}">
                                                <i class="mdi mdi-check me-1"></i>Update Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Handle status update confirmation
    document.querySelectorAll('.btn-confirm-update').forEach(function(button) {
        button.addEventListener('click', function() {
            const rekomendasiId = this.dataset.rekomendasiId;
            const newStatusSelect = document.getElementById(`newStatus${rekomendasiId}`);
            const newStatus = newStatusSelect.value;
            
            if (!newStatus) {
                alert('Silakan pilih status terlebih dahulu!');
                return;
            }
            
            // Disable button while processing
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
                    status_tindak_lanjut: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge display
                    const statusContainer = document.getElementById(`status-container-${rekomendasiId}`);
                    let badgeClass = 'bg-secondary';
                    let badgeIcon = 'mdi-help-circle';
                    let badgeText = 'Unknown';
                    
                    if (newStatus === 'closed') {
                        badgeClass = 'bg-success';
                        badgeIcon = 'mdi-check-circle';
                        badgeText = 'Closed';
                    } else if (newStatus === 'on_progress') {
                        badgeClass = 'bg-info';
                        badgeIcon = 'mdi-clock';
                        badgeText = 'On Progress';
                    } else if (newStatus === 'open') {
                        badgeClass = 'bg-warning';
                        badgeIcon = 'mdi-alert-circle';
                        badgeText = 'Open';
                    }
                    
                    statusContainer.innerHTML = `
                        <span class="badge ${badgeClass} w-100 py-2">
                            <i class="mdi ${badgeIcon} me-1"></i>${badgeText}
                        </span>
                    `;
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById(`modalUpdateStatus${rekomendasiId}`));
                    modal.hide();
                    
                    // Show success message
                    alert(data.message);
                    
                    // Optional: reload page to refresh all data
                    // window.location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengubah status');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalHtml;
            });
        });
    });
});
</script>
@endsection 