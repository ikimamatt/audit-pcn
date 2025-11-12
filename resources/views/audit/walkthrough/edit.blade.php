@extends('layouts.vertical', ['title' => 'Edit Walkthrough Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Walkthrough Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('components.alert')
                
                <form action="{{ route('audit.walkthrough.update', $item->id) }}" method="POST" id="walkthroughForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="program_kerja_audit_id" class="form-label">Program Kerja Audit</label>
                        <input type="text" class="form-control" value="{{ $item->programKerjaAudit ? $item->programKerjaAudit->no_pka . ' - ' . ($item->programKerjaAudit->perencanaanAudit ? $item->programKerjaAudit->perencanaanAudit->nomor_surat_tugas : 'N/A') : 'N/A' }}" readonly>
                        <input type="hidden" name="program_kerja_audit_id" value="{{ $item->program_kerja_audit_id }}">
                        <small class="text-muted">Program Kerja Audit tidak dapat diubah setelah dibuat</small>
                    </div>

                    <div class="mb-3">
                        <label for="planned_walkthrough_date" class="form-label">Planned Walkthrough Date</label>
                        <input type="date" name="planned_walkthrough_date" id="planned_walkthrough_date" class="form-control" value="{{ $item->planned_walkthrough_date }}" readonly>
                        <small class="text-muted">Tanggal diambil dari milestone Walkthrough</small>
                    </div>

                    <div class="mb-3">
                        <label for="actual_walkthrough_date" class="form-label">Actual Walkthrough Date</label>
                        <input type="date" name="actual_walkthrough_date" id="actual_walkthrough_date" class="form-control" value="{{ $item->actual_walkthrough_date }}">
                        <small class="text-muted">Tanggal walkthrough yang sebenarnya dilaksanakan (opsional)</small>
                    </div>

                    <div class="mb-3">
                        <label for="auditee_id" class="form-label">Nama Auditee</label>
                        <select name="auditee_id" id="auditee_id" class="form-select" required>
                            <option value="">Pilih Auditee</option>
                            @foreach($auditees as $auditee)
                                <option value="{{ $auditee->id }}" {{ old('auditee_id', $item->auditee_nama == $auditee->divisi ? $auditee->id : '') == $auditee->id ? 'selected' : '' }}>
                                    {{ $auditee->divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="hasil_walkthrough" class="form-label">Hasil Walkthrough</label>
                        <textarea name="hasil_walkthrough" id="hasil_walkthrough" class="form-control" rows="4" required>{{ $item->hasil_walkthrough }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save me-1"></i> Update
                    </button>
                    <a href="{{ route('audit.walkthrough.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 