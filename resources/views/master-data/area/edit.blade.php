@extends('layouts.vertical', ['title' => 'Edit Area'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.area.index') }}">Master Area</a></li>
                    <li class="breadcrumb-item active">Edit Area</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Area</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('master.area.update', $masterArea->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="kd_region" class="form-label">Region</label>
                        <select class="form-select @error('kd_region') is-invalid @enderror"
                                id="kd_region"
                                name="kd_region">
                            <option value="">-- Pilih Region --</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->kd_region }}" {{ old('kd_region', $masterArea->kd_region) == $region->kd_region ? 'selected' : '' }}>
                                    [{{ $region->kd_region }}] {{ $region->nama_region }}
                                </option>
                            @endforeach
                        </select>
                        @error('kd_region')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kd_area" class="form-label">Kode Area <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('kd_area') is-invalid @enderror"
                               id="kd_area"
                               name="kd_area"
                               value="{{ old('kd_area', $masterArea->kd_area) }}"
                               placeholder="Contoh: 02"
                               maxlength="50"
                               required>
                        @error('kd_area')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nama_area" class="form-label">Nama Area <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('nama_area') is-invalid @enderror"
                               id="nama_area"
                               name="nama_area"
                               value="{{ old('nama_area', $masterArea->nama_area) }}"
                               placeholder="Masukkan nama area"
                               maxlength="255"
                               required>
                        @error('nama_area')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('master.area.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
