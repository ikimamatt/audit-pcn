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
                        <label for="resiko" class="form-label">Resiko</label>
                        <textarea name="resiko" id="resiko" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="kontrol" class="form-label">Kontrol</label>
                        <textarea name="kontrol" id="kontrol" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="walkthrough_id" class="form-label">Pilih File BPM dari Walkthrough <span class="text-danger">*</span></label>
                        <select name="walkthrough_id" id="walkthrough_id" class="form-control" required>
                            <option value="">Pilih Walkthrough</option>
                        </select>
                        <small class="text-muted">Hanya menampilkan walkthrough yang sudah approved dan memiliki file BPM untuk surat tugas yang dipilih</small>
                        <div id="walkthrough-file-info" class="mt-2" style="display: none;">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                <span id="walkthrough-file-name"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="file_kka_tod" class="form-label">Upload File KKA ToD</label>
                        <input type="file" name="file_kka_tod" id="file_kka_tod" class="form-control" accept=".pdf">
                        <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB) - Opsional</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hasil Evaluasi TOD</label>
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


@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('evaluasi-container');
    
    const walkthroughSelect = document.getElementById('walkthrough_id');
    const perencanaanSelect = document.getElementById('perencanaan_audit_id');
    const walkthroughFileInfo = document.getElementById('walkthrough-file-info');
    const walkthroughFileName = document.getElementById('walkthrough-file-name');
    
    // Walkthrough data dari server
    const walkthroughs = @json($walkthroughs);
    
    // Update walkthrough options berdasarkan perencanaan_audit_id
    function updateWalkthroughOptions() {
        const perencanaanId = perencanaanSelect.value;
        walkthroughSelect.innerHTML = '<option value="">Pilih Walkthrough</option>';
        walkthroughFileInfo.style.display = 'none';
        
        if (perencanaanId && walkthroughs[perencanaanId]) {
            walkthroughs[perencanaanId].forEach(function(walkthrough) {
                const option = document.createElement('option');
                option.value = walkthrough.id;
                option.textContent = 'Walkthrough - ' + (walkthrough.tanggal_walkthrough || 'N/A');
                option.setAttribute('data-file', walkthrough.file_bpm || '');
                walkthroughSelect.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Tidak ada walkthrough dengan file BPM untuk surat tugas ini';
            option.disabled = true;
            walkthroughSelect.appendChild(option);
        }
    }
    
    // Handle perubahan surat tugas
    perencanaanSelect.addEventListener('change', function() {
        updateWalkthroughOptions();
    });
    
    // Handle perubahan walkthrough selection
    walkthroughSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const filePath = selectedOption.getAttribute('data-file');
        
        if (filePath) {
            walkthroughFileName.textContent = 'File BPM: ' + filePath.split('/').pop();
            walkthroughFileInfo.style.display = 'block';
        } else {
            walkthroughFileInfo.style.display = 'none';
        }
    });
    
    // Initialize walkthrough options
    updateWalkthroughOptions();
    
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
    
    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validasi walkthrough dipilih
        if (!walkthroughSelect.value) {
            alert('Silakan pilih walkthrough yang memiliki file BPM');
            walkthroughSelect.classList.add('is-invalid');
            isValid = false;
        } else {
            walkthroughSelect.classList.remove('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            walkthroughSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection 