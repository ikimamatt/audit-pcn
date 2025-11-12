@extends('layouts.vertical', ['title' => 'Edit Temuan Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Edit Temuan Audit</h4>
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
                    <div class="mb-3">
                        <label class="form-label">Pelaporan Hasil Audit (Judul LHA/LHK)</label>
                        @php
                            $selected = $pelaporanList->where('id', $item->pelaporan_hasil_audit_id)->first();
                        @endphp
                        <input type="text" class="form-control" value="{{ $selected ? $selected->nomor_lha_lhk : '' }}" readonly>
                        <input type="hidden" name="pelaporan_hasil_audit_id" value="{{ $item->pelaporan_hasil_audit_id }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hasil Temuan (AOI)</label>
                        <textarea name="hasil_temuan" class="form-control" rows="3" required>{{ old('hasil_temuan', $item->hasil_temuan) }}</textarea>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Kode AOI</label>
                            <select name="kode_aoi_id" class="form-select" required>
                                <option value="">Pilih Kode AOI</option>
                                @foreach($kodeAoi as $aoi)
                                    <option value="{{ $aoi->id }}" {{ old('kode_aoi_id', $item->kode_aoi_id)==$aoi->id?'selected':'' }}>{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Risiko</label>
                            <select name="kode_risk_id" class="form-select" required>
                                <option value="">Pilih Kode Risiko</option>
                                @foreach($kodeRisk as $risk)
                                    <option value="{{ $risk->id }}" {{ old('kode_risk_id', $item->kode_risk_id)==$risk->id?'selected':'' }}>{{ $risk->kode_risiko }} - {{ $risk->deskripsi_risiko }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor ISS</label>
                        <input type="text" name="nomor_iss" class="form-control" placeholder="ISS.xxx/PO PCN/MM/NN/PP/yyyy" required value="{{ old('nomor_iss', $item->nomor_iss) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" placeholder="2024" required value="{{ old('tahun', $item->tahun) }}">
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 