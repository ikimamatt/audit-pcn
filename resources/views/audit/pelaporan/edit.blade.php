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
                <select name="kode_aoi_id[]" class="form-select kode-aoi-select select2-search" required>
                    <option value="">Pilih Kode AOI</option>
                    @foreach($kodeAoi as $aoi)
                        <option value="{{ $aoi->id }}">{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode Risiko <span class="text-danger">*</span></label>
                <select name="kode_risk_id[]" class="form-select kode-risk-select select2-search" required>
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

$(document).ready(function() {
    let issIndex = 0;

    // Auto-set Kode SPI dari jenis audit
    $('#jenis_audit_id').change(function() {
        const kode = $(this).find('option:selected').data('kode');
        $('#kode_spi').val(kode || '');
    });
    // Trigger saat load jika sudah ada nilai
    if ($('#jenis_audit_id').val()) {
        $('#jenis_audit_id').trigger('change');
    }

    // Tambah ISS item baru
    $('#add-iss-btn').click(function() {
        addIssItem({});
    });

    function addIssItem(data) {
        const template = document.getElementById('iss-item-template');
        const clone = $(template.content.cloneNode(true));

        clone.find('.iss-item').attr('data-iss-index', issIndex);

        // Isi hidden temuan_id (kosong jika baru)
        clone.find('input[name="temuan_id[]"]').val(data.id || '');

        // Isi nilai existing
        if (data.hasil_temuan)   clone.find('textarea[name="hasil_temuan[]"]').val(data.hasil_temuan);
        if (data.kode_aoi_id)    clone.find('select[name="kode_aoi_id[]"]').val(data.kode_aoi_id);
        if (data.kode_risk_id)   clone.find('select[name="kode_risk_id[]"]').val(data.kode_risk_id);
        if (data.nomor_iss)      clone.find('.nomor-iss-input').val(data.nomor_iss);
        if (data.nomor_urut_iss) clone.find('.nomor-urut-iss-input').val(data.nomor_urut_iss);
        if (data.permasalahan)   clone.find('textarea[name="permasalahan[]"]').val(data.permasalahan);
        if (data.kriteria)       clone.find('textarea[name="kriteria[]"]').val(data.kriteria);
        if (data.dampak_terjadi) clone.find('textarea[name="dampak_terjadi[]"]').val(data.dampak_terjadi);
        if (data.dampak_potensi) clone.find('textarea[name="dampak_potensi[]"]').val(data.dampak_potensi);
        if (data.signifikan)     clone.find('select[name="signifikan[]"]').val(data.signifikan);
        if (data.penyebab)       clone.find('textarea[name="penyebab[]"]').val(data.penyebab);

        // Jika ada data existing, langsung buka detail
        if (data.id) {
            clone.find('.iss-detail-collapse').addClass('show');
        }

        $('#iss-container').append(clone);
        
        // Re-initialize select2 for newly added elements
        $('#iss-container').find('[data-iss-index="' + issIndex + '"] .select2-search').select2({
            width: '100%'
        });
        
        bindIssEvents(issIndex);
        issIndex++;
    }

    function bindIssEvents(index) {
        const container = $(`[data-iss-index="${index}"]`);

        // Toggle detail
        container.find('.expand-iss-detail-btn').click(function() {
            container.find('.iss-detail-collapse').toggleClass('show');
        });

        // Hapus ISS
        container.find('.remove-iss-btn').click(function() {
            container.remove();
        });

        // Generate nomor ISS
        container.find('.generate-iss-btn').click(function() {
            generateIssNumber(index);
        });

        // Auto-generate saat AOI/Risk berubah
        container.find('.kode-aoi-select, .kode-risk-select').change(function() {
            container.find('.nomor-iss-input').val('');
            const aoi  = container.find('.kode-aoi-select').val();
            const risk = container.find('.kode-risk-select').val();
            if (aoi && risk && $('#nomor_lha_lhk').val() && $('#kode_spi').val()) {
                generateIssNumber(index);
            }
        });
    }

    function generateIssNumber(index) {
        const container   = $(`[data-iss-index="${index}"]`);
        const nomorLhaLhk = $('#nomor_lha_lhk').val();
        const kodeSpi     = $('#kode_spi').val();
        const kodeAoiId   = container.find('.kode-aoi-select').val();
        const kodeRiskId  = container.find('.kode-risk-select').val();

        if (!nomorLhaLhk || !kodeSpi || !kodeAoiId || !kodeRiskId) {
            alert('Lengkapi nomor LHA/LHK, kode SPI, kode AOI, dan kode risiko terlebih dahulu');
            return;
        }

        $.ajax({
            url: '{{ route("audit.pelaporan-hasil-audit.generate-nomor-iss") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nomor_lha_lhk: nomorLhaLhk,
                kode_spi: kodeSpi,
                kode_aoi_id: kodeAoiId,
                kode_risk_id: kodeRiskId
            },
            success: function(res) {
                container.find('.nomor-iss-input').val(res.nomor_iss);
                container.find('.nomor-urut-iss-input').val(res.nomor_urut_iss);
            },
            error: function() {
                alert('Gagal generate nomor ISS');
            }
        });
    }

    // Load existing temuan on page load
    existingTemuan.forEach(function(t) {
        addIssItem(t);
    });

    // Jika tidak ada temuan existing, tambah satu kosong
    if (existingTemuan.length === 0) {
        addIssItem({});
    }
});
</script>
@endsection