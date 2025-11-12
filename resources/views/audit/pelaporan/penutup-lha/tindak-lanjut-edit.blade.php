@extends('layouts.vertical', ['title' => 'Edit Tindak Lanjut Rekomendasi'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Edit Tindak Lanjut Rekomendasi</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('audit.penutup-lha-tindak-lanjut.update', $tindakLanjut->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Tanggal Penyelesaian (Real Waktu)</label>
                        <input type="date" name="real_waktu" class="form-control" value="{{ old('real_waktu', $tindakLanjut->real_waktu) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Komentar</label>
                        <textarea name="komentar" class="form-control" rows="3">{{ old('komentar', $tindakLanjut->komentar) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Eviden</label>
                        <input type="file" name="file_eviden" class="form-control">
                        @if($tindakLanjut->file_eviden)
                            <div class="mt-1"><a href="{{ asset('storage/' . $tindakLanjut->file_eviden) }}" target="_blank">Lihat Eviden Lama</a></div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Tindak Lanjut</label>
                        <select name="status_tindak_lanjut" class="form-control">
                            <option value="open" {{ old('status_tindak_lanjut', $tindakLanjut->status_tindak_lanjut) == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="on_progress" {{ old('status_tindak_lanjut', $tindakLanjut->status_tindak_lanjut) == 'on_progress' ? 'selected' : '' }}>On Progress</option>
                            <option value="closed" {{ old('status_tindak_lanjut', $tindakLanjut->status_tindak_lanjut) == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 