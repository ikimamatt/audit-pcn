@extends('layouts.vertical', ['title' => 'Edit TOE Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit TOE Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.toe.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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
                        <select name="judul_bpm" id="judul_bpm" class="form-control" required>
                            <option value="">Pilih Judul BPM</option>
                            @php
                                $currentJudulBpm = old('judul_bpm', $item->judul_bpm ?? '');
                                $judulBpmFound = false;
                                $currentJudulBpmTrimmed = trim($currentJudulBpm);
                            @endphp
                            @foreach($bpmList as $bpm)
                                @php
                                    $bpmJudulTrimmed = trim($bpm->judul_bpm ?? '');
                                    // Perbandingan case-insensitive dan trim
                                    $isSelected = strcasecmp($currentJudulBpmTrimmed, $bpmJudulTrimmed) === 0 || $currentJudulBpmTrimmed === $bpmJudulTrimmed;
                                    if ($isSelected) {
                                        $judulBpmFound = true;
                                    }
                                @endphp
                                <option value="{{ $bpm->judul_bpm }}" {{ $isSelected ? 'selected' : '' }}>{{ $bpm->judul_bpm }}</option>
                            @endforeach
                            @if($currentJudulBpmTrimmed && !$judulBpmFound)
                                <option value="{{ $currentJudulBpm }}" selected>{{ $currentJudulBpm }} (Tidak ada di list)</option>
                            @endif
                        </select>
                        @if($currentJudulBpm && !$judulBpmFound)
                            <small class="text-warning">Judul BPM yang dipilih tidak ditemukan di list saat ini. Pastikan untuk memilih ulang dari list.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="pengendalian_eksisting" class="form-label">Pengendalian Eksisting</label>
                        <textarea name="pengendalian_eksisting" id="pengendalian_eksisting" class="form-control" rows="2" required>{{ $item->pengendalian_eksisting }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pemilihan_sampel_audit" class="form-label">Pemilihan Sampel Audit</label>
                        <textarea name="pemilihan_sampel_audit" id="pemilihan_sampel_audit" class="form-control" rows="3">{{ $item->pemilihan_sampel_audit }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resiko</label>
                        <div id="resiko-container">
                            @php
                                $resikoArray = $item->resiko ? (is_string($item->resiko) && (strpos($item->resiko, '[') === 0 || strpos($item->resiko, '{') === 0) ? json_decode($item->resiko, true) : [$item->resiko]) : [];
                                $kontrolArray = $item->kontrol ? (is_string($item->kontrol) && (strpos($item->kontrol, '[') === 0 || strpos($item->kontrol, '{') === 0) ? json_decode($item->kontrol, true) : [$item->kontrol]) : [];
                                // Ensure both arrays have the same length
                                $maxLength = max(count($resikoArray), count($kontrolArray));
                                if (empty($resikoArray) && empty($kontrolArray)) {
                                    $resikoArray = [''];
                                    $kontrolArray = [''];
                                }
                            @endphp
                            @foreach($resikoArray as $index => $resiko)
                                <div class="resiko-item mb-3 border p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>Resiko <span class="resiko-number">{{ $index + 1 }}</span></strong>
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-resiko">Hapus</button>
                                    </div>
                                    <textarea name="resiko[]" class="form-control resiko-input" rows="2" placeholder="Masukkan resiko">{{ $resiko }}</textarea>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-resiko">Tambah Resiko</button>
                        <small class="text-muted d-block mt-2">Resiko akan otomatis terisi dari PKA saat surat tugas dipilih</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontrol</label>
                        <div id="kontrol-container">
                            @foreach($kontrolArray as $index => $kontrol)
                                <div class="kontrol-item mb-3 border p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>Kontrol <span class="kontrol-number">{{ $index + 1 }}</span></strong>
                                    </div>
                                    <textarea name="kontrol[]" class="form-control kontrol-input" rows="2" placeholder="Masukkan kontrol">{{ $kontrol }}</textarea>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2">Kontrol harus diisi manual untuk setiap resiko yang dipilih</small>
                    </div>
                    <div class="mb-3">
                        <label for="file_kka_toe" class="form-label">File KKA ToE</label>
                        @if($item->file_kka_toe)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_kka_toe) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="mdi mdi-download me-1"></i> Download File Saat Ini
                                </a>
                            </div>
                        @endif
                        <input type="file" name="file_kka_toe" id="file_kka_toe" class="form-control" accept=".pdf">
                        <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB) - Kosongkan jika tidak ingin mengganti file</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('audit.toe.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const perencanaanSelect = document.getElementById('perencanaan_audit_id');
    
    // Handle perubahan surat tugas
    perencanaanSelect.addEventListener('change', function() {
        loadRisksFromPKA();
    });
    
    // Load risks from PKA
    function loadRisksFromPKA() {
        const perencanaanId = perencanaanSelect.value;
        if (!perencanaanId) {
            return;
        }
        
        fetch(`{{ url('audit/toe/get-risks') }}/${perencanaanId}`)
            .then(response => response.json())
            .then(data => {
                if (data.risks && data.risks.length > 0) {
                    // Clear existing resiko and kontrol
                    document.getElementById('resiko-container').innerHTML = '';
                    document.getElementById('kontrol-container').innerHTML = '';
                    
                    // Add risks from PKA
                    data.risks.forEach((risk, index) => {
                        addResikoItem(risk.deskripsi_resiko, ''); // Kontrol selalu kosong
                    });
                    
                    updateResikoNumbers();
                    updateKontrolNumbers();
                }
            })
            .catch(error => {
                console.error('Error loading risks:', error);
            });
    }
    
    // Add resiko item
    function addResikoItem(resikoText = '', kontrolText = '') {
        const resikoContainer = document.getElementById('resiko-container');
        const kontrolContainer = document.getElementById('kontrol-container');
        const resikoIndex = resikoContainer.children.length;
        
        // Create resiko item
        const resikoItem = document.createElement('div');
        resikoItem.className = 'resiko-item mb-3 border p-3 rounded';
        resikoItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Resiko <span class="resiko-number">${resikoIndex + 1}</span></strong>
                <button type="button" class="btn btn-sm btn-danger btn-remove-resiko">Hapus</button>
            </div>
            <textarea name="resiko[]" class="form-control resiko-input" rows="2" placeholder="Masukkan resiko">${resikoText}</textarea>
        `;
        resikoContainer.appendChild(resikoItem);
        
        // Create corresponding kontrol item
        const kontrolItem = document.createElement('div');
        kontrolItem.className = 'kontrol-item mb-3 border p-3 rounded';
        kontrolItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Kontrol <span class="kontrol-number">${resikoIndex + 1}</span></strong>
            </div>
            <textarea name="kontrol[]" class="form-control kontrol-input" rows="2" placeholder="Masukkan kontrol">${kontrolText}</textarea>
        `;
        kontrolContainer.appendChild(kontrolItem);
        
        // Add event listener for remove button
        resikoItem.querySelector('.btn-remove-resiko').addEventListener('click', function() {
            removeResikoItem(resikoItem, kontrolItem);
        });
    }
    
    // Remove resiko item
    function removeResikoItem(resikoItem, kontrolItem) {
        const resikoContainer = document.getElementById('resiko-container');
        if (resikoContainer.children.length > 1) {
            resikoItem.remove();
            kontrolItem.remove();
            updateResikoNumbers();
            updateKontrolNumbers();
        } else {
            alert('Minimal harus ada 1 resiko');
        }
    }
    
    // Update resiko numbers
    function updateResikoNumbers() {
        const resikoItems = document.querySelectorAll('.resiko-item');
        resikoItems.forEach((item, index) => {
            item.querySelector('.resiko-number').textContent = index + 1;
        });
    }
    
    // Update kontrol numbers
    function updateKontrolNumbers() {
        const kontrolItems = document.querySelectorAll('.kontrol-item');
        kontrolItems.forEach((item, index) => {
            item.querySelector('.kontrol-number').textContent = index + 1;
        });
    }
    
    // Add resiko button
    document.getElementById('btn-add-resiko').addEventListener('click', function() {
        addResikoItem();
        updateResikoNumbers();
        updateKontrolNumbers();
    });
    
    // Handle remove resiko clicks (for dynamically added items)
    document.getElementById('resiko-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-resiko')) {
            const resikoItem = e.target.closest('.resiko-item');
            const resikoIndex = Array.from(document.querySelectorAll('.resiko-item')).indexOf(resikoItem);
            const kontrolItem = document.querySelectorAll('.kontrol-item')[resikoIndex];
            removeResikoItem(resikoItem, kontrolItem);
        }
    });
});
</script>
@endsection 