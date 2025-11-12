@extends('layouts.vertical', ['title' => 'Edit TOE Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit TOE Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.toe.update', $item->id) }}" method="POST">
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
                        <select name="judul_bpm" id="judul_bpm" class="form-control" required>
                            <option value="">Pilih Judul BPM</option>
                            @foreach($bpmList as $bpm)
                                <option value="{{ $bpm->judul_bpm }}" {{ $item->judul_bpm == $bpm->judul_bpm ? 'selected' : '' }}>{{ $bpm->judul_bpm }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pengendalian_eksisting" class="form-label">Pengendalian Eksisting</label>
                        <textarea name="pengendalian_eksisting" id="pengendalian_eksisting" class="form-control" rows="2" required>{{ $item->pengendalian_eksisting }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('audit.toe.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 