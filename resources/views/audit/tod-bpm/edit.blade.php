@extends('layouts.vertical', ['title' => 'Edit BPM Audit (TOD)'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit BPM Audit (TOD)</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.tod-bpm.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label for="perencanaan_audit_id" class="form-label">Surat Tugas Audit</label>
                        <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-control" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}" {{ $item->perencanaan_audit_id == $st->id ? 'selected' : '' }}>{{ $st->nomor_surat_tugas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="judul_bpm" class="form-label">Judul BPM</label>
                        <textarea name="judul_bpm" id="judul_bpm" class="form-control" rows="2" required>{{ $item->judul_bpm }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nama_bpo" class="form-label">Nama BPO</label>
                        <textarea name="nama_bpo" id="nama_bpo" class="form-control" rows="2" required>{{ $item->nama_bpo }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="resiko" class="form-label">Resiko</label>
                        <textarea name="resiko" id="resiko" class="form-control" rows="3">{{ $item->resiko }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="kontrol" class="form-label">Kontrol</label>
                        <textarea name="kontrol" id="kontrol" class="form-control" rows="3">{{ $item->kontrol }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file_bpm" class="form-label">File BPM</label>
                        @if($item->file_bpm)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_bpm) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="mdi mdi-download me-1"></i> Download File Saat Ini
                                </a>
                            </div>
                        @endif
                        <label for="walkthrough_id" class="form-label">Ganti dengan File dari Walkthrough (Opsional)</label>
                        <select name="walkthrough_id" id="walkthrough_id" class="form-control">
                            <option value="">Pertahankan File Saat Ini</option>
                        </select>
                        <small class="text-muted">Pilih walkthrough untuk mengganti file BPM. Kosongkan untuk mempertahankan file saat ini.</small>
                    </div>
                    <div class="mb-3">
                        <label for="file_kka_tod" class="form-label">File KKA ToD</label>
                        @if($item->file_kka_tod)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_kka_tod) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="mdi mdi-download me-1"></i> Download File Saat Ini
                                </a>
                            </div>
                        @endif
                        <input type="file" name="file_kka_tod" id="file_kka_tod" class="form-control" accept=".pdf">
                        <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB) - Kosongkan jika tidak ingin mengganti file</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
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
    const walkthroughSelect = document.getElementById('walkthrough_id');
    const perencanaanSelect = document.getElementById('perencanaan_audit_id');
    
    // Walkthrough data dari server
    const walkthroughs = @json($walkthroughs);
    
    // Update walkthrough options berdasarkan perencanaan_audit_id
    function updateWalkthroughOptions() {
        const perencanaanId = perencanaanSelect.value;
        const currentOptions = walkthroughSelect.innerHTML;
        walkthroughSelect.innerHTML = '<option value="">Pertahankan File Saat Ini</option>';
        
        if (perencanaanId && walkthroughs[perencanaanId]) {
            walkthroughs[perencanaanId].forEach(function(walkthrough) {
                const option = document.createElement('option');
                option.value = walkthrough.id;
                option.textContent = 'Walkthrough - ' + (walkthrough.tanggal_walkthrough || 'N/A');
                walkthroughSelect.appendChild(option);
            });
        }
    }
    
    // Handle perubahan surat tugas
    perencanaanSelect.addEventListener('change', function() {
        updateWalkthroughOptions();
    });
    
    // Initialize walkthrough options
    updateWalkthroughOptions();
});
</script>
@endsection 