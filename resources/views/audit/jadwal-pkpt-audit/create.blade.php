@extends('layouts.vertical', ['title' => 'Tambah Jadwal PKPT Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Tambah Jadwal PKPT Audit</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('audit.pkpt.store') }}">
                    @csrf
                    @if(!empty($returnUrl))
                        <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Nama Auditee</label>
                        <select name="auditee_id" class="form-select" required>
                            <option value="">Pilih Auditee</option>
                            @foreach($auditees as $auditee)
                                <option value="{{ $auditee->id }}">{{ $auditee->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Audit</label>
                        <select name="jenis_audit" class="form-select" required>
                            <option value="">Pilih Jenis Audit</option>
                            <option value="Operasional">Operasional (O)</option>
                            <option value="Khusus">Khusus (A/T)</option>
                            <option value="Konsultan">Konsultan (C)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Auditor</label>
                        <input type="number" name="jumlah_auditor" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Audit (Mulai)</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Audit (Selesai)</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ $returnUrl ?? route('audit.pkpt.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 