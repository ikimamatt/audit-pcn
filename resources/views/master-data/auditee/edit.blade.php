@extends('layouts.vertical', ['title' => 'Edit Auditee'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.auditee.index') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Edit Auditee</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Auditee</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('master.auditee.update', $masterAuditee->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="divisi" class="form-label">Divisi/Cabang <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('divisi') is-invalid @enderror" 
                               id="divisi" 
                               name="divisi" 
                               value="{{ old('divisi', $masterAuditee->divisi) }}" 
                               placeholder="Masukkan nama divisi/cabang"
                               required>
                        @error('divisi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('master.auditee.index') }}" class="btn btn-secondary">
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