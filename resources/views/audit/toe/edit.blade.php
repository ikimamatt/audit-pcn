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
                <form action="{{ route('audit.toe.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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
                            @php
                                $currentJudulBpm = old('judul_bpm', $item->judul_bpm ?? '');
                                $judulBpmFound = false;
                                $currentJudulBpmTrimmed = trim($currentJudulBpm);
                            @endphp
                            @foreach($bpmList as $bpm)
                                @php
                                    $bpmJudulTrimmed = trim($bpm->judul_bpm ?? '');
                                    // Perbandingan case-insensitive dan trim
                                    $isSelected = strcasecmp($currentJudulBpmTrimmed, $bpmJudulTrimmed) === 0 || $currentJudulBpmTrimmed === $bpmJudulTrimmed;
                                    if ($isSelected) {
                                        $judulBpmFound = true;
                                    }
                                @endphp
                                <option value="{{ $bpm->judul_bpm }}" {{ $isSelected ? 'selected' : '' }}>{{ $bpm->judul_bpm }}</option>
                            @endforeach
                            @if($currentJudulBpmTrimmed && !$judulBpmFound)
                                <option value="{{ $currentJudulBpm }}" selected>{{ $currentJudulBpm }} (Tidak ada di list)</option>
                            @endif
                        </select>
                        @if($currentJudulBpm && !$judulBpmFound)
                            <small class="text-warning">Judul BPM yang dipilih tidak ditemukan di list saat ini. Pastikan untuk memilih ulang dari list.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="pengendalian_eksisting" class="form-label">Pengendalian Eksisting</label>
                        <textarea name="pengendalian_eksisting" id="pengendalian_eksisting" class="form-control" rows="2" required>{{ $item->pengendalian_eksisting }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pemilihan_sampel_audit" class="form-label">Pemilihan Sampel Audit</label>
                        <textarea name="pemilihan_sampel_audit" id="pemilihan_sampel_audit" class="form-control" rows="3">{{ $item->pemilihan_sampel_audit }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="resiko" class="form-label">Resiko</label>
                        <textarea name="resiko" id="resiko" class="form-control" rows="3">{{ $item->resiko }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="kontrol" class="form-label">Kontrol</label>
                        <textarea name="kontrol" id="kontrol" class="form-control" rows="3">{{ $item->kontrol }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file_kka_toe" class="form-label">File KKA ToE</label>
                        @if($item->file_kka_toe)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_kka_toe) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="mdi mdi-download me-1"></i> Download File Saat Ini
                                </a>
                            </div>
                        @endif
                        <input type="file" name="file_kka_toe" id="file_kka_toe" class="form-control" accept=".pdf">
                        <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB) - Kosongkan jika tidak ingin mengganti file</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('audit.toe.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 