@extends('layouts.vertical', ['title' => 'Edit Penutup LHA/LHK'])

@section('css')
    <style>
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            transition: none !important;
        }
        .card-header {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }
        .card-header h4 {
            color: #1a3a5c !important;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
        }
        .btn-primary {
            background-color: #1e3a8a !important;
            border-color: #1e3a8a !important;
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 18px;
            font-size: 0.85rem;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.15);
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(15, 23, 42, 0.2);
        }
        .btn-secondary {
            background-color: #ffffff !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 18px;
            font-size: 0.85rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
            transform: translateY(-1px);
        }
        .bg-light {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.875rem;
        }
        .form-text {
            font-size: 0.78rem;
            color: #64748b;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Rekomendasi Penutup LHA/LHK</h4>
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
                    <form action="{{ route('audit.penutup-lha-rekomendasi.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="return_url" value="{{ $returnUrl ?? '' }}">
                        <input type="hidden" name="pelaporan_isi_lha_id" value="{{ $item->pelaporan_isi_lha_id }}">
                        <div class="mb-3">
                            <label class="form-label">Nomor ISS (LHA/LHK)</label>
                            <input type="text" class="form-control" value="{{ $item->temuan->nomor_iss ?? $item->pelaporan_isi_lha_id }}" readonly>
                            <div class="form-text">Field ini tidak dapat diubah setelah dibuat</div>
                        </div>
                        
                        <div class="mb-3" id="iss-details">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Detail ISS</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nomor LHA/LHK:</strong>
                                            <p class="mb-2">{{ $item->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? '-' }}</p>
                                            <strong>Hasil Temuan:</strong>
                                            <p class="mb-2">{{ $item->temuan->hasil_temuan ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Permasalahan:</strong>
                                            <p class="mb-2">{{ $item->temuan->permasalahan ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="rekomendasi" class="form-label">Rekomendasi</label>
                            <textarea name="rekomendasi" id="rekomendasi" class="form-control" rows="3" maxlength="5000" required>{{ old('rekomendasi', $item->rekomendasi) }}</textarea>
                            <div class="form-text">Tulis rekomendasi dalam format paragraf point-point, maksimal 5000 karakter.</div>
                            @error('rekomendasi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="rencana_aksi" class="form-label">Rencana Aksi</label>
                            <textarea name="rencana_aksi" id="rencana_aksi" class="form-control" rows="3" maxlength="5000" required>{{ old('rencana_aksi', $item->rencana_aksi) }}</textarea>
                            <div class="form-text">Tulis rencana aksi dalam format paragraf point-point, maksimal 5000 karakter.</div>
                            @error('rencana_aksi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="eviden_rekomendasi" class="form-label">Eviden Rekomendasi</label>
                            <textarea name="eviden_rekomendasi" id="eviden_rekomendasi" class="form-control" rows="3" maxlength="5000" required>{{ old('eviden_rekomendasi', $item->eviden_rekomendasi) }}</textarea>
                            <div class="form-text">Tulis eviden rekomendasi dalam format paragraf point-point, maksimal 5000 karakter.</div>
                            @error('eviden_rekomendasi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">PIC Rekomendasi</label>
                            
                            <div class="mb-3">
                                <label for="pic_business_contact" class="form-label fw-bold">BUSINESS CONTACT</label>
                                <select name="pic_business_contact" id="pic_business_contact" class="form-select" required>
                                    <option value="">Pilih PIC Business Contact</option>
                                    @foreach($picUsers as $picUser)
                                        <option value="{{ $picUser->id }}" 
                                            {{ old('pic_business_contact', $item->pic_business_contact_id) == $picUser->id ? 'selected' : '' }}>
                                            {{ $picUser->nama }} - {{ $picUser->auditee->divisi ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pic_business_contact')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="pic_approval_1_spi" class="form-label fw-bold">BUSINESS REVIEWER 1</label>
                                <select name="pic_approval_1_spi" id="pic_approval_1_spi" class="form-select" required>
                                    <option value="">Pilih PIC Business Reviewer 1</option>
                                    @foreach($picUsers as $picUser)
                                        <option value="{{ $picUser->id }}" 
                                            {{ old('pic_approval_1_spi', $item->pic_approval_1_spi_id) == $picUser->id ? 'selected' : '' }}>
                                            {{ $picUser->nama }} - {{ $picUser->auditee->divisi ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pic_approval_1_spi')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="pic_approval_2_spi" class="form-label fw-bold">BUSINESS REVIEWER 2</label>
                                <select name="pic_approval_2_spi" id="pic_approval_2_spi" class="form-select" required>
                                    <option value="">Pilih PIC Business Reviewer 2</option>
                                    @foreach($picUsers as $picUser)
                                        <option value="{{ $picUser->id }}" 
                                            {{ old('pic_approval_2_spi', $item->pic_approval_2_spi_id) == $picUser->id ? 'selected' : '' }}>
                                            {{ $picUser->nama }} - {{ $picUser->auditee->divisi ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pic_approval_2_spi')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-text">
                                Pilih PIC untuk masing-masing role dari data master user.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="target_waktu" class="form-label">Target Waktu Penyelesaian</label>
                            <input type="date" name="target_waktu" id="target_waktu" class="form-control" value="{{ old('target_waktu', $item->target_waktu) }}" required>
                            <div class="form-text">Pilih target waktu penyelesaian rekomendasi.</div>
                            @error('target_waktu')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ $returnUrl ?? url()->previous() }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Script untuk edit form jika diperlukan
</script>
@endsection 