@php
    $auditors = $auditors ?? collect();
    $auditees = $auditees ?? collect();
@endphp
@extends('layouts.vertical', ['title' => 'Edit Perencanaan Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">EDIT PERENCANAAN AUDIT</h4>
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
                @if(session('success'))
                    @include('components.alert')
                @endif
                <form id="form-perencanaan-audit" method="POST" action="{{ route('audit.perencanaan.update', $item->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Tanggal Surat Tugas</label>
                        <input type="date" name="tanggal_surat_tugas" class="form-control" value="{{ old('tanggal_surat_tugas', $item->tanggal_surat_tugas) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Surat Tugas <small class="text-muted">(Otomatis)</small></label>
                        <input type="text" name="nomor_surat_tugas" id="nomor_surat_tugas" class="form-control" value="{{ old('nomor_surat_tugas', $item->nomor_surat_tugas) }}" readonly>
                        <small class="text-info">Nomor akan otomatis ter-generate berdasarkan jenis audit yang dipilih</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Audit</label>
                        <select name="jenis_audit" id="jenis_audit" class="form-select" required>
                            <option value="">Pilih Jenis Audit</option>
                            <option value="Audit Operasional" {{ old('jenis_audit', $item->jenis_audit) == 'Audit Operasional' ? 'selected' : '' }}>Audit Operasional - SPI.01.02</option>
                            <option value="Audit Khusus" {{ old('jenis_audit', $item->jenis_audit) == 'Audit Khusus' ? 'selected' : '' }}>Audit Khusus - SPI.01.03</option>
                            <option value="Konsultasi" {{ old('jenis_audit', $item->jenis_audit) == 'Konsultasi' ? 'selected' : '' }}>Konsultasi - SPI.01.04</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Auditor</label>
                        <div id="auditor-list">
                            @php 
                                // Gunakan matched_auditor_ids jika ada, jika tidak gunakan array kosong untuk dropdown baru
                                $auditorList = old('auditor', $item->matched_auditor_ids ?? []);
                                // Jika tidak ada matched, buat satu dropdown kosong
                                if (empty($auditorList)) {
                                    $auditorList = [''];
                                }
                            @endphp
                            @foreach($auditorList as $i => $aud)
                            <div class="input-group mb-2 auditor-item">
                                <select name="auditor[]" class="form-select auditor-select" required>
                                    <option value="">Pilih Auditor</option>
                                    @foreach($auditors as $auditor)
                                        <option value="{{ $auditor->id }}" 
                                            data-nama="{{ $auditor->nama }}" 
                                            data-nip="{{ $auditor->nip }}"
                                            {{ (is_numeric($aud) && $aud == $auditor->id) || $aud == $auditor->id ? 'selected' : '' }}>
                                            {{ $auditor->nama }} - {{ $auditor->nip }} ({{ $auditor->akses->nama_akses ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-danger btn-remove-auditor" @if($i==0 && count($auditorList) == 1) style="display:none" @endif>-</button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-info" id="btn-add-auditor">Tambah Auditor</button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Auditee</label>
                        <select name="auditee" class="form-select" required>
                            <option value="">Pilih Auditee</option>
                            @foreach($auditees as $auditee)
                                <option value="{{ $auditee->id }}" {{ old('auditee', $item->auditee_id) == $auditee->id ? 'selected' : '' }}>{{ $auditee->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ruang Lingkup</label>
                        <div id="ruang-lingkup-list">
                            @php $ruangLingkup = old('ruang_lingkup', is_array($item->ruang_lingkup) ? $item->ruang_lingkup : [$item->ruang_lingkup]); @endphp
                            @foreach($ruangLingkup as $i => $rl)
                            <div class="input-group mb-2 ruang-lingkup-item">
                                <input type="text" name="ruang_lingkup[]" class="form-control" value="{{ $rl }}" required>
                                <button type="button" class="btn btn-danger btn-remove-rl" @if($i==0) style="display:none" @endif>-</button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-info" id="btn-add-rl">Tambah Ruang Lingkup</button>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Audit (mulai)</label>
                            <input type="date" name="tanggal_audit_mulai" class="form-control" value="{{ old('tanggal_audit_mulai', $item->tanggal_audit_mulai) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Audit (sampai)</label>
                            <input type="date" name="tanggal_audit_sampai" class="form-control" value="{{ old('tanggal_audit_sampai', $item->tanggal_audit_sampai) }}" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Periode Audit (awal)</label>
                            <input type="text" name="periode_awal" class="form-control" value="{{ old('periode_awal', $item->periode_awal) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode Audit (akhir)</label>
                            <input type="text" name="periode_akhir" class="form-control" value="{{ old('periode_akhir', $item->periode_akhir) }}" required>
                        </div>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('audit.perencanaan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Event listener untuk perubahan jenis audit
    document.getElementById('jenis_audit').addEventListener('change', function() {
        const jenisAudit = this.value;
        if (jenisAudit) {
            // Panggil API untuk mendapatkan nomor surat tugas otomatis
            fetch(`{{ route('audit.perencanaan.get-nomor-surat-tugas') }}?jenis_audit=${encodeURIComponent(jenisAudit)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('nomor_surat_tugas').value = data.nomor_surat_tugas;
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback ke generate lokal jika API gagal
                    const nomorSuratTugas = generateNomorSuratTugas(jenisAudit);
                    document.getElementById('nomor_surat_tugas').value = nomorSuratTugas;
                });
        }
    });
    
    // Fungsi fallback untuk generate nomor surat tugas (jika API gagal)
    function generateNomorSuratTugas(jenisAudit) {
        const tahun = new Date().getFullYear();
        let kodeJenis = '02'; // Default untuk audit operasional
        
        // Mapping jenis audit ke kode
        switch (jenisAudit.toLowerCase()) {
            case 'audit operasional':
                kodeJenis = '02';
                break;
            case 'audit khusus':
                kodeJenis = '03';
                break;
            case 'konsultasi':
                kodeJenis = '04';
                break;
            default:
                kodeJenis = '02';
                break;
        }
        
        // Untuk demo, kita gunakan nomor urut 001
        const nomorUrut = '001';
        
        return `${nomorUrut}.STG/SPI.01.${kodeJenis}/SPI-PCN/${tahun}`;
    }
    
    // Ruang lingkup dinamis
    document.getElementById('btn-add-rl').onclick = function() {
        var list = document.getElementById('ruang-lingkup-list');
        var item = document.createElement('div');
        item.className = 'input-group mb-2 ruang-lingkup-item';
        item.innerHTML = '<input type="text" name="ruang_lingkup[]" class="form-control" required> <button type="button" class="btn btn-danger btn-remove-rl">-</button>';
        list.appendChild(item);
        item.querySelector('.btn-remove-rl').onclick = function() { item.remove(); };
    };
    document.querySelectorAll('.btn-remove-rl').forEach(function(btn) {
        btn.onclick = function() { btn.closest('.ruang-lingkup-item').remove(); };
    });
    
    // Auditor dinamis
    document.getElementById('btn-add-auditor').onclick = function() {
        var list = document.getElementById('auditor-list');
        var item = document.createElement('div');
        item.className = 'input-group mb-2 auditor-item';
        
        // Buat select dropdown dengan opsi auditor
        var selectHtml = '<select name="auditor[]" class="form-select auditor-select" required><option value="">Pilih Auditor</option>';
        @foreach($auditors as $auditor)
            selectHtml += '<option value="{{ $auditor->id }}" data-nama="{{ $auditor->nama }}" data-nip="{{ $auditor->nip }}">{{ $auditor->nama }} - {{ $auditor->nip }} ({{ $auditor->akses->nama_akses ?? "-" }})</option>';
        @endforeach
        selectHtml += '</select>';
        
        item.innerHTML = selectHtml + ' <button type="button" class="btn btn-danger btn-remove-auditor">-</button>';
        list.appendChild(item);
        
        // Event handler untuk remove button
        item.querySelector('.btn-remove-auditor').onclick = function() { 
            item.remove(); 
            // Update visibility tombol remove untuk item pertama
            updateRemoveButtons();
        };
        
        // Update visibility tombol remove
        updateRemoveButtons();
    };
    
    // Update visibility tombol remove
    function updateRemoveButtons() {
        var items = document.querySelectorAll('.auditor-item');
        items.forEach(function(item, index) {
            var removeBtn = item.querySelector('.btn-remove-auditor');
            if (removeBtn) {
                removeBtn.style.display = items.length > 1 ? 'block' : 'none';
            }
        });
    }
    
    // Event handler untuk remove button yang sudah ada
    document.querySelectorAll('.btn-remove-auditor').forEach(function(btn) {
        btn.onclick = function() { 
            btn.closest('.auditor-item').remove(); 
            updateRemoveButtons();
        };
    });
    
    // Inisialisasi visibility tombol remove saat halaman dimuat
    updateRemoveButtons();
</script>
@endsection 
