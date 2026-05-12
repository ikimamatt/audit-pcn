@extends('layouts.vertical', ['title' => 'Edit Unit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.unit.index') }}">Master Unit</a></li>
                    <li class="breadcrumb-item active">Edit Unit</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Unit</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('master.unit.update', $masterUnit->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="kode_unit" class="form-label">Kode Unit <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('kode_unit') is-invalid @enderror"
                               id="kode_unit"
                               name="kode_unit"
                               value="{{ old('kode_unit', $masterUnit->kode_unit) }}"
                               placeholder="Contoh: U001"
                               maxlength="20"
                               required>
                        @error('kode_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nama_unit" class="form-label">Nama Unit <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('nama_unit') is-invalid @enderror"
                               id="nama_unit"
                               name="nama_unit"
                               value="{{ old('nama_unit', $masterUnit->nama_unit) }}"
                               placeholder="Masukkan nama unit"
                               maxlength="150"
                               required>
                        @error('nama_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('master.unit.index') }}" class="btn btn-secondary">
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
