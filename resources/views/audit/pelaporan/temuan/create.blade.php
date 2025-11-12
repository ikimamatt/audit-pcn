@extends('layouts.vertical', ['title' => 'Tambah Temuan Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Tambah Temuan Audit</h4>
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
                    <div class="mb-3">
                        <label class="form-label">Pelaporan Hasil Audit (Judul LHA/LHK)</label>
                        @if(isset($selectedPelaporan) && $selectedPelaporan)
                            @php
                                $selected = $pelaporanList->where('id', $selectedPelaporan)->first();
                            @endphp
                            <input type="text" class="form-control" value="{{ $selected ? $selected->nomor_lha_lhk : '' }}" readonly>
                            <input type="hidden" name="pelaporan_hasil_audit_id" value="{{ $selectedPelaporan }}">
                        @else
                            <select name="pelaporan_hasil_audit_id" class="form-select" required>
                                <option value="">Pilih Judul LHA/LHK</option>
                                @foreach($pelaporanList as $p)
                                    <option value="{{ $p->id }}" {{ (old('pelaporan_hasil_audit_id', $selectedPelaporan ?? null)==$p->id)?'selected':'' }}>{{ $p->nomor_lha_lhk }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hasil Temuan (AOI)</label>
                        <textarea name="hasil_temuan" class="form-control" rows="3" required>{{ old('hasil_temuan') }}</textarea>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Kode AOI</label>
                            <select name="kode_aoi_id" class="form-select" required>
                                <option value="">Pilih Kode AOI</option>
                                @if(isset($kodeAoi) && count($kodeAoi))
                                    @foreach($kodeAoi as $aoi)
                                        <option value="{{ $aoi->id }}">{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Risiko</label>
                            <select name="kode_risk_id" class="form-select" required>
                                <option value="">Pilih Kode Risiko</option>
                                @if(isset($kodeRisk) && count($kodeRisk))
                                    @foreach($kodeRisk as $risk)
                                        <option value="{{ $risk->id }}">{{ $risk->kode_risiko }} - {{ $risk->deskripsi_risiko }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor ISS</label>
                        <input type="text" name="nomor_iss" class="form-control" placeholder="ISS.xxx/PO PCN/MM/NN/PP/yyyy" required value="{{ old('nomor_iss') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" placeholder="2024" required value="{{ old('tahun') }}">
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 