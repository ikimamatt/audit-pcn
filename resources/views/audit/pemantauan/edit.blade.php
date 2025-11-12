@extends('layouts.vertical', ['title' => 'Edit Rekomendasi (Pemantauan)'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Rekomendasi (Pemantauan Hasil Audit)</h4>
                        <a href="{{ route('audit.pemantauan.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Informasi Auditee dan Temuan -->
                    @if($item->temuan && $item->temuan->pelaporanHasilAudit && $item->temuan->pelaporanHasilAudit->perencanaanAudit)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informasi Auditee</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Nama Auditee:</dt>
                                        <dd class="col-sm-8">{{ $item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi ?? 'N/A' }}</dd>
                                        
                                        @if($item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat)
                                        <dt class="col-sm-4">Direktorat:</dt>
                                        <dd class="col-sm-8">{{ $item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat }}</dd>
                                        @endif
                                        
                                        @if($item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang)
                                        <dt class="col-sm-4">Divisi Cabang:</dt>
                                        <dd class="col-sm-8">{{ $item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang }}</dd>
                                        @endif
                                        
                                        <dt class="col-sm-4">Nomor Tugas:</dt>
                                        <dd class="col-sm-8">{{ $item->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas ?? 'N/A' }}</dd>
                                        
                                        <dt class="col-sm-4">Jenis Audit:</dt>
                                        <dd class="col-sm-8">{{ $item->temuan->pelaporanHasilAudit->perencanaanAudit->jenis_audit ?? 'N/A' }}</dd>
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
                                        <dd class="col-sm-8">{{ $item->temuan->nomor_iss ?? 'N/A' }}</dd>
                                        
                                        <dt class="col-sm-4">Nomor LHA/LHK:</dt>
                                        <dd class="col-sm-8">{{ $item->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? 'N/A' }}</dd>
                                        
                                        <dt class="col-sm-4">Permasalahan:</dt>
                                        <dd class="col-sm-8">{{ Str::limit($item->temuan->permasalahan ?? 'N/A', 100) }}</dd>
                                        
                                        <dt class="col-sm-4">Penyebab:</dt>
                                        <dd class="col-sm-8">{{ Str::limit($item->temuan->penyebab ?? 'N/A', 100) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <form action="{{ route('audit.pemantauan.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rekomendasi" class="form-label">Rekomendasi <span class="text-danger">*</span></label>
                                    <textarea name="rekomendasi" id="rekomendasi" class="form-control" rows="4" maxlength="5000" required>{{ old('rekomendasi', $item->rekomendasi) }}</textarea>
                                    <div class="form-text">Maksimal 5000 karakter</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="rencana_aksi" class="form-label">Rencana Aksi <span class="text-danger">*</span></label>
                                    <textarea name="rencana_aksi" id="rencana_aksi" class="form-control" rows="4" maxlength="5000" required>{{ old('rencana_aksi', $item->rencana_aksi) }}</textarea>
                                    <div class="form-text">Maksimal 5000 karakter</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eviden_rekomendasi" class="form-label">Eviden Rekomendasi <span class="text-danger">*</span></label>
                                    <textarea name="eviden_rekomendasi" id="eviden_rekomendasi" class="form-control" rows="4" maxlength="5000" required>{{ old('eviden_rekomendasi', $item->eviden_rekomendasi) }}</textarea>
                                    <div class="form-text">Maksimal 5000 karakter</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="pic_rekomendasi" class="form-label">PIC Rekomendasi <span class="text-danger">*</span></label>
                                    <input type="text" name="pic_rekomendasi" id="pic_rekomendasi" class="form-control" maxlength="500" value="{{ old('pic_rekomendasi', $item->pic_rekomendasi) }}" required>
                                    <div class="form-text">Maksimal 500 karakter</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="target_waktu" class="form-label">Target Waktu Penyelesaian <span class="text-danger">*</span></label>
                                    <input type="date" name="target_waktu" id="target_waktu" class="form-control" value="{{ old('target_waktu', $item->target_waktu) }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('audit.pemantauan.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-2"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 