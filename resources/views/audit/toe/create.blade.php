@extends('layouts.vertical', ['title' => 'Tambah TOE Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Tambah TOE Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.toe.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="perencanaan_audit_id" class="form-label">Surat Tugas Audit</label>
                        <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-control" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}">{{ $st->nomor_surat_tugas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="judul_bpm" class="form-label">Judul BPM</label>
                        <select name="judul_bpm" id="judul_bpm" class="form-control" required>
                            <option value="">Pilih Judul BPM</option>
                            @foreach($bpmList as $bpm)
                                <option value="{{ $bpm->judul_bpm }}">{{ $bpm->judul_bpm }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pengendalian_eksisting" class="form-label">Pengendalian Eksisting</label>
                        <textarea name="pengendalian_eksisting" id="pengendalian_eksisting" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Evaluasi Pengendalian</label>
                        <div id="evaluasi-container">
                            <div class="input-group mb-2 evaluasi-item">
                                <textarea name="hasil_evaluasi[]" class="form-control" rows="2" required></textarea>
                                <button type="button" class="btn btn-danger btn-remove-evaluasi">-</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-evaluasi">Tambah Evaluasi</button>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('audit.toe.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('evaluasi-container');
    document.getElementById('btn-add-evaluasi').onclick = function() {
        const div = document.createElement('div');
        div.className = 'input-group mb-2 evaluasi-item';
        div.innerHTML = `<textarea name="hasil_evaluasi[]" class="form-control" rows="2" required></textarea><button type="button" class="btn btn-danger btn-remove-evaluasi">-</button>`;
        container.appendChild(div);
    };
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-evaluasi')) {
            if (container.querySelectorAll('.evaluasi-item').length > 1) {
                e.target.parentElement.remove();
            }
        }
    });
});
</script>
@endsection 