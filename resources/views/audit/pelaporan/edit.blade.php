@extends('layouts.vertical', ['title' => 'Edit Pelaporan Hasil Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Edit Judul LHA/LHK &amp; Temuan</h4>
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
                <form method="POST" action="{{ route('audit.pelaporan-hasil-audit.update', $item->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Row 1: Surat Tugas dan Jenis -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Surat Tugas Audit <span class="text-danger">*</span></label>
                            <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-select select2-search" required>
                                <option value="">Pilih Surat Tugas</option>
                                @foreach($suratTugas as $s)
                                    <option value="{{ $s->id }}" {{ $item->perencanaan_audit_id == $s->id ? 'selected' : '' }}>
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
                                <option value="LHA" {{ $item->jenis_lha_lhk == 'LHA' ? 'selected' : '' }}>LHA</option>
                                <option value="LHK" {{ $item->jenis_lha_lhk == 'LHK' ? 'selected' : '' }}>LHK</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis Audit <span class="text-danger">*</span></label>
                            <select name="jenis_audit_id" id="jenis_audit_id" class="form-select select2-search" required>
                                <option value="">Pilih Jenis Audit</option>
                                @foreach($jenisAudit as $ja)
                                    <option value="{{ $ja->id }}" data-kode="{{ $ja->kode }}" {{ $item->jenis_audit_id == $ja->id ? 'selected' : '' }}>{{ $ja->nama_jenis_audit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Nomor LHA/LHK dan Kode SPI -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor LHA/LHK <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_lha_lhk" id="nomor_lha_lhk" class="form-control" value="{{ $item->nomor_lha_lhk }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode SPI <span class="text-danger">*</span></label>
                            <input type="text" name="kode_spi" id="kode_spi" class="form-control" value="{{ $item->kode_spi }}" required readonly>
                            <small class="text-muted">Kode SPI otomatis terisi dari jenis audit yang dipilih</small>
                        </div>
                    </div>

                    <!-- Row 3: Area ISS -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Area ISS (Issues)</h6>
                                    <small>Data ISS yang sudah ada akan ditampilkan. Anda bisa menambah, mengubah, atau menghapus.</small>
                                </div>
                                <div class="card-body">
                                    <div id="iss-container">
                                        <!-- ISS items rendered by JavaScript -->
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
                                <i class="mdi mdi-content-save"></i> Update
                            </button>
                            <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">
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
        <input type="hidden" name="temuan_id[]" value="">
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
                <input type="text" name="nomor_iss[]" class="form-control nomor-iss-input" readonly>
                <input type="hidden" name="nomor_urut_iss[]" class="nomor-urut-iss-input">
                <button type="button" class="btn btn-outline-primary btn-sm mt-1 generate-iss-btn">
                    <i class="mdi mdi-refresh"></i> Generate
                </button>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-12">
                <button type="button" class="btn btn-info btn-sm expand-iss-detail-btn">
                    <i class="mdi mdi-chevron-down"></i> Detail ISS
                </button>
            </div>
        </div>

        <div class="collapse iss-detail-collapse mt-2">
            <div class="row g-3">
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
                <div class="col-md-12">
                    <label class="form-label">Penyebab <span class="text-danger">*</span></label>
                    <textarea name="penyebab[]" class="form-control" rows="3" required></textarea>
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

@php
    $existingTemuan = $item->temuan->map(fn($t) => [
        'id'             => $t->id,
        'hasil_temuan'   => $t->hasil_temuan,
        'kode_aoi_id'    => $t->kode_aoi_id,
        'kode_risk_id'   => $t->kode_risk_id,
        'nomor_iss'      => $t->nomor_iss,
        'nomor_urut_iss' => $t->nomor_urut_iss,
        'permasalahan'   => $t->permasalahan,
        'penyebab'       => $t->penyebab,
        'kriteria'       => $t->kriteria,
        'dampak_terjadi' => $t->dampak_terjadi,
        'dampak_potensi' => $t->dampak_potensi,
        'signifikan'     => $t->signifikan,
    ])->values();
@endphp

@section('script')
<script>
const existingTemuan = @json($existingTemuan);

window.addEventListener('load', function() {
    let issIndex = 0;

    // ─── Jenis Audit → auto-fill Kode SPI ─────────────────────────────────────
    $('#jenis_audit_id').on('change select2:select', function() {
        const kode = $(this).find('option:selected').data('kode') || '';
        $('#kode_spi').val(kode);
        $('.nomor-iss-input').val('');
    });

    // ─── Tambah ISS Item (baru atau existing) ──────────────────────────────────
    function addIssItem(data) {
        const template = document.getElementById('iss-item-template');
        const clone    = template.content.cloneNode(true);
        const itemEl   = clone.querySelector('.iss-item');
        const idx      = issIndex;

        // Set index sebelum append
        itemEl.setAttribute('data-iss-index', idx);

        // ── Isi data ke elemen fragment SEBELUM append ke DOM ──────────────────
        // Hidden temuan_id
        const temuanIdEl = itemEl.querySelector('input[name="temuan_id[]"]');
        if (temuanIdEl) temuanIdEl.value = data.id || '';

        // Textarea & input
        if (data.hasil_temuan)   itemEl.querySelector('textarea[name="hasil_temuan[]"]').value   = data.hasil_temuan;
        if (data.nomor_iss)      itemEl.querySelector('.nomor-iss-input').value                   = data.nomor_iss;
        if (data.nomor_urut_iss) itemEl.querySelector('.nomor-urut-iss-input').value              = data.nomor_urut_iss;
        if (data.permasalahan)   itemEl.querySelector('textarea[name="permasalahan[]"]').value    = data.permasalahan;
        if (data.kriteria)       itemEl.querySelector('textarea[name="kriteria[]"]').value        = data.kriteria;
        if (data.dampak_terjadi) itemEl.querySelector('textarea[name="dampak_terjadi[]"]').value = data.dampak_terjadi;
        if (data.dampak_potensi) itemEl.querySelector('textarea[name="dampak_potensi[]"]').value = data.dampak_potensi;
        if (data.penyebab)       itemEl.querySelector('textarea[name="penyebab[]"]').value        = data.penyebab;

        // Select values (set selected attribute directly)
        if (data.kode_aoi_id) {
            const opt = itemEl.querySelector(`select[name="kode_aoi_id[]"] option[value="${data.kode_aoi_id}"]`);
            if (opt) opt.selected = true;
        }
        if (data.kode_risk_id) {
            const opt = itemEl.querySelector(`select[name="kode_risk_id[]"] option[value="${data.kode_risk_id}"]`);
            if (opt) opt.selected = true;
        }
        if (data.signifikan) {
            const opt = itemEl.querySelector(`select[name="signifikan[]"] option[value="${data.signifikan}"]`);
            if (opt) opt.selected = true;
        }

        // Jika data existing, langsung tampilkan detail
        if (data.id) {
            const detail = itemEl.querySelector('.iss-detail-collapse');
            if (detail) detail.classList.add('show');
        }

        // ── Append ke DOM (fragment pindah ke DOM di sini) ─────────────────────
        document.getElementById('iss-container').appendChild(clone);

        // ── Inisialisasi Select2 pada elemen LIVE di DOM ───────────────────────
        const $item = $(`#iss-container [data-iss-index="${idx}"]`);
        $item.find('.select2-iss').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({ width: '100%' });
        });

        issIndex++;
    }

    // Tombol Tambah ISS (item baru kosong)
    $('#add-iss-btn').on('click', function() {
        addIssItem({});
    });

    // ─── Event Delegation — semua event pada .iss-item ────────────────────────

    // Toggle Detail ISS
    $(document).on('click', '#iss-container .expand-iss-detail-btn', function() {
        $(this).closest('.iss-item').find('.iss-detail-collapse').toggleClass('show');
    });

    // Hapus ISS
    $(document).on('click', '#iss-container .remove-iss-btn', function() {
        $(this).closest('.iss-item').remove();
    });

    // Generate ISS (manual)
    $(document).on('click', '#iss-container .generate-iss-btn', function() {
        generateIssNumber($(this).closest('.iss-item'), false);
    });

    // Auto-generate saat AOI atau Risiko diubah
    $(document).on('change', '#iss-container .kode-aoi-select, #iss-container .kode-risk-select', function() {
        const $item  = $(this).closest('.iss-item');
        const aoiVal = $item.find('.kode-aoi-select').val();
        const rskVal = $item.find('.kode-risk-select').val();

        $item.find('.nomor-iss-input').val('');

        if (aoiVal && rskVal) {
            generateIssNumber($item, true); // silent
        }
    });

    // ─── Fungsi Generate Nomor ISS ────────────────────────────────────────────
    function generateIssNumber($item, silent) {
        const nomorLhaLhk = $('#nomor_lha_lhk').val();
        const kodeSpi     = $('#kode_spi').val();
        const kodeAoiId   = $item.find('.kode-aoi-select').val();
        const kodeRiskId  = $item.find('.kode-risk-select').val();

        if (!nomorLhaLhk || !kodeSpi || !kodeAoiId || !kodeRiskId) {
            if (!silent) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Field Belum Lengkap',
                    text: 'Mohon lengkapi Nomor LHA/LHK, Kode SPI, Kode AOI, dan Kode Risiko terlebih dahulu.',
                    confirmButtonText: 'OK'
                });
            }
            return;
        }

        $.ajax({
            url: '{{ route("audit.pelaporan-hasil-audit.generate-nomor-iss") }}',
            type: 'POST',
            data: {
                _token       : '{{ csrf_token() }}',
                nomor_lha_lhk: nomorLhaLhk,
                kode_spi     : kodeSpi,
                kode_aoi_id  : kodeAoiId,
                kode_risk_id : kodeRiskId
            },
            success: function(res) {
                $item.find('.nomor-iss-input').val(res.nomor_iss);
                $item.find('.nomor-urut-iss-input').val(res.nomor_urut_iss);
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

    // ─── Load data temuan existing saat halaman dibuka ────────────────────────
    if (existingTemuan.length > 0) {
        existingTemuan.forEach(function(t) { addIssItem(t); });
    } else {
        addIssItem({}); // Minimal satu item kosong
    }
});
</script>
@endsection
