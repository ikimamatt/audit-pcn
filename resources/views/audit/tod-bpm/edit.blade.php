@extends('layouts.vertical', ['title' => 'Edit BPM Audit (TOD)'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit BPM Audit (TOD)</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.tod-bpm.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label for="perencanaan_audit_id" class="form-label">Surat Tugas Audit</label>
                        <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-control" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}" {{ $item->perencanaan_audit_id == $st->id ? 'selected' : '' }}>{{ $st->nomor_surat_tugas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="judul_bpm" class="form-label">Judul BPM</label>
                        <textarea name="judul_bpm" id="judul_bpm" class="form-control" rows="2" required>{{ $item->judul_bpm }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nama_bpo" class="form-label">Nama BPO</label>
                        <textarea name="nama_bpo" id="nama_bpo" class="form-control" rows="2" required>{{ $item->nama_bpo }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file_bpm" class="form-label">Upload File BPM</label>
                        @if($item->file_bpm)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_bpm) }}" target="_blank">Download File Lama</a>
                            </div>
                        @endif
                        <input type="file" name="file_bpm" id="file_bpm" class="form-control">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('audit.tod-bpm.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 