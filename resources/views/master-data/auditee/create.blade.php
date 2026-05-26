@extends('layouts.vertical', ['title' => 'Tambah Bidang'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.auditee.index') }}">Master Bidang</a></li>
                    <li class="breadcrumb-item active">Tambah Bidang</li>
                </ol>
            </div>
            <h4 class="page-title">Tambah Bidang</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('master.auditee.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kd_bidang" class="form-label">Kode Bidang <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('kd_bidang') is-invalid @enderror"
                                   id="kd_bidang"
                                   name="kd_bidang"
                                   value="{{ old('kd_bidang') }}"
                                   placeholder="Contoh: 01"
                                   maxlength="10"
                                   required>
                            @error('kd_bidang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="nama_bidang" class="form-label">Nama Bidang <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nama_bidang') is-invalid @enderror"
                                   id="nama_bidang"
                                   name="nama_bidang"
                                   value="{{ old('nama_bidang') }}"
                                   placeholder="Masukkan nama bidang"
                                   required>
                            @error('nama_bidang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="is_available_for_up"
                                   name="is_available_for_up"
                                   value="1"
                                   {{ old('is_available_for_up', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_available_for_up">
                                Tersedia untuk User UP
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('master.auditee.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection