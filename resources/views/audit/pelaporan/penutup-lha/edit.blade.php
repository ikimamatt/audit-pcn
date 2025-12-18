@extends('layouts.vertical', ['title' => 'Edit Penutup LHA/LHK'])

@section('content')
<div class="container-fluid">
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
                                <label for="pic_approval_1_spi" class="form-label fw-bold">APPROVAL 1 SPI</label>
                                <select name="pic_approval_1_spi" id="pic_approval_1_spi" class="form-select" required>
                                    <option value="">Pilih PIC Approval 1 SPI</option>
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
                                <label for="pic_approval_2_spi" class="form-label fw-bold">APPROVAL 2 SPI</label>
                                <select name="pic_approval_2_spi" id="pic_approval_2_spi" class="form-select" required>
                                    <option value="">Pilih PIC Approval 2 SPI</option>
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
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
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