@extends('layouts.vertical', ['title' => 'Tambah Pelaporan Hasil Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Input Judul LHA/LHK & Temuan</h4>
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
                <form method="POST" action="{{ route('audit.pelaporan-hasil-audit.store') }}">
                    @csrf
                    <input type="hidden" name="return_url" value="{{ $returnUrl ?? '' }}">
                    
                    <!-- Row 1: Surat Tugas dan Jenis -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Surat Tugas Audit <span class="text-danger">*</span></label>
                            <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-select select2-search" required>
                                <option value="">Pilih Surat Tugas</option>
                                @foreach($suratTugas as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->nomor_surat_tugas }}
                                        @if($s->jenis_audit) · {{ $s->jenis_audit }}@endif
                                        @if($s->auditee) · {{ $s->auditee->divisi }}@endif
                                        @if($s->tanggal_audit_mulai && $s->tanggal_audit_sampai)
                                            · [{{ \Carbon\Carbon::parse($s->tanggal_audit_mulai)->locale('id')->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($s->tanggal_audit_sampai)->locale('id')->translatedFormat('d M Y') }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis_lha_lhk" id="jenis_lha_lhk" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="LHA">LHA</option>
                                <option value="LHK">LHK</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis Audit <span class="text-danger">*</span></label>
                            <select name="jenis_audit_id" id="jenis_audit_id" class="form-select select2-search" required>
                                <option value="">Pilih Jenis Audit</option>
                                @foreach($jenisAudit as $ja)
                                    <option value="{{ $ja->id }}" data-kode="{{ $ja->kode }}">{{ $ja->nama_jenis_audit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Nomor LHA/LHK dan Kode SPI -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor LHA/LHK <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_lha_lhk" id="nomor_lha_lhk" class="form-control" value="" placeholder="xxx/AA/BB/CC/SPI.PCN.yyyy" required>
                            <small class="text-muted">Masukkan nomor LHA/LHK secara manual</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode SPI <span class="text-danger">*</span></label>
                            <input type="text" name="kode_spi" id="kode_spi" class="form-control" value="" placeholder="Kode SPI akan terisi otomatis" required readonly>
                            <small class="text-muted">Kode SPI otomatis terisi dari jenis audit yang dipilih</small>
                        </div>
                    </div>

                    <!-- Row 3: Area ISS -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Area ISS (Issues)</h6>
                                    <small>Tambahkan kode AOI dan risiko untuk generate nomor ISS</small>
                                </div>
                                <div class="card-body">
                                    <div id="iss-container">
                                        <!-- ISS items will be added here -->
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" id="add-iss-btn">
                                        <i class="mdi mdi-plus"></i> Tambah ISS
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Buttons -->
                    <div class="row g-3 mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan
                            </button>
                            <a href="{{ $returnUrl ?? route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-close"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template untuk ISS Item -->
<template id="iss-item-template">
    <div class="iss-item border rounded p-3 mb-3" data-iss-index="">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Hasil Temuan (AOI) <span class="text-danger">*</span></label>
                <textarea name="hasil_temuan[]" class="form-control" rows="2" required></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode AOI <span class="text-danger">*</span></label>
                <select name="kode_aoi_id[]" class="form-select kode-aoi-select select2-iss" required>
                    <option value="">Pilih Kode AOI</option>
                    @foreach($kodeAoi as $aoi)
                        <option value="{{ $aoi->id }}">{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode Risiko <span class="text-danger">*</span></label>
                <select name="kode_risk_id[]" class="form-select kode-risk-select select2-iss" required>
                    <option value="">Pilih Kode Risiko</option>
                    @foreach($kodeRisk as $risk)
                        <option value="{{ $risk->id }}">{{ $risk->kode_risiko }} - {{ $risk->deskripsi_risiko }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nomor ISS</label>
                <input type="text" name="nomor_iss[]" class="form-control nomor-iss-input" placeholder="ISS.xxx/PO_KONSUL/MM/NN/PP/yyyy" readonly>
                <input type="hidden" name="nomor_urut_iss[]" class="nomor-urut-iss-input">
                <button type="button" class="btn btn-outline-primary btn-sm mt-1 generate-iss-btn">
                    <i class="mdi mdi-refresh"></i> Generate
                </button>
            </div>
        </div>
        
        <!-- ISS Detail Fields -->
        <div class="row mt-3">
            <div class="col-md-12">
                <button type="button" class="btn btn-info btn-sm expand-iss-detail-btn" data-bs-toggle="collapse" data-bs-target="#iss-detail-{index}">
                    <i class="mdi mdi-chevron-down"></i> Detail ISS
                </button>
            </div>
        </div>
        
        <div class="collapse" id="iss-detail-{index}">
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Permasalahan <span class="text-danger">*</span></label>
                    <textarea name="permasalahan[]" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kriteria <span class="text-danger">*</span></label>
                    <textarea name="kriteria[]" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dampak yang Terjadi</label>
                    <textarea name="dampak_terjadi[]" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dampak Potensial</label>
                    <textarea name="dampak_potensi[]" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Signifikansi <span class="text-danger">*</span></label>
                    <select name="signifikan[]" class="form-select" required>
                        <option value="">Pilih Signifikansi</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Medium">Medium</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <h6 class="text-muted">Analisis Penyebab (Root Cause Analysis)</h6>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Penyebab <span class="text-danger">*</span></label>
                    <textarea name="penyebab[]" class="form-control" rows="4" placeholder="Jelaskan penyebab dari permasalahan yang ditemukan (People, Process, Policy, System, Eksternal)" required></textarea>
                    <small class="form-text text-muted">
                        <strong>Petunjuk:</strong> Jelaskan penyebab dari berbagai aspek seperti People (SDM), Process (Proses), Policy (Kebijakan), System (Sistem), dan Eksternal (Faktor luar).
                    </small>
                </div>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-iss-btn">
                    <i class="mdi mdi-delete"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</template>


@endsection

@section('script')
<script>
window.addEventListener('load', function() {
    let issIndex = 0;

    // ─── Jenis Audit → auto-fill Kode SPI ─────────────────────────────────────
    // Select2 fires both 'change' and 'select2:select' — bind both just in case
    $('#jenis_audit_id').on('change select2:select', function() {
        const kode = $(this).find('option:selected').data('kode') || '';
        $('#kode_spi').val(kode);
        // Jika kode SPI berubah, reset semua nomor ISS yang ada
        $('.nomor-iss-input').val('');
    });

    // ─── Tambah ISS Item ───────────────────────────────────────────────────────
    $('#add-iss-btn').on('click', function() {
        const template = document.getElementById('iss-item-template');
        const clone    = template.content.cloneNode(true);
        const itemEl   = clone.querySelector('.iss-item');

        // Set index dan update collapse ID sebelum append ke DOM
        itemEl.setAttribute('data-iss-index', issIndex);
        const collapseEl = itemEl.querySelector('.collapse');
        if (collapseEl) collapseEl.setAttribute('id', `iss-detail-${issIndex}`);
        const toggleEl = itemEl.querySelector('[data-bs-target]');
        if (toggleEl) toggleEl.setAttribute('data-bs-target', `#iss-detail-${issIndex}`);

        // Append ke DOM (fragment nodes berpindah ke DOM di sini)
        document.getElementById('iss-container').appendChild(clone);

        // Inisialisasi Select2 pada elemen yang sudah ada di DOM
        const $item = $(`#iss-container [data-iss-index="${issIndex}"]`);
        $item.find('.select2-iss').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({ width: '100%' });
        });

        issIndex++;
    });

    // ─── Event Delegation — semua event pada .iss-item ────────────────────────
    // Gunakan event delegation via $(document) agar berfungsi untuk elemen dinamis

    // Tombol Hapus ISS
    $(document).on('click', '#iss-container .remove-iss-btn', function() {
        $(this).closest('.iss-item').remove();
    });

    // Tombol Generate ISS (manual)
    $(document).on('click', '#iss-container .generate-iss-btn', function() {
        const $item = $(this).closest('.iss-item');
        generateIssNumber($item, false);
    });

    // Auto-generate: dipicu ketika Kode AOI atau Kode Risiko diubah
    // Select2 men-trigger event 'change' pada <select> asli setelah pemilihan
    $(document).on('change', '#iss-container .kode-aoi-select, #iss-container .kode-risk-select', function() {
        const $item  = $(this).closest('.iss-item');
        const aoiVal = $item.find('.kode-aoi-select').val();
        const rskVal = $item.find('.kode-risk-select').val();

        // Reset nomor ISS saat ada perubahan pilihan
        $item.find('.nomor-iss-input').val('');

        // Auto-generate jika kedua dropdown sudah dipilih
        if (aoiVal && rskVal) {
            generateIssNumber($item, true); // silent — tidak tampilkan alert
        }
    });

    // ─── Fungsi Generate Nomor ISS ────────────────────────────────────────────
    // $item   : jQuery object dari .iss-item yang bersangkutan
    // silent  : true = diam jika field belum lengkap | false = tampilkan alert
    function generateIssNumber($item, silent) {
        const nomorLhaLhk = $('#nomor_lha_lhk').val();
        const kodeSpi     = $('#kode_spi').val();
        const kodeAoiId   = $item.find('.kode-aoi-select').val();
        const kodeRiskId  = $item.find('.kode-risk-select').val();
        const perencanaanAuditId = $('#perencanaan_audit_id').val();

        if (!nomorLhaLhk || !kodeSpi || !kodeAoiId || !kodeRiskId || !perencanaanAuditId) {
            if (!silent) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Field Belum Lengkap',
                    text: 'Mohon lengkapi Surat Tugas, Nomor LHA/LHK, Kode SPI, Kode AOI, dan Kode Risiko terlebih dahulu.',
                    confirmButtonText: 'OK'
                });
            }
            return;
        }

        $.ajax({
            url: '{{ route("audit.pelaporan-hasil-audit.generate-nomor-iss") }}',
            type: 'POST',
            data: {
                _token              : '{{ csrf_token() }}',
                perencanaan_audit_id: perencanaanAuditId,
                nomor_lha_lhk       : nomorLhaLhk,
                kode_spi            : kodeSpi,
                kode_aoi_id         : kodeAoiId,
                kode_risk_id        : kodeRiskId
            },
            success: function(response) {
                $item.find('.nomor-iss-input').val(response.nomor_iss);
                $item.find('.nomor-urut-iss-input').val(response.nomor_urut_iss);
            },
            error: function(xhr) {
                console.error('Error generating nomor ISS:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal generate nomor ISS. Periksa console untuk detail.',
                });
            }
        });
    }

    // Tambahkan satu ISS item secara default saat halaman dimuat
    $('#add-iss-btn').trigger('click');
});
</script>
@endsection
