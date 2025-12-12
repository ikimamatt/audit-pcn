@extends('layouts.vertical', ['title' => 'Tambah User'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.user.index') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Tambah User</li>
                </ol>
            </div>
            <h4 class="page-title">Tambah User</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('master.user.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" 
                                       name="nama" 
                                       value="{{ old('nama') }}" 
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('nama')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       placeholder="Masukkan username"
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nip') is-invalid @enderror" 
                                       id="nip" 
                                       name="nip" 
                                       value="{{ old('nip') }}" 
                                       placeholder="Masukkan NIP"
                                       required>
                                @error('nip')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Masukkan email">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_telpon" class="form-label">No. Telpon</label>
                                <input type="text" 
                                       class="form-control @error('no_telpon') is-invalid @enderror" 
                                       id="no_telpon" 
                                       name="no_telpon" 
                                       value="{{ old('no_telpon') }}" 
                                       placeholder="Masukkan nomor telpon">
                                @error('no_telpon')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" 
                                       class="form-control @error('jabatan') is-invalid @enderror" 
                                       id="jabatan" 
                                       name="jabatan" 
                                       value="{{ old('jabatan') }}" 
                                       placeholder="Masukkan jabatan">
                                @error('jabatan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Masukkan password"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="master_auditee_id" class="form-label">Divisi <span class="text-danger">*</span></label>
                                <select class="form-select @error('master_auditee_id') is-invalid @enderror" 
                                        id="master_auditee_id" 
                                        name="master_auditee_id" 
                                        required>
                                    <option value="">Pilih Divisi</option>
                                    @foreach($auditees as $auditee)
                                        <option value="{{ $auditee->id }}" {{ old('master_auditee_id') == $auditee->id ? 'selected' : '' }}>
                                            {{ $auditee->divisi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('master_auditee_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="master_akses_user_id" class="form-label">Akses <span class="text-danger">*</span></label>
                                <select class="form-select @error('master_akses_user_id') is-invalid @enderror" 
                                        id="master_akses_user_id" 
                                        name="master_akses_user_id" 
                                        required>
                                    <option value="">Pilih Akses</option>
                                    @foreach($aksesUsers as $akses)
                                        <option value="{{ $akses->id }}" {{ old('master_akses_user_id') == $akses->id ? 'selected' : '' }}>
                                            {{ $akses->nama_akses }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('master_akses_user_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('master.user.index') }}" class="btn btn-secondary">
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