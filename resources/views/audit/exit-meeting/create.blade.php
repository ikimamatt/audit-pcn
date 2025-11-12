@extends('layouts.vertical', ['title' => 'Tambah Exit Meeting'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </div>
            <h4 class="page-title">Tambah Exit Meeting</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.exit-meeting.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="perencanaan_audit_id" class="form-label">Perencanaan Audit <span class="text-danger">*</span></label>
                                <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-select @error('perencanaan_audit_id') is-invalid @enderror" required>
                                    <option value="">Pilih Perencanaan Audit</option>
                                    @forelse($perencanaanAudits as $perencanaanAudit)
                                        @php
                                            $hasRejectedEntry = false;
                                            foreach($perencanaanAudit->programKerjaAudit as $pka) {
                                                if($pka->entryMeeting && $pka->entryMeeting->status === 'rejected') {
                                                    $hasRejectedEntry = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $perencanaanAudit->id }}" {{ old('perencanaan_audit_id') == $perencanaanAudit->id ? 'selected' : '' }}>
                                            {{ $perencanaanAudit->nomor_surat_tugas }} - 
                                            @if($perencanaanAudit->auditee)
                                                {{ $perencanaanAudit->auditee->divisi }}
                                            @else
                                                -
                                            @endif
                                            ({{ $perencanaanAudit->jenis_audit }})
                                            @if($hasRejectedEntry)
                                                <span class="text-danger"> - Reject (Ajukan Ulang)</span>
                                            @endif
                                        </option>
                                    @empty
                                        <option disabled>Tidak ada perencanaan audit yang tersedia</option>
                                    @endforelse
                                </select>
                                @error('perencanaan_audit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <small class="text-muted">(Otomatis berdasarkan tanggal)</small></label>
                                <input type="text" class="form-control" id="status_display" value="Belum Dimulai" readonly>
                                <input type="hidden" name="status" id="status" value="belum">
                                <small class="text-muted">
                                    <ul class="mb-0 mt-2">
                                        <li>Belum Dimulai: Jika belum ada tanggal mulai</li>
                                        <li>Sedang Berlangsung: Jika ada tanggal mulai tapi belum ada tanggal selesai</li>
                                        <li>Selesai: Jika sudah ada tanggal mulai dan selesai</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                       id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                       id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Upload Dokumen Exit Meeting</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file_undangan" class="form-label">Upload Undangan Exit Meeting <span class="text-danger">*</span></label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="form-control @error('file_undangan') is-invalid @enderror" 
                                           id="file_undangan" name="file_undangan" accept=".pdf" required>
                                    <div class="file-info" id="file_undangan_info"></div>
                                </div>
                                <div class="invalid-feedback" id="file_undangan_error"></div>
                                <div class="valid-feedback" id="file_undangan_success"></div>
                                <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB)</small>
                                
                                <!-- File Preview -->
                                <div class="file-preview" id="file_undangan_preview">
                                    <div class="file-name" id="file_undangan_name"></div>
                                    <div class="file-size" id="file_undangan_size"></div>
                                </div>
                                
                                @error('file_undangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file_absensi" class="form-label">Upload Absensi Exit Meeting <span class="text-danger">*</span></label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="form-control @error('file_absensi') is-invalid @enderror" 
                                           id="file_absensi" name="file_absensi" accept=".pdf" required>
                                    <div class="file-info" id="file_absensi_info"></div>
                                </div>
                                <div class="invalid-feedback" id="file_absensi_error"></div>
                                <div class="valid-feedback" id="file_absensi_success"></div>
                                <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB)</small>
                                
                                <!-- File Preview -->
                                <div class="file-preview" id="file_absensi_preview">
                                    <div class="file-name" id="file_absensi_name"></div>
                                    <div class="file-size" id="file_absensi_size"></div>
                                </div>
                                
                                @error('file_absensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </button>
                        <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
.file-upload-wrapper {
    position: relative;
}

.file-upload-wrapper .form-control {
    padding-right: 40px;
}

.file-upload-wrapper .file-info {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #6c757d;
}

.file-upload-wrapper .file-info.success {
    color: #198754;
}

.file-upload-wrapper .file-info.error {
    color: #dc3545;
}

.is-valid {
    border-color: #198754 !important;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
}

.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.valid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #198754;
}

.file-preview {
    margin-top: 10px;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background-color: #f8f9fa;
    display: none;
}

.file-preview.show {
    display: block;
}

.file-preview .file-name {
    font-weight: 600;
    color: #495057;
}

.file-preview .file-size {
    color: #6c757d;
    font-size: 0.875em;
}
</style>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File validation elements
    const fileUndanganInput = document.getElementById('file_undangan');
    const fileAbsensiInput = document.getElementById('file_absensi');
    const fileUndanganError = document.getElementById('file_undangan_error');
    const fileAbsensiError = document.getElementById('file_absensi_error');
    const fileUndanganSuccess = document.getElementById('file_undangan_success');
    const fileAbsensiSuccess = document.getElementById('file_absensi_success');
    const fileUndanganInfo = document.getElementById('file_undangan_info');
    const fileAbsensiInfo = document.getElementById('file_absensi_info');
    const fileUndanganPreview = document.getElementById('file_undangan_preview');
    const fileAbsensiPreview = document.getElementById('file_absensi_preview');
    const fileUndanganName = document.getElementById('file_undangan_name');
    const fileAbsensiName = document.getElementById('file_absensi_name');
    const fileUndanganSize = document.getElementById('file_undangan_size');
    const fileAbsensiSize = document.getElementById('file_absensi_size');
    
    // File validation function
    function validatePdfFile(file, errorElement, successElement, infoElement, previewElement, nameElement, sizeElement, inputElement) {
        // Reset all states
        errorElement.textContent = '';
        successElement.textContent = '';
        infoElement.textContent = '';
        inputElement.classList.remove('is-invalid', 'is-valid');
        previewElement.classList.remove('show');
        
        if (!file) {
            return false;
        }
        
        // Check file type
        if (file.type !== 'application/pdf') {
            errorElement.textContent = 'Hanya file PDF yang diperbolehkan';
            inputElement.classList.add('is-invalid');
            infoElement.textContent = '❌';
            infoElement.className = 'file-info error';
            return false;
        }
        
        // Check file size (5MB = 5 * 1024 * 1024 bytes)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            errorElement.textContent = 'Ukuran file maksimal 5MB';
            inputElement.classList.add('is-invalid');
            infoElement.textContent = '❌';
            infoElement.className = 'file-info error';
            return false;
        }
        
        // File is valid
        inputElement.classList.add('is-valid');
        successElement.textContent = 'File PDF valid';
        infoElement.textContent = '✅';
        infoElement.className = 'file-info success';
        
        // Show file preview
        nameElement.textContent = file.name;
        sizeElement.textContent = formatFileSize(file.size);
        previewElement.classList.add('show');
        
        return true;
    }
    
    // Format file size function
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // File input change handlers
    fileUndanganInput.addEventListener('change', function() {
        validatePdfFile(
            this.files[0], 
            fileUndanganError, 
            fileUndanganSuccess, 
            fileUndanganInfo, 
            fileUndanganPreview, 
            fileUndanganName, 
            fileUndanganSize, 
            this
        );
    });
    
    fileAbsensiInput.addEventListener('change', function() {
        validatePdfFile(
            this.files[0], 
            fileAbsensiError, 
            fileAbsensiSuccess, 
            fileAbsensiInfo, 
            fileAbsensiPreview, 
            fileAbsensiName, 
            fileAbsensiSize, 
            this
        );
    });
    
    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate undangan file
        if (fileUndanganInput.files.length === 0) {
            fileUndanganError.textContent = 'File undangan harus diupload';
            fileUndanganInput.classList.add('is-invalid');
            isValid = false;
        } else if (!validatePdfFile(
            fileUndanganInput.files[0], 
            fileUndanganError, 
            fileUndanganSuccess, 
            fileUndanganInfo, 
            fileUndanganPreview, 
            fileUndanganName, 
            fileUndanganSize, 
            fileUndanganInput
        )) {
            isValid = false;
        }
        
        // Validate absensi file
        if (fileAbsensiInput.files.length === 0) {
            fileAbsensiError.textContent = 'File absensi harus diupload';
            fileAbsensiInput.classList.add('is-invalid');
            isValid = false;
        } else if (!validatePdfFile(
            fileAbsensiInput.files[0], 
            fileAbsensiError, 
            fileAbsensiSuccess, 
            fileAbsensiInfo, 
            fileAbsensiPreview, 
            fileAbsensiName, 
            fileAbsensiSize, 
            fileAbsensiInput
        )) {
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    
    // Status update function
    function updateStatus() {
        const tanggalMulai = document.getElementById('tanggal_mulai').value;
        const tanggalSelesai = document.getElementById('tanggal_selesai').value;
        const statusInput = document.getElementById('status');
        const statusDisplay = document.getElementById('status_display');
        
        let status = 'belum';
        let statusText = 'Belum Dimulai';
        
        if (tanggalMulai && tanggalSelesai) {
            status = 'selesai';
            statusText = 'Selesai';
        } else if (tanggalMulai && !tanggalSelesai) {
            status = 'on progress';
            statusText = 'Sedang Berlangsung';
        }
        
        statusInput.value = status;
        statusDisplay.value = statusText;
        
        // Update display color
        statusDisplay.className = 'form-control';
        if (status === 'selesai') {
            statusDisplay.style.backgroundColor = '#d4edda';
            statusDisplay.style.color = '#155724';
        } else if (status === 'on progress') {
            statusDisplay.style.backgroundColor = '#fff3cd';
            statusDisplay.style.color = '#856404';
        } else {
            statusDisplay.style.backgroundColor = '#f8f9fa';
            statusDisplay.style.color = '#6c757d';
        }
    }

    // Set minimum date for tanggal_selesai based on tanggal_mulai and update status
    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        document.getElementById('tanggal_selesai').min = this.value;
        updateStatus();
    });
    
    // Update status when tanggal_selesai changes
    document.getElementById('tanggal_selesai').addEventListener('change', function() {
        updateStatus();
    });
    
    // Initialize status on page load
    updateStatus();
});
</script>
@endsection 