@extends('layouts.vertical', ['title' => 'Tambah BPM Audit (TOD)'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Tambah BPM Audit (TOD)</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.tod-bpm.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="perencanaan_audit_id" class="form-label">Surat Tugas Audit</label>
                        <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-control" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}">{{ $st->nomor_surat_tugas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="judul_bpm" class="form-label">Judul BPM</label>
                        <textarea name="judul_bpm" id="judul_bpm" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nama_bpo" class="form-label">Nama BPO</label>
                        <textarea name="nama_bpo" id="nama_bpo" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file_bpm" class="form-label">Upload File BPM <span class="text-danger">*</span></label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="file_bpm" id="file_bpm" class="form-control" accept=".pdf" required>
                            <div class="file-info" id="file_bpm_info"></div>
                        </div>
                        <div class="invalid-feedback" id="file_bpm_error"></div>
                        <div class="valid-feedback" id="file_bpm_success"></div>
                        <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB)</small>
                        
                        <!-- File Preview -->
                        <div class="file-preview" id="file_bpm_preview">
                            <div class="file-name" id="file_bpm_name"></div>
                            <div class="file-size" id="file_bpm_size"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hasil Evaluasi BPM</label>
                        <div id="evaluasi-container">
                            <div class="input-group mb-2 evaluasi-item">
                                <textarea name="hasil_evaluasi[]" class="form-control" rows="2" required></textarea>
                                <button type="button" class="btn btn-danger btn-remove-evaluasi">-</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-evaluasi">Tambah Evaluasi</button>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('audit.tod-bpm.index') }}" class="btn btn-secondary">Batal</a>
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
    const container = document.getElementById('evaluasi-container');
    
    // File validation elements
    const fileBpmInput = document.getElementById('file_bpm');
    const fileBpmError = document.getElementById('file_bpm_error');
    const fileBpmSuccess = document.getElementById('file_bpm_success');
    const fileBpmInfo = document.getElementById('file_bpm_info');
    const fileBpmPreview = document.getElementById('file_bpm_preview');
    const fileBpmName = document.getElementById('file_bpm_name');
    const fileBpmSize = document.getElementById('file_bpm_size');
    
    // Add evaluasi functionality
    document.getElementById('btn-add-evaluasi').onclick = function() {
        const div = document.createElement('div');
        div.className = 'input-group mb-2 evaluasi-item';
        div.innerHTML = `<textarea name="hasil_evaluasi[]" class="form-control" rows="2" required></textarea><button type="button" class="btn btn-danger btn-remove-evaluasi">-</button>`;
        container.appendChild(div);
    };
    
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-evaluasi')) {
            if (container.querySelectorAll('.evaluasi-item').length > 1) {
                e.target.parentElement.remove();
            }
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
    
    // File input change handler
    fileBpmInput.addEventListener('change', function() {
        validatePdfFile(
            this.files[0], 
            fileBpmError, 
            fileBpmSuccess, 
            fileBpmInfo, 
            fileBpmPreview, 
            fileBpmName, 
            fileBpmSize, 
            this
        );
    });
    
    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate BPM file
        if (fileBpmInput.files.length === 0) {
            fileBpmError.textContent = 'File BPM harus diupload';
            fileBpmInput.classList.add('is-invalid');
            isValid = false;
        } else if (!validatePdfFile(
            fileBpmInput.files[0], 
            fileBpmError, 
            fileBpmSuccess, 
            fileBpmInfo, 
            fileBpmPreview, 
            fileBpmName, 
            fileBpmSize, 
            fileBpmInput
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