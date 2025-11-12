@extends('layouts.vertical', ['title' => 'Edit Entry Meeting'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Entry Meeting</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.entry-meeting.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label for="program_kerja_audit_id" class="form-label">Program Kerja Audit</label>
                        <input type="text" class="form-control" value="{{ $item->programKerjaAudit ? $item->programKerjaAudit->no_pka . ' - ' . ($item->programKerjaAudit->perencanaanAudit ? $item->programKerjaAudit->perencanaanAudit->nomor_surat_tugas : 'N/A') : 'N/A' }}" readonly>
                        <input type="hidden" name="program_kerja_audit_id" value="{{ $item->program_kerja_audit_id }}">
                        <small class="text-muted">Program Kerja Audit tidak dapat diubah setelah dibuat</small>
                    </div>
                    <div class="mb-3">
                        <label for="planned_meeting_date" class="form-label">Planned Meeting Date</label>
                        <input type="date" name="planned_meeting_date" id="planned_meeting_date" class="form-control" value="{{ $item->tanggal }}" required readonly>
                        <small class="text-muted">Tanggal meeting sesuai milestone Entry Meeting</small>
                    </div>
                    <div class="mb-3">
                        <label for="actual_meeting_date" class="form-label">Actual Meeting Date</label>
                        <input type="date" name="actual_meeting_date" id="actual_meeting_date" class="form-control" value="{{ $item->actual_meeting_date }}">
                        <small class="text-muted">Tanggal meeting yang sebenarnya dilaksanakan (opsional)</small>
                    </div>
                    <div class="mb-3">
                        <label for="auditee_id" class="form-label">Nama Auditee</label>
                        <select name="auditee_id" id="auditee_id" class="form-control" required>
                            <option value="">Pilih Auditee</option>
                            @foreach($auditees as $auditee)
                                <option value="{{ $auditee->id }}" {{ $item->auditee_id == $auditee->id ? 'selected' : '' }}>{{ $auditee->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file_undangan" class="form-label">Upload Undangan Entry Meeting</label>
                        <input type="file" name="file_undangan" id="file_undangan" class="form-control">
                        @if($item->file_undangan)
                            <div class="mt-1"><a href="{{ asset('storage/' . $item->file_undangan) }}" target="_blank">Download File Lama</a></div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="file_absensi" class="form-label">Upload Absensi Entry Meeting</label>
                        <input type="file" name="file_absensi" id="file_absensi" class="form-control">
                        @if($item->file_absensi)
                            <div class="mt-1"><a href="{{ asset('storage/' . $item->file_absensi) }}" target="_blank">Download File Lama</a></div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('audit.entry-meeting.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 