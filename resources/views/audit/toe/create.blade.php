@extends('layouts.vertical', ['title' => 'Tambah TOE Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Tambah TOE Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.toe.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="perencanaan_audit_id" class="form-label">Surat Tugas Audit</label>
                        <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-control select2-search" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->nomor_surat_tugas }}
                                    @if($st->jenis_audit) · {{ $st->jenis_audit }}@endif
                                    @if($st->auditee) · {{ $st->auditee->divisi }}@endif
                                    @if($st->tanggal_audit_mulai && $st->tanggal_audit_sampai)
                                        · [{{ \Carbon\Carbon::parse($st->tanggal_audit_mulai)->locale('id')->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($st->tanggal_audit_sampai)->locale('id')->translatedFormat('d M Y') }}]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="judul_bpm" class="form-label">Judul BPM</label>
                        <select name="judul_bpm" id="judul_bpm" class="form-control select2-search" required>
                            <option value="">-- Pilih Surat Tugas terlebih dahulu --</option>
                        </select>
                        <small class="text-muted">Judul BPM akan muncul setelah Surat Tugas dipilih</small>
                    </div>
                    <div class="mb-3">
                        <label for="pengendalian_eksisting" class="form-label">Pengendalian Eksisting</label>
                        <textarea name="pengendalian_eksisting" id="pengendalian_eksisting" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pemilihan_sampel_audit" class="form-label">Pemilihan Sampel Audit</label>
                        <textarea name="pemilihan_sampel_audit" id="pemilihan_sampel_audit" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resiko</label>
                        <div id="resiko-container">
                            <div class="resiko-item mb-3 border p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Resiko <span class="resiko-number">1</span></strong>
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-resiko">Hapus</button>
                                </div>
                                <textarea name="resiko[]" class="form-control resiko-input" rows="2" placeholder="Masukkan resiko"></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-resiko">Tambah Resiko</button>
                        <small class="text-muted d-block mt-2">Resiko akan otomatis terisi dari PKA saat surat tugas dipilih</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontrol</label>
                        <div id="kontrol-container">
                            <div class="kontrol-item mb-3 border p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Kontrol <span class="kontrol-number">1</span></strong>
                                </div>
                                <textarea name="kontrol[]" class="form-control kontrol-input" rows="2" placeholder="Masukkan kontrol"></textarea>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Kontrol harus diisi manual untuk setiap resiko yang dipilih</small>
                    </div>
                    <div class="mb-3">
                        <label for="file_kka_toe" class="form-label">Upload File KKA ToE</label>
                        <input type="file" name="file_kka_toe" id="file_kka_toe" class="form-control" accept=".pdf">
                        <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB) - Opsional</small>
                    </div>
                    <div class="mb-3">
                        <label for="hasil_evaluasi" class="form-label">Evaluasi Pengendalian</label>
                        <select name="hasil_evaluasi" id="hasil_evaluasi" class="form-control" required>
                            <option value="">Pilih Hasil Evaluasi</option>
                            <option value="Efektif">Efektif</option>
                            <option value="Tidak Efektif">Tidak Efektif</option>
                            <option value="Efektif Sebagian">Efektif Sebagian</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('audit.toe.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $bpmJson = $bpmList->map(fn($b) => [
        'id'                   => $b->id,
        'judul_bpm'            => $b->judul_bpm,
        'perencanaan_audit_id' => $b->perencanaan_audit_id,
    ])->values();
@endphp

@section('script')
<script>
// Data semua TOD BPM dari server, dikelompokkan per perencanaan_audit_id
const allBpmData = @json($bpmJson);

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('evaluasi-container');
    const perencanaanSelect = document.getElementById('perencanaan_audit_id');
    const judulBpmSelect    = document.getElementById('judul_bpm');

    // Filter dropdown Judul BPM sesuai surat tugas yang dipilih
    function filterBpmByPerencanaan(perencanaanId) {
        judulBpmSelect.innerHTML = '';

        if (!perencanaanId) {
            judulBpmSelect.innerHTML = '<option value="">-- Pilih Surat Tugas terlebih dahulu --</option>';
            return;
        }

        const filtered = allBpmData.filter(b => b.perencanaan_audit_id == perencanaanId);

        if (filtered.length === 0) {
            judulBpmSelect.innerHTML = '<option value="">Tidak ada TOD BPM untuk surat tugas ini</option>';
            return;
        }

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Pilih Judul BPM';
        judulBpmSelect.appendChild(placeholder);

        filtered.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.judul_bpm;
            opt.textContent = b.judul_bpm;
            judulBpmSelect.appendChild(opt);
        });
    }

    // Handle perubahan surat tugas
    perencanaanSelect.addEventListener('change', function() {
        filterBpmByPerencanaan(this.value);
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