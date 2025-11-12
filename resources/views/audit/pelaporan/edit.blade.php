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
                            <label class="form-label">PO/Konsul</label>
                            <select name="po_audit_konsul" class="form-select" required>
                                <option value="PO AUDIT" {{ $item->po_audit_konsul=='PO AUDIT'?'selected':'' }}>PO AUDIT</option>
                                <option value="KONSUL" {{ $item->po_audit_konsul=='KONSUL'?'selected':'' }}>KONSUL</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-md-3">
                            <label class="form-label">Kode SPI</label>
                            <select name="kode_spi" class="form-select" required>
                                <option value="SPI.01.02" {{ $item->kode_spi=='SPI.01.02'?'selected':'' }}>SPI.01.02</option>
                                <option value="SPI.01.03" {{ $item->kode_spi=='SPI.01.03'?'selected':'' }}>SPI.01.03</option>
                                <option value="SPI.01.04" {{ $item->kode_spi=='SPI.01.04'?'selected':'' }}>SPI.01.04</option>
                            </select>
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