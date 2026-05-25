@extends('layouts.vertical', ['title' => 'Tindak Lanjut Rekomendasi Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Tindak Lanjut Rekomendasi Audit</h4>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @php
                    $latestTindakLanjut = $rekomendasi->tindakLanjut->sortByDesc('created_at')->first();
                    $currentStatus = $latestTindakLanjut ? $latestTindakLanjut->status_tindak_lanjut : $rekomendasi->status_tindak_lanjut;
                @endphp
                
                @if($currentStatus == 'closed')
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-information-outline me-2"></i>
                        <strong>Status Saat Ini: CLOSED</strong><br>
                        Meskipun status tindak lanjut sudah <strong>closed</strong>, Anda masih dapat menambahkan tindak lanjut baru untuk update atau dokumentasi tambahan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif($currentStatus == 'on_progress')
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-clock-outline me-2"></i>
                        <strong>Status Saat Ini: ON PROGRESS</strong><br>
                        Tindak lanjut sedang dalam proses. Silakan update progress atau ubah status sesuai perkembangan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <!-- Informasi Rekomendasi -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Detail Rekomendasi</h6>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Rekomendasi:</dt>
                                    <dd class="col-sm-8">{{ Str::limit($rekomendasi->rekomendasi, 100) }}</dd>
                                    
                                    <dt class="col-sm-4">Rencana Aksi:</dt>
                                    <dd class="col-sm-8">{{ Str::limit($rekomendasi->rencana_aksi, 100) }}</dd>
                                    
                                    <dt class="col-sm-4">Eviden:</dt>
                                    <dd class="col-sm-8">{{ Str::limit($rekomendasi->eviden_rekomendasi, 100) }}</dd>
                                    
                                    @php
                                        $picItems = [];
                                        if (isset($rekomendasi->picUsers) && $rekomendasi->picUsers->count() > 0) {
                                            foreach ($rekomendasi->picUsers as $user) {
                                                $typeLabel = 'PIC';
                                                $typeClass = 'bg-secondary-subtle text-secondary';
                                                $icon = 'mdi-account';
                                                if ($user->pivot->pic_type === 'business_contact') {
                                                    $typeLabel = 'Business Contact';
                                                    $typeClass = 'bg-primary-subtle text-primary';
                                                    $icon = 'mdi-account-tie';
                                                } elseif ($user->pivot->pic_type === 'approval_1_spi') {
                                                    $typeLabel = 'Business Reviewer 1';
                                                    $typeClass = 'bg-warning-subtle text-warning';
                                                    $icon = 'mdi-account-check';
                                                } elseif ($user->pivot->pic_type === 'approval_2_spi') {
                                                    $typeLabel = 'Business Reviewer 2';
                                                    $typeClass = 'bg-success-subtle text-success';
                                                    $icon = 'mdi-shield-check';
                                                }
                                                $picItems[] = [
                                                    'role' => $typeLabel,
                                                    'class' => $typeClass,
                                                    'icon' => $icon,
                                                    'name' => ucwords(strtolower($user->nama)),
                                                    'dept' => $user->auditee->divisi ?? $user->jabatan ?? '-'
                                                ];
                                            }
                                        } elseif ($rekomendasi->pic_rekomendasi) {
                                            if (strpos($rekomendasi->pic_rekomendasi, ':') !== false) {
                                                $parts = explode('|', $rekomendasi->pic_rekomendasi);
                                                foreach ($parts as $part) {
                                                    $subParts = explode(':', trim($part), 2);
                                                    if (count($subParts) == 2) {
                                                        $role = trim($subParts[0]);
                                                        $personDetails = explode('-', trim($subParts[1]), 2);
                                                        $name = trim($personDetails[0]);
                                                        $dept = isset($personDetails[1]) ? trim($personDetails[1]) : '-';
                                                        
                                                        $typeClass = 'bg-secondary-subtle text-secondary';
                                                        $icon = 'mdi-account';
                                                        if (stripos($role, 'BUSINESS CONTACT') !== false) {
                                                            $role = 'Business Contact';
                                                            $typeClass = 'bg-primary-subtle text-primary';
                                                            $icon = 'mdi-account-tie';
                                                        } elseif (stripos($role, 'APPROVAL 1') !== false || stripos($role, 'APPROVAL_1') !== false || stripos($role, 'BUSINESS REVIEWER 1') !== false || stripos($role, 'BUSINESS_REVIEWER_1') !== false) {
                                                            $role = 'Business Reviewer 1';
                                                            $typeClass = 'bg-warning-subtle text-warning';
                                                            $icon = 'mdi-account-check';
                                                        } elseif (stripos($role, 'APPROVAL 2') !== false || stripos($role, 'APPROVAL_2') !== false || stripos($role, 'BUSINESS REVIEWER 2') !== false || stripos($role, 'BUSINESS_REVIEWER_2') !== false) {
                                                            $role = 'Business Reviewer 2';
                                                            $typeClass = 'bg-success-subtle text-success';
                                                            $icon = 'mdi-shield-check';
                                                        }

                                                        $picItems[] = [
                                                            'role' => $role,
                                                            'class' => $typeClass,
                                                            'icon' => $icon,
                                                            'name' => ucwords(strtolower($name)),
                                                            'dept' => ucwords(strtolower($dept))
                                                        ];
                                                    }
                                                }
                                            } else {
                                                $picItems[] = [
                                                    'role' => 'PIC',
                                                    'class' => 'bg-secondary-subtle text-secondary',
                                                    'icon' => 'mdi-account',
                                                    'name' => $rekomendasi->pic_rekomendasi,
                                                    'dept' => '-'
                                                ];
                                            }
                                        }
                                    @endphp
                                    <dt class="col-sm-4 align-self-start mt-2">PIC:</dt>
                                    <dd class="col-sm-8">
                                        @if(count($picItems) > 0)
                                            <div class="row g-2 mt-0">
                                                @foreach($picItems as $pic)
                                                    <div class="col-12">
                                                        <div class="card mb-0 shadow-none border pic-card" style="border-color: rgba(0,0,0,0.08) !important; border-radius: 12px; background: #fafbfe;">
                                                            <div class="card-body p-2 d-flex align-items-center gap-2">
                                                                <div class="avatar flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle {{ $pic['class'] }}" style="width: 32px; height: 32px;">
                                                                    <i class="mdi {{ $pic['icon'] }} fs-14"></i>
                                                                </div>
                                                                <div class="flex-grow-1 min-width-0">
                                                                    <span class="badge {{ $pic['class'] }} mb-0.5 px-1.5 py-0.2 rounded" style="font-size: 0.6rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">{{ $pic['role'] }}</span>
                                                                    <h6 class="mb-0.5 text-dark fw-bold text-wrap" style="font-size: 0.8rem; word-break: break-word; white-space: normal; line-height: 1.3;" title="{{ $pic['name'] }}">{{ $pic['name'] }}</h6>
                                                                    <p class="text-muted mb-0 d-flex align-items-start" style="font-size: 0.7rem;" title="{{ $pic['dept'] }}">
                                                                        <i class="mdi mdi-office-building-outline me-1 flex-shrink-0" style="margin-top: 2px;"></i>
                                                                        <span class="text-wrap" style="word-break: break-word; white-space: normal; line-height: 1.2;">{{ $pic['dept'] }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-4">Target Waktu:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge {{ $rekomendasi->target_waktu < now() ? 'bg-danger' : 'bg-success' }}">
                                            {{ \Carbon\Carbon::parse($rekomendasi->target_waktu)->format('d/m/Y') }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Riwayat Tindak Lanjut</h6>
                                @if($rekomendasi->tindakLanjut && $rekomendasi->tindakLanjut->count() > 0)
                                    <p class="mb-2"><strong>Total Tindak Lanjut:</strong> {{ $rekomendasi->tindakLanjut->count() }}</p>
                                    <p class="mb-2"><strong>Terakhir Update:</strong> {{ \Carbon\Carbon::parse($rekomendasi->tindakLanjut->sortByDesc('created_at')->first()->created_at)->format('d/m/Y H:i') }}</p>
                                    
                                    @php
                                        $latestTindakLanjut = $rekomendasi->tindakLanjut->sortByDesc('created_at')->first();
                                        $latestStatus = $latestTindakLanjut->status_tindak_lanjut;
                                    @endphp
                                    
                                    <p class="mb-3">
                                        <strong>Status Terbaru:</strong> 
                                        @if($latestStatus == 'closed')
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check-circle me-1"></i>Closed
                                            </span>
                                        @elseif($latestStatus == 'on_progress')
                                            <span class="badge bg-info">
                                                <i class="mdi mdi-clock me-1"></i>On Progress
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="mdi mdi-alert-circle me-1"></i>Open
                                            </span>
                                        @endif
                                    </p>
                                    
                                    <a href="{{ route('audit.pemantauan.tindak-lanjut.index', $rekomendasi->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="mdi mdi-eye me-1"></i>Lihat Riwayat
                                    </a>
                                @else
                                    <p class="text-muted mb-0">Belum ada tindak lanjut</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Form Tindak Lanjut -->
                <form action="{{ route('audit.penutup-lha-rekomendasi.tindak-lanjut.store', $rekomendasi->id) }}" method="POST" enctype="multipart/form-data" id="tindakLanjutForm">
                    @csrf
                    
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Form Tindak Lanjut</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Penyelesaian (Real Waktu)</label>
                                        <input type="date" name="real_waktu" class="form-control" value="{{ old('real_waktu') }}">
                                        <small class="form-text text-muted">Isi tanggal penyelesaian jika tindak lanjut sudah selesai</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden field to preserve current status - status changes can only be done from pemantauan page -->
                            <input type="hidden" name="status_tindak_lanjut" value="{{ $rekomendasi->tindakLanjut->sortByDesc('created_at')->first()?->status_tindak_lanjut ?? $rekomendasi->status_tindak_lanjut ?? 'open' }}">
                            
                            <!-- Dynamic Komentar Fields -->
                            <div class="mb-3">
                                <label class="form-label">Komentar Tindak Lanjut</label>
                                <div id="komentar-container">
                                    <div class="komentar-field mb-2">
                                        <div class="input-group">
                                            <textarea name="komentar[]" class="form-control" rows="3" placeholder="Masukkan komentar tindak lanjut..." required></textarea>
                                            <button type="button" class="btn btn-outline-danger remove-komentar" style="display: none;">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-success btn-sm" id="add-komentar">
                                        <i class="mdi mdi-plus-circle me-1"></i>Tambah Komentar
                                    </button>
                                    <small class="form-text text-muted">Anda bisa menambahkan lebih dari satu komentar untuk tindak lanjut yang berbeda</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Upload Eviden <span class="text-danger">*</span></label>
                                <input type="file" name="file_eviden" id="file_eviden" class="form-control @error('file_eviden') is-invalid @enderror" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                @error('file_eviden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Wajib upload file bukti tindak lanjut (PDF, JPG, PNG, DOC, DOCX — maks. 5MB)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-2"></i>Simpan Tindak Lanjut
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const komentarContainer = document.getElementById('komentar-container');
    const addKomentarBtn = document.getElementById('add-komentar');
    const removeButtons = document.querySelectorAll('.remove-komentar');
    
    // Add new komentar field
    addKomentarBtn.addEventListener('click', function() {
        const komentarField = document.createElement('div');
        komentarField.className = 'komentar-field mb-2';
        komentarField.innerHTML = `
            <div class="input-group">
                <textarea name="komentar[]" class="form-control" rows="3" placeholder="Masukkan komentar tindak lanjut..." required></textarea>
                <button type="button" class="btn btn-outline-danger remove-komentar">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        `;
        
        komentarContainer.appendChild(komentarField);
        updateRemoveButtons();
    });
    
    // Remove komentar field
    komentarContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-komentar')) {
            e.target.closest('.komentar-field').remove();
            updateRemoveButtons();
        }
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const komentarFields = document.querySelectorAll('.komentar-field');
        const removeButtons = document.querySelectorAll('.remove-komentar');
        
        removeButtons.forEach((btn, index) => {
            if (komentarFields.length > 1) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        });
    }
    
    // Initial setup
    updateRemoveButtons();
    
    // Form validation
    document.getElementById('tindakLanjutForm').addEventListener('submit', function(e) {
        const komentarFields = document.querySelectorAll('textarea[name="komentar[]"]');
        let hasContent = false;
        
        komentarFields.forEach(field => {
            if (field.value.trim() !== '') {
                hasContent = true;
            }
        });
        
        if (!hasContent) {
            e.preventDefault();
            alert('Minimal harus ada satu komentar yang diisi!');
            return false;
        }

        // Validasi file eviden wajib
        const fileEviden = document.getElementById('file_eviden');
        if (!fileEviden || !fileEviden.files || fileEviden.files.length === 0) {
            e.preventDefault();
            fileEviden.classList.add('is-invalid');
            // Tampilkan pesan jika belum ada invalid-feedback dari server
            let feedback = fileEviden.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                const msg = document.createElement('div');
                msg.className = 'invalid-feedback d-block';
                msg.textContent = 'File eviden wajib diupload.';
                fileEviden.parentNode.insertBefore(msg, fileEviden.nextSibling);
            }
            fileEviden.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
    });

    // Reset invalid state saat file dipilih
    const fileEvidenInput = document.getElementById('file_eviden');
    if (fileEvidenInput) {
        fileEvidenInput.addEventListener('change', function() {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback.d-block');
            if (feedback) feedback.remove();
        });
    }
});
</script>

<style>
.komentar-field .input-group {
    align-items: flex-start;
}

.komentar-field .input-group .btn {
    margin-left: 10px;
    height: auto;
    align-self: stretch;
}

.komentar-field textarea {
    resize: vertical;
    min-height: 80px;
}

.pic-card {
    transition: all 0.2s ease-in-out;
}
.pic-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
    border-color: rgba(0,0,0,0.15) !important;
    background: #ffffff !important;
}
</style>
@endsection 