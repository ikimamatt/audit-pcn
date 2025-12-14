@extends('layouts.vertical', ['title' => 'Edit Pelaporan Hasil Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Edit Judul LHA/LHK & Temuan</h4>
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
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Surat Tugas Audit</label>
                            <select name="perencanaan_audit_id" class="form-select" required>
                                <option value="">Pilih Surat Tugas</option>
                                @foreach($suratTugas as $s)
                                    <option value="{{ $s->id }}" {{ $item->perencanaan_audit_id == $s->id ? 'selected' : '' }}>{{ $s->nomor_surat_tugas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nomor LHA/LHK</label>
                            <input type="text" name="nomor_lha_lhk" class="form-control" value="{{ $item->nomor_lha_lhk }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Jenis</label>
                            <select name="jenis_lha_lhk" class="form-select" required>
                                <option value="LHA" {{ $item->jenis_lha_lhk=='LHA'?'selected':'' }}>LHA</option>
                                <option value="LHK" {{ $item->jenis_lha_lhk=='LHK'?'selected':'' }}>LHK</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Jenis Audit</label>
                            <select name="jenis_audit_id" id="jenis_audit_id" class="form-select" required>
                                <option value="">Pilih Jenis Audit</option>
                                @foreach($jenisAudit as $ja)
                                    <option value="{{ $ja->id }}" data-kode="{{ $ja->kode }}" {{ $item->jenis_audit_id == $ja->id ? 'selected' : '' }}>{{ $ja->nama_jenis_audit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-md-3">
                            <label class="form-label">Kode SPI</label>
                            <input type="text" name="kode_spi" id="kode_spi" class="form-control" value="{{ $item->kode_spi }}" required readonly>
                            <small class="text-muted">Kode SPI otomatis terisi dari jenis audit yang dipilih</small>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Nomor ISS</label>
                            <input type="text" name="nomor_iss" class="form-control" value="{{ $item->nomor_iss }}" required>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Hasil Temuan (AOI)</label>
                            <textarea name="hasil_temuan" class="form-control" rows="2" required>{{ $item->hasil_temuan }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kode AOI</label>
                            <select name="kode_aoi_id" class="form-select" required>
                                <option value="">Pilih Kode AOI</option>
                                @foreach($kodeAoi as $aoi)
                                    <option value="{{ $aoi->id }}" {{ $item->kode_aoi_id == $aoi->id ? 'selected' : '' }}>{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kode Risiko</label>
                            <select name="kode_risk_id" class="form-select" required>
                                <option value="">Pilih Kode Risiko</option>
                                @foreach($kodeRisk as $risk)
                                    <option value="{{ $risk->id }}" {{ $item->kode_risk_id == $risk->id ? 'selected' : '' }}>{{ $risk->kode_risiko }} - {{ $risk->deskripsi_risiko }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set Kode SPI berdasarkan jenis audit yang dipilih
    $('#jenis_audit_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const kodeSpi = selectedOption.data('kode');
        
        if (kodeSpi) {
            $('#kode_spi').val(kodeSpi);
        } else {
            $('#kode_spi').val('');
        }
    });
    
    // Trigger on page load if jenis audit already selected
    if ($('#jenis_audit_id').val()) {
        $('#jenis_audit_id').trigger('change');
    }
});
</script>
@endsection 