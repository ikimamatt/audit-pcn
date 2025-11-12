@extends('layouts.vertical', ['title' => 'Edit Kode AOI'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Kode AOI</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('master.kode-aoi.update', $masterKodeAoi->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="indikator_pengawasan" class="form-label">Indikator Pengawasan</label>
                                <input type="text" class="form-control @error('indikator_pengawasan') is-invalid @enderror" id="indikator_pengawasan" name="indikator_pengawasan" value="{{ old('indikator_pengawasan', $masterKodeAoi->indikator_pengawasan) }}" required>
                                @error('indikator_pengawasan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="kode_area_of_improvement" class="form-label">Kode Area of Improvement</label>
                                <input type="text" class="form-control @error('kode_area_of_improvement') is-invalid @enderror" id="kode_area_of_improvement" name="kode_area_of_improvement" value="{{ old('kode_area_of_improvement', $masterKodeAoi->kode_area_of_improvement) }}" required>
                                @error('kode_area_of_improvement')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi_area_of_improvement" class="form-label">Deskripsi Area of Improvement</label>
                                <textarea class="form-control @error('deskripsi_area_of_improvement') is-invalid @enderror" id="deskripsi_area_of_improvement" name="deskripsi_area_of_improvement" rows="3" required>{{ old('deskripsi_area_of_improvement', $masterKodeAoi->deskripsi_area_of_improvement) }}</textarea>
                                @error('deskripsi_area_of_improvement')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('master.kode-aoi.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
