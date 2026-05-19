@extends('layouts.vertical', ['title' => 'Tambah Walkthrough Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Tambah Walkthrough Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('components.alert')
                
                @if($programKerjaAudit->isEmpty())
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Tidak ada Program Kerja Audit yang tersedia untuk Walkthrough. 
                        Semua PKA yang memiliki milestone Walkthrough sudah memiliki data walkthrough.
                    </div>
                    <a href="{{ route('audit.perencanaan.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i> Buat Surat Tugas Baru
                    </a>
                @else
                    <form action="{{ route('audit.walkthrough.store') }}" method="POST" id="walkthroughForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="program_kerja_audit_id" class="form-label">Program Kerja Audit</label>
                            <select name="program_kerja_audit_id" id="program_kerja_audit_id" class="form-select select2-search" required>
                                <option value="">Pilih Program Kerja Audit</option>
                                @foreach($programKerjaAudit as $pka)
                                    @php
                                        $isRejected = $pka->walkthroughAudit && $pka->walkthroughAudit->status_approval === 'rejected';
                                        $plannedDate = $pka->milestones ? $pka->milestones->first()->tanggal_mulai ?? '' : '';
                                        $perencanaan = $pka->perencanaanAudit;
                                        $suratTugasText = $perencanaan ? $perencanaan->nomor_surat_tugas : 'N/A';
                                    @endphp
                                    <option value="{{ $pka->id }}" 
                                            data-planned-date="{{ $plannedDate }}"
                                            data-surat-tugas="{{ $suratTugasText }}">
                                        {{ $pka->no_pka }}
                                        @if($perencanaan) · {{ $perencanaan->nomor_surat_tugas }}@endif
                                        @if($perencanaan && $perencanaan->jenis_audit) · {{ $perencanaan->jenis_audit }}@endif
                                        @if($perencanaan && $perencanaan->auditee) · {{ $perencanaan->auditee->divisi }}@endif
                                        @if($perencanaan && $perencanaan->tanggal_audit_mulai && $perencanaan->tanggal_audit_sampai)
                                            · [{{ \Carbon\Carbon::parse($perencanaan->tanggal_audit_mulai)->locale('id')->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($perencanaan->tanggal_audit_sampai)->locale('id')->translatedFormat('d M Y') }}]
                                        @endif
                                        @if($isRejected) (Reject - Ajukan Ulang)@endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya menampilkan PKA yang memiliki milestone Walkthrough dan belum memiliki data walkthrough yang approved/pending</small>
                        </div>

                        <div class="mb-3">
                            <label for="planned_walkthrough_date" class="form-label">Planned Walkthrough Date</label>
                            <input type="date" name="planned_walkthrough_date" id="planned_walkthrough_date" class="form-control" readonly>
                            <small class="text-muted">Tanggal diambil dari milestone Walkthrough</small>
                        </div>

                        <div class="mb-3">
                            <label for="actual_walkthrough_date" class="form-label">Actual Walkthrough Date</label>
                            <input type="date" name="actual_walkthrough_date" id="actual_walkthrough_date" class="form-control">
                            <small class="text-muted">Tanggal walkthrough yang sebenarnya dilaksanakan (opsional)</small>
                        </div>

                        <div class="mb-3">
                            <label for="auditee_id" class="form-label">Nama Auditee</label>
                            <select name="auditee_id" id="auditee_id" class="form-select select2-search" required>
                                <option value="">Pilih Auditee</option>
                                @foreach($auditees as $auditee)
                                    <option value="{{ $auditee->id }}" {{ old('auditee_id') == $auditee->id ? 'selected' : '' }}>
                                        {{ $auditee->divisi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="hasil_walkthrough" class="form-label">Hasil Walkthrough</label>
                            <textarea name="hasil_walkthrough" id="hasil_walkthrough" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="file_bpm" class="form-label">Upload File BPM</label>
                            <input type="file" name="file_bpm" id="file_bpm" class="form-control" accept=".pdf">
                            <small class="text-muted">Hanya file PDF yang diperbolehkan (maksimal 5MB) - Opsional</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                        <a href="{{ route('audit.walkthrough.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali
                        </a>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pkaSelect = document.getElementById('program_kerja_audit_id');
    const plannedDateInput = document.getElementById('planned_walkthrough_date');
    const actualDateInput = document.getElementById('actual_walkthrough_date');

    if (typeof $ !== 'undefined' && ($(pkaSelect).hasClass('select2-search') || $(pkaSelect).hasClass('select2-hidden-accessible'))) {
        $(pkaSelect).on('change', function() {
            const selectedOption = $(this).find(':selected');
            const plannedDate = selectedOption.data('planned-date') || selectedOption.attr('data-planned-date');
            
            if (plannedDate) {
                plannedDateInput.value = plannedDate;
            } else {
                plannedDateInput.value = '';
            }
        });
    } else {
        pkaSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const plannedDate = selectedOption.getAttribute('data-planned-date');
            
            if (plannedDate) {
                plannedDateInput.value = plannedDate;
            } else {
                plannedDateInput.value = '';
            }
        });
    }
});
</script>
@endsection 