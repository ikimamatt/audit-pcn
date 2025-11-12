<form method="POST" action="{{ isset($edit) ? route('audit.unggah-dokumen.update', $edit->id) : route('audit.unggah-dokumen.store') }}" enctype="multipart/form-data" id="unggahDokumenForm">
    @csrf
    @if(isset($edit))
        @method('PUT')
    @endif
    <div class="mb-3 row">
        <div class="col-md-6">
            <label class="form-label">Tanggal Exit Meeting</label>
            <input type="date" name="tanggal_exit_meeting" class="form-control" value="{{ old('tanggal_exit_meeting', isset($edit) && $edit->tanggal_exit_meeting ? $edit->tanggal_exit_meeting instanceof \Illuminate\Support\Carbon ? $edit->tanggal_exit_meeting->format('Y-m-d') : date('Y-m-d', strtotime($edit->tanggal_exit_meeting)) : '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Nama Auditee</label>
            <select name="auditee_id" class="form-select" required>
                <option value="">Pilih Auditee</option>
                @foreach($auditees as $auditee)
                    <option value="{{ $auditee->id }}" {{ old('auditee_id', $edit->auditee_id ?? '') == $auditee->id ? 'selected' : '' }}>{{ $auditee->divisi }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-md-6">
            <label class="form-label">Upload Undangan Exit Meeting</label>
            <input type="file" name="file_undangan" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ isset($edit) ? '' : 'required' }}>
            @if(isset($edit) && $edit->file_undangan)
                <small class="text-info">File saat ini: <a href="{{ asset('storage/' . $edit->file_undangan) }}" target="_blank">Lihat</a></small>
            @endif
        </div>
        <div class="col-md-6">
            <label class="form-label">Upload Absensi Exit Meeting</label>
            <input type="file" name="file_absensi" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ isset($edit) ? '' : 'required' }}>
            @if(isset($edit) && $edit->file_absensi)
                <small class="text-info">File saat ini: <a href="{{ asset('storage/' . $edit->file_absensi) }}" target="_blank">Lihat</a></small>
            @endif
        </div>
    </div>
    <hr>
    <div class="mb-3 row">
        <div class="col-md-6">
            <label class="form-label">Nomor LHA/LHK</label>
            <select name="lha_lhk_id" class="form-select" required>
                <option value="">Pilih Nomor LHA/LHK</option>
                @foreach($lhaList as $lha)
                    <option value="{{ $lha->id }}" {{ old('lha_lhk_id', $edit->lhaLhk->pelaporan_hasil_audit_id ?? '') == $lha->id ? 'selected' : '' }}>{{ $lha->nomor_lha_lhk }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Upload Dokumen LHA/LHK</label>
            <input type="file" name="file_lha_lhk" class="form-control" accept=".pdf,.doc,.docx" {{ isset($edit) ? '' : 'required' }}>
            @if(isset($edit) && $edit->lhaLhk && $edit->lhaLhk->file_lha_lhk)
                <small class="text-info">File saat ini: <a href="{{ asset('storage/' . $edit->lhaLhk->file_lha_lhk) }}" target="_blank">Lihat</a></small>
            @endif
        </div>
    </div>
    <hr>
    <div class="mb-3 row">
        <label class="form-label">Pilih Tujuan Nota Dinas</label>
        <div class="col-md-12 d-flex gap-4 align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tujuan_nota_dinas" id="dirut" value="dirut" {{ old('tujuan_nota_dinas', $edit->tujuan_nota_dinas ?? ($edit->notaDinas->tujuan_nota_dinas ?? '')) == 'dirut' ? 'checked' : '' }} required>
                <label class="form-check-label" for="dirut">DIRUT</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tujuan_nota_dinas" id="dekom" value="dekom" {{ old('tujuan_nota_dinas', $edit->tujuan_nota_dinas ?? ($edit->notaDinas->tujuan_nota_dinas ?? '')) == 'dekom' ? 'checked' : '' }}>
                <label class="form-check-label" for="dekom">DEKOM</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tujuan_nota_dinas" id="auditee" value="auditee" {{ old('tujuan_nota_dinas', $edit->tujuan_nota_dinas ?? ($edit->notaDinas->tujuan_nota_dinas ?? '')) == 'auditee' ? 'checked' : '' }}>
                <label class="form-check-label" for="auditee">Auditee</label>
            </div>
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-md-6">
            <label class="form-label">Upload Nota Dinas</label>
            <input type="file" name="file_nota_dinas" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ isset($edit) ? '' : 'required' }}>
            @if(isset($edit) && ($edit->file_nota_dinas || (isset($edit->notaDinas) && $edit->notaDinas->file_nota_dinas)))
                <small class="text-info">File saat ini: <a href="{{ asset('storage/' . ($edit->file_nota_dinas ?? ($edit->notaDinas->file_nota_dinas ?? ''))) }}" target="_blank">Lihat</a></small>
            @endif
        </div>
    </div>
    <div class="mb-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">{{ isset($edit) ? 'Update' : 'Simpan' }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    </div>
</form> 