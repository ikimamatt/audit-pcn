@extends('layouts.vertical', ['title' => 'Edit Program Kerja Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">EDIT PROGRAM KERJA AUDIT</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('audit.pka.update', $item->id) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Surat Tugas</label>
                        <select name="perencanaan_audit_id" class="form-select" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}" {{ $item->perencanaan_audit_id == $st->id ? 'selected' : '' }}>{{ $st->nomor_surat_tugas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal PKA</label>
                        <input type="date" name="tanggal_pka" class="form-control" value="{{ $item->tanggal_pka }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No PKA</label>
                        <input type="text" name="no_pka" class="form-control" value="{{ $item->no_pka }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Informasi Umum</label>
                        <textarea name="informasi_umum" class="form-control">{{ $item->informasi_umum }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">KPI Tidak Tercapai</label>
                        <textarea name="kpi_tidak_tercapai" class="form-control">{{ $item->kpi_tidak_tercapai }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Awal Dokumen Audit</label>
                        <textarea name="data_awal_dokumen" class="form-control">{{ $item->data_awal_dokumen }}</textarea>
                    </div>
                    <!-- Risk Based Audit -->
                    <div class="mb-3">
                        <label class="form-label">Risk Based Audit</label>
                        <div id="risk-list">
                            @foreach($item->risks as $i => $risk)
                            <div class='card mb-2 p-2 risk-item'>
                                <div class='row'>
                                    <div class='col-md-3'><input type='text' name='risk[{{ $i }}][deskripsi_resiko]' class='form-control' placeholder='Deskripsi Risiko' value='{{ $risk->deskripsi_resiko }}' required></div>
                                    <div class='col-md-2'><input type='text' name='risk[{{ $i }}][penyebab_resiko]' class='form-control' placeholder='Penyebab Risiko' value='{{ $risk->penyebab_resiko }}' required></div>
                                    <div class='col-md-2'><input type='text' name='risk[{{ $i }}][dampak_resiko]' class='form-control' placeholder='Dampak Risiko' value='{{ $risk->dampak_resiko }}' required></div>
                                    <div class='col-md-3'><input type='text' name='risk[{{ $i }}][pengendalian_eksisting]' class='form-control' placeholder='Pengendalian Eksisting' value='{{ $risk->pengendalian_eksisting }}' required></div>
                                    <div class='col-md-2'><button type='button' class='btn btn-danger btn-remove-risk'>Hapus</button></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-info" id="btn-add-risk">Tambah Risk</button>
                    </div>
                    <!-- Milestone -->
                    <div class="mb-3">
                        <label class="form-label">Milestone</label>
                        @php
                        $milestones = ['Entry Meeting', 'Walkthrough', 'TOD', 'TOE', 'Draf LHA', 'Exit Meeting'];
                        @endphp
                        <div class="row">
                            @foreach($milestones as $m)
                            @php $ms = $item->milestones->firstWhere('nama_milestone', $m); @endphp
                            <div class="col-md-6 mb-2">
                                <div class="card p-2">
                                    <strong>{{ $m }}</strong>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Mulai</label>
                                            <input type="date" name="milestone[{{ $m }}][mulai]" class="form-control" value="{{ $ms->tanggal_mulai ?? '' }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Selesai</label>
                                            <input type="date" name="milestone[{{ $m }}][selesai]" class="form-control" value="{{ $ms->tanggal_selesai ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Upload Dokumen -->
                    <div class="mb-3">
                        <label class="form-label">Upload Dokumen PKA</label>
                        <input type="file" name="dokumen[]" class="form-control" multiple>
                        <div class="mt-2">
                            @foreach($item->dokumen as $dok)
                                <div>{{ $dok->nama_dokumen }} - <a href="{{ asset('storage/'.$dok->file_path) }}" target="_blank">Lihat</a></div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('audit.pka.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
let riskIndex = {{ count($item->risks) }};
function riskInput(idx, data = {}) {
    return `<div class='card mb-2 p-2 risk-item'>
        <div class='row'>
            <div class='col-md-3'><input type='text' name='risk[${idx}][deskripsi_resiko]' class='form-control' placeholder='Deskripsi Risiko' value='${data.deskripsi_resiko||''}' required></div>
            <div class='col-md-2'><input type='text' name='risk[${idx}][penyebab_resiko]' class='form-control' placeholder='Penyebab Risiko' value='${data.penyebab_resiko||''}' required></div>
            <div class='col-md-2'><input type='text' name='risk[${idx}][dampak_resiko]' class='form-control' placeholder='Dampak Risiko' value='${data.dampak_resiko||''}' required></div>
            <div class='col-md-3'><input type='text' name='risk[${idx}][pengendalian_eksisting]' class='form-control' placeholder='Pengendalian Eksisting' value='${data.pengendalian_eksisting||''}' required></div>
            <div class='col-md-2'><button type='button' class='btn btn-danger btn-remove-risk'>Hapus</button></div>
        </div>
    </div>`;
}
$(document).on('click', '#btn-add-risk', function() {
    $('#risk-list').append(riskInput(riskIndex++));
});
$(document).on('click', '.btn-remove-risk', function() {
    $(this).closest('.risk-item').remove();
});
</script>
@endsection 