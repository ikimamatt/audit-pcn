@extends('layouts.vertical', ['title' => 'Tambah Entry Meeting'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Tambah Entry Meeting</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.entry-meeting.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="program_kerja_audit_id" class="form-label">Program Kerja Audit</label>
                        @if($programKerjaAudit->count() > 0)
                            <select name="program_kerja_audit_id" id="program_kerja_audit_id" class="form-control" required>
                                <option value="">Pilih Program Kerja Audit</option>
                                @foreach($programKerjaAudit as $pka)
                                @php
                                    $entryMeetingMilestone = $pka->milestones ? $pka->milestones->where('nama_milestone', 'Entry Meeting')->first() : null;
                                    $rejectedEntryMeeting = $pka->entryMeeting ? $pka->entryMeeting->where('status_approval', 'rejected')->first() : null;
                                @endphp
                                <option value="{{ $pka->id }}" data-planned-date="{{ $entryMeetingMilestone ? $entryMeetingMilestone->tanggal_mulai : '' }}">
                                    {{ $pka->no_pka }} - {{ $pka->perencanaanAudit ? $pka->perencanaanAudit->nomor_surat_tugas : 'N/A' }}
                                    @if($rejectedEntryMeeting)
                                        <span class="text-danger">(Reject - Ajukan Ulang)</span>
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-circle"></i>
                                <strong>Tidak ada Program Kerja Audit yang tersedia!</strong><br>
                                Semua Program Kerja Audit sudah memiliki Entry Meeting yang approved atau pending.
                                <br><br>
                                <a href="{{ route('audit.perencanaan.create') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus"></i> Buat Surat Tugas Baru
                                </a>
                            </div>
                        @endif
                        <small class="text-muted">Pilih Program Kerja Audit yang memiliki milestone Entry Meeting</small>
                    </div>
                    <div class="mb-3">
                        <label for="planned_meeting_date" class="form-label">Planned Meeting Date</label>
                        <input type="date" name="planned_meeting_date" id="planned_meeting_date" class="form-control" required readonly>
                        <small class="text-muted">Tanggal meeting sesuai milestone Entry Meeting</small>
                    </div>
                    <div class="mb-3">
                        <label for="actual_meeting_date" class="form-label">Actual Meeting Date</label>
                        <input type="date" name="actual_meeting_date" id="actual_meeting_date" class="form-control">
                        <small class="text-muted">Tanggal meeting yang sebenarnya dilaksanakan (opsional)</small>
                    </div>
                    <div class="mb-3">
                        <label for="auditee_id" class="form-label">Nama Auditee</label>
                        <select name="auditee_id" id="auditee_id" class="form-control" required>
                            <option value="">Pilih Auditee</option>
                            @foreach($auditees as $auditee)
                                <option value="{{ $auditee->id }}">{{ $auditee->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_undangan" class="form-label">Upload Undangan Entry Meeting <span class="text-danger">*</span></label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="file_undangan" id="file_undangan" class="form-control" accept=".pdf" required>
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
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_absensi" class="form-label">Upload Absensi Entry Meeting <span class="text-danger">*</span></label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="file_absensi" id="file_absensi" class="form-control" accept=".pdf" required>
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
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('audit.entry-meeting.index') }}" class="btn btn-secondary">Batal</a>
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
    const pkaSelect = document.getElementById('program_kerja_audit_id');
    const plannedDateInput = document.getElementById('planned_meeting_date');
    const submitButton = document.querySelector('button[type="submit"]');
    
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
    
    // Disable submit button if no PKA available
    if (!pkaSelect) {
        if (submitButton) {
            submitButton.disabled = true;
        }
        return;
    }
    
    // PKA selection change handler
    pkaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const plannedDate = selectedOption.getAttribute('data-planned-date');
        
        if (plannedDate) {
            plannedDateInput.value = plannedDate;
        } else {
            plannedDateInput.value = '';
        }
    });
    
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
});
</script>
@endsection 