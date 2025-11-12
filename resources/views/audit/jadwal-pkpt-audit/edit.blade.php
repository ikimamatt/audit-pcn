@extends('layouts.vertical', ['title' => 'Edit Jadwal PKPT Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Edit Jadwal PKPT Audit</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('audit.pkpt.update', $item->id) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Auditee</label>
                        <select name="auditee_id" class="form-select" required>
                            <option value="">Pilih Auditee</option>
                            @foreach($auditees as $auditee)
                                <option value="{{ $auditee->id }}" {{ $item->auditee_id == $auditee->id ? 'selected' : '' }}>{{ $auditee->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Audit</label>
                        <select name="jenis_audit" class="form-select" required>
                            <option value="">Pilih Jenis Audit</option>
                            <option value="Operasional" {{ $item->jenis_audit == 'Operasional' ? 'selected' : '' }}>Operasional (O)</option>
                            <option value="Khusus" {{ $item->jenis_audit == 'Khusus' ? 'selected' : '' }}>Khusus (A/T)</option>
                            <option value="Konsultan" {{ $item->jenis_audit == 'Konsultan' ? 'selected' : '' }}>Konsultan (C)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Auditor</label>
                        <input type="number" name="jumlah_auditor" class="form-control" min="1" value="{{ $item->jumlah_auditor }}" required>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Audit (Mulai)</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ $item->tanggal_mulai }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Audit (Selesai)</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ $item->tanggal_selesai }}" required>
                        </div>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('audit.pkpt.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 