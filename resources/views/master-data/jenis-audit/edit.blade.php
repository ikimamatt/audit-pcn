@extends('layouts.vertical', ['title' => 'Edit Jenis Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.jenis-audit.index') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Edit Jenis Audit</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Jenis Audit</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('master.jenis-audit.update', $masterJenisAudit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nama_jenis_audit" class="form-label">Nama Jenis Audit <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama_jenis_audit') is-invalid @enderror" 
                               id="nama_jenis_audit" 
                               name="nama_jenis_audit" 
                               value="{{ old('nama_jenis_audit', $masterJenisAudit->nama_jenis_audit) }}" 
                               placeholder="Masukkan nama jenis audit"
                               required>
                        @error('nama_jenis_audit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode</label>
                        <input type="text" 
                               class="form-control @error('kode') is-invalid @enderror" 
                               id="kode" 
                               name="kode" 
                               value="{{ old('kode', $masterJenisAudit->kode) }}" 
                               placeholder="Masukkan kode (contoh: SPI.01.02)">
                        @error('kode')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('master.jenis-audit.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

