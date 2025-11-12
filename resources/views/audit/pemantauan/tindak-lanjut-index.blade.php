@extends('layouts.vertical', ['title' => 'Detail Tindak Lanjut'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Detail Tindak Lanjut</h4>
                    <a href="{{ route('audit.pemantauan.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Informasi Auditee dan Temuan -->
                @if($rekomendasi->temuan && $rekomendasi->temuan->pelaporanHasilAudit && $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit)
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Auditee</h6>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Nama Auditee:</dt>
                                    <dd class="col-sm-8">{{ $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi ?? 'N/A' }}</dd>
                                    
                                    @if($rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat)
                                    <dt class="col-sm-4">Direktorat:</dt>
                                    <dd class="col-sm-8">{{ $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat }}</dd>
                                    @endif
                                    
                                    @if($rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang)
                                    <dt class="col-sm-4">Divisi Cabang:</dt>
                                    <dd class="col-sm-8">{{ $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang }}</dd>
                                    @endif
                                    
                                    <dt class="col-sm-4">Nomor Tugas:</dt>
                                    <dd class="col-sm-8">{{ $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas ?? 'N/A' }}</dd>
                                    
                                    <dt class="col-sm-4">Jenis Audit:</dt>
                                    <dd class="col-sm-8">{{ $rekomendasi->temuan->pelaporanHasilAudit->perencanaanAudit->jenis_audit ?? 'N/A' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Temuan</h6>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Nomor ISS:</dt>
                                    <dd class="col-sm-8">{{ $rekomendasi->temuan->nomor_iss ?? 'N/A' }}</dd>
                                    
                                    <dt class="col-sm-4">Nomor LHA/LHK:</dt>
                                    <dd class="col-sm-8">{{ $rekomendasi->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? 'N/A' }}</dd>
                                    
                                    <dt class="col-sm-4">Permasalahan:</dt>
                                    <dd class="col-sm-8">{{ Str::limit($rekomendasi->temuan->permasalahan ?? 'N/A', 150) }}</dd>
                                    
                                    <dt class="col-sm-4">Penyebab:</dt>
                                    <dd class="col-sm-8">{{ Str::limit($rekomendasi->temuan->penyebab ?? 'N/A', 150) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Informasi Rekomendasi -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Detail Rekomendasi</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Rekomendasi</dt>
                            <dd class="col-sm-9">{{ $rekomendasi->rekomendasi }}</dd>
                            
                            <dt class="col-sm-3">Rencana Aksi</dt>
                            <dd class="col-sm-9">{{ $rekomendasi->rencana_aksi }}</dd>
                            
                            <dt class="col-sm-3">Eviden Rekomendasi</dt>
                            <dd class="col-sm-9">{{ $rekomendasi->eviden_rekomendasi }}</dd>
                            
                            <dt class="col-sm-3">Target Waktu</dt>
                            <dd class="col-sm-9">
                                <span class="badge {{ $rekomendasi->target_waktu < now() ? 'bg-danger' : 'bg-success' }}">
                                    {{ \Carbon\Carbon::parse($rekomendasi->target_waktu)->format('d/m/Y') }}
                                </span>
                            </dd>
                            
                            <dt class="col-sm-3">PIC Rekomendasi</dt>
                            <dd class="col-sm-9">{{ $rekomendasi->pic_rekomendasi }}</dd>
                            
                            <dt class="col-sm-3">Status Approval</dt>
                            <dd class="col-sm-9">
                                @if($rekomendasi->status_approval == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($rekomendasi->status_approval == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <!-- Informasi Tindak Lanjut -->
                @if($rekomendasi->tindakLanjut && $rekomendasi->tindakLanjut->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Riwayat Tindak Lanjut</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($rekomendasi->tindakLanjut->sortByDesc('created_at') as $index => $tindakLanjut)
                            <div class="timeline-item {{ $index == 0 ? 'latest' : '' }}">
                                <div class="timeline-marker {{ $index == 0 ? 'bg-success' : 'bg-secondary' }}">
                                    @if($index == 0)
                                        <i class="mdi mdi-star"></i>
                                    @else
                                        <i class="mdi mdi-circle"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="card {{ $index == 0 ? 'border-success' : 'border-light' }}">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0">
                                                Tindak Lanjut #{{ $rekomendasi->tindakLanjut->count() - $index }}
                                                @if($index == 0)
                                                    <span class="badge bg-success ms-2">Terbaru</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($tindakLanjut->created_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="card-body">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-3">Tanggal Penyelesaian</dt>
                                                <dd class="col-sm-9">
                                                    @if($tindakLanjut->real_waktu)
                                                        <span class="badge bg-success">{{ \Carbon\Carbon::parse($tindakLanjut->real_waktu)->format('d/m/Y') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </dd>
                                                
                                                <dt class="col-sm-3">Komentar</dt>
                                                <dd class="col-sm-9">
                                                    @if($tindakLanjut->komentar)
                                                        @php
                                                            $komentarArray = explode("\n\n---\n\n", $tindakLanjut->komentar);
                                                        @endphp
                                                        @if(count($komentarArray) > 1)
                                                            <div class="komentar-list">
                                                                @foreach($komentarArray as $index => $komentar)
                                                                    <div class="komentar-item mb-2 p-2 border rounded {{ $index == 0 ? 'border-primary' : 'border-light' }}">
                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                            <strong class="text-primary">Komentar #{{ $index + 1 }}</strong>
                                                                            @if($index == 0)
                                                                                <span class="badge bg-primary">Utama</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="mt-1">{{ $komentar }}</div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            {{ $tindakLanjut->komentar }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </dd>
                                                
                                                <dt class="col-sm-3">File Eviden</dt>
                                                <dd class="col-sm-9">
                                                    @if($tindakLanjut->file_eviden)
                                                        <a href="{{ asset('storage/' . $tindakLanjut->file_eviden) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="mdi mdi-download me-1"></i>Download Eviden
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Tidak ada file</span>
                                                    @endif
                                                </dd>
                                                
                                                <dt class="col-sm-3">Status Tindak Lanjut</dt>
                                                <dd class="col-sm-9">
                                                    @if($tindakLanjut->status_tindak_lanjut == 'open')
                                                        <span class="badge bg-warning">Open</span>
                                                    @elseif($tindakLanjut->status_tindak_lanjut == 'on_progress')
                                                        <span class="badge bg-info">On Progress</span>
                                                    @elseif($tindakLanjut->status_tindak_lanjut == 'closed')
                                                        <span class="badge bg-success">Closed</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $tindakLanjut->status_tindak_lanjut }}</span>
                                                    @endif
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Belum ada tindak lanjut untuk rekomendasi ini.
                </div>
                @endif
                
                <div class="mt-4 text-center">
                    <a href="{{ route('audit.pemantauan.index') }}" class="btn btn-secondary me-2">
                        <i class="mdi mdi-arrow-left me-2"></i>Kembali ke Pemantauan
                    </a>
                    <a href="{{ route('audit.penutup-lha-rekomendasi.tindak-lanjut.form', $rekomendasi->id) }}" class="btn btn-success">
                        <i class="mdi mdi-plus-circle me-2"></i>Tambah Tindak Lanjut
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    margin-left: 20px;
}

.timeline-item.latest .timeline-marker {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

.komentar-list {
    max-height: 300px;
    overflow-y: auto;
}

.komentar-item {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.komentar-item:hover {
    background-color: #e9ecef;
    transform: translateX(5px);
}

.komentar-item.border-primary {
    background-color: #e3f2fd;
    border-color: #2196f3 !important;
}

.komentar-item .badge {
    font-size: 0.75rem;
}
</style>
@endsection 