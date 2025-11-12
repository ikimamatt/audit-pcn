@extends('layouts.vertical', ['title' => 'Unggah Dokumen Audit'])

@section('content')
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `{!! implode('<br>', $errors->all()) !!}`,
            });
        });
    </script>
@endif
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        });
    </script>
@endif
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Unggah Dokumen Audit</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('audit.unggah-dokumen.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Exit Meeting</label>
                            <input type="date" name="tanggal_exit_meeting" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Auditee</label>
                            <select name="auditee_id" class="form-select" required>
                                <option value="">Pilih Auditee</option>
                                @foreach($auditees as $auditee)
                                    <option value="{{ $auditee->id }}">{{ $auditee->direktorat }} - {{ $auditee->divisi_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Upload Undangan Exit Meeting</label>
                            <input type="file" name="file_undangan" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Absensi Exit Meeting</label>
                            <input type="file" name="file_absensi" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Nomor LHA/LHK</label>
                            <select name="lha_lhk_id" class="form-select" required>
                                <option value="">Pilih Nomor LHA/LHK</option>
                                @foreach($lhaList as $lha)
                                    <option value="{{ $lha->id }}">{{ $lha->nomor_lha_lhk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Dokumen LHA/LHK</label>
                            <input type="file" name="file_lha_lhk" class="form-control" accept=".pdf,.doc,.docx" required>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 row">
                        <label class="form-label">Pilih Tujuan Nota Dinas</label>
                        <div class="col-md-12 d-flex gap-4 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nota_tujuan" id="dirut" value="dirut" required>
                                <label class="form-check-label" for="dirut">DIRUT</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nota_tujuan" id="dekom" value="dekom">
                                <label class="form-check-label" for="dekom">DEKOM</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nota_tujuan" id="auditee" value="auditee">
                                <label class="form-check-label" for="auditee">Auditee</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Upload Nota Dinas</label>
                            <input type="file" name="file_nota_dinas" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('audit.unggah-dokumen.create') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 