@extends('layouts.vertical', ['title' => 'Edit Exit Meeting'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </div>
            <h4 class="page-title">Edit Exit Meeting</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.exit-meeting.update', $realisasiAudit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="perencanaan_audit_id" class="form-label">Perencanaan Audit <span class="text-danger">*</span></label>
                                <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-select @error('perencanaan_audit_id') is-invalid @enderror" required>
                                    <option value="">Pilih Perencanaan Audit</option>
                                    @foreach($perencanaanAudits as $perencanaanAudit)
                                        <option value="{{ $perencanaanAudit->id }}" 
                                                {{ old('perencanaan_audit_id', $realisasiAudit->perencanaan_audit_id) == $perencanaanAudit->id ? 'selected' : '' }}>
                                            {{ $perencanaanAudit->nomor_surat_tugas }} - 
                                            @if($perencanaanAudit->auditee)
                                                {{ $perencanaanAudit->auditee->divisi }}
                                            @else
                                                -
                                            @endif
                                            ({{ $perencanaanAudit->jenis_audit }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('perencanaan_audit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <small class="text-muted">(Otomatis berdasarkan tanggal)</small></label>
                                @php
                                    $statusText = 'Belum Dimulai';
                                    if($realisasiAudit->tanggal_mulai && $realisasiAudit->tanggal_selesai) {
                                        $statusText = 'Selesai';
                                    } elseif($realisasiAudit->tanggal_mulai && !$realisasiAudit->tanggal_selesai) {
                                        $statusText = 'Sedang Berlangsung';
                                    }
                                @endphp
                                <input type="text" class="form-control" id="status_display" value="{{ $statusText }}" readonly>
                                <input type="hidden" name="status" id="status" value="{{ old('status', $realisasiAudit->status) }}">
                                <small class="text-muted">
                                    <ul class="mb-0 mt-2">
                                        <li>Belum Dimulai: Jika belum ada tanggal mulai</li>
                                        <li>Sedang Berlangsung: Jika ada tanggal mulai tapi belum ada tanggal selesai</li>
                                        <li>Selesai: Jika sudah ada tanggal mulai dan selesai</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                       id="tanggal_mulai" name="tanggal_mulai" 
                                       value="{{ old('tanggal_mulai', $realisasiAudit->tanggal_mulai) }}">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                       id="tanggal_selesai" name="tanggal_selesai" 
                                       value="{{ old('tanggal_selesai', $realisasiAudit->tanggal_selesai) }}">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Upload Dokumen Exit Meeting</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file_undangan" class="form-label">Upload Undangan Exit Meeting</label>
                                <input type="file" class="form-control @error('file_undangan') is-invalid @enderror" 
                                       id="file_undangan" name="file_undangan" accept=".pdf">
                                <small class="text-muted">Format: PDF. Maksimal 2MB</small>
                                @if($realisasiAudit->file_undangan)
                                    <small class="text-info d-block">File saat ini: <a href="{{ asset('storage/' . $realisasiAudit->file_undangan) }}" target="_blank">Lihat</a></small>
                                @endif
                                @error('file_undangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file_absensi" class="form-label">Upload Absensi Exit Meeting</label>
                                <input type="file" class="form-control @error('file_absensi') is-invalid @enderror" 
                                       id="file_absensi" name="file_absensi" accept=".pdf">
                                <small class="text-muted">Format: PDF. Maksimal 2MB</small>
                                @if($realisasiAudit->file_absensi)
                                    <small class="text-info d-block">File saat ini: <a href="{{ asset('storage/' . $realisasiAudit->file_absensi) }}" target="_blank">Lihat</a></small>
                                @endif
                                @error('file_absensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Update
                        </button>
                        <a href="{{ route('audit.exit-meeting.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function updateStatus() {
        const tanggalMulai = document.getElementById('tanggal_mulai').value;
        const tanggalSelesai = document.getElementById('tanggal_selesai').value;
        const statusInput = document.getElementById('status');
        const statusDisplay = document.getElementById('status_display');
        
        let status = 'belum';
        let statusText = 'Belum Dimulai';
        
        if (tanggalMulai && tanggalSelesai) {
            status = 'selesai';
            statusText = 'Selesai';
        } else if (tanggalMulai && !tanggalSelesai) {
            status = 'on progress';
            statusText = 'Sedang Berlangsung';
        }
        
        statusInput.value = status;
        statusDisplay.value = statusText;
        
        // Update display color
        statusDisplay.className = 'form-control';
        if (status === 'selesai') {
            statusDisplay.style.backgroundColor = '#d4edda';
            statusDisplay.style.color = '#155724';
        } else if (status === 'on progress') {
            statusDisplay.style.backgroundColor = '#fff3cd';
            statusDisplay.style.color = '#856404';
        } else {
            statusDisplay.style.backgroundColor = '#f8f9fa';
            statusDisplay.style.color = '#6c757d';
        }
    }

    // Set minimum date for tanggal_selesai based on tanggal_mulai and update status
    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        document.getElementById('tanggal_selesai').min = this.value;
        updateStatus();
    });
    
    // Update status when tanggal_selesai changes
    document.getElementById('tanggal_selesai').addEventListener('change', function() {
        updateStatus();
    });
    
    // Initialize status on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateStatus();
    });
</script>
@endsection 