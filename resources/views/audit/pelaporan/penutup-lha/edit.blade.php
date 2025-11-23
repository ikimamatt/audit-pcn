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
                            <label for="pic_rekomendasi_id" class="form-label">PIC Rekomendasi</label>
                            <div id="pic-list">
                                @php 
                                    // Gunakan selected_pic_user_ids jika ada, jika tidak gunakan array kosong untuk dropdown baru
                                    $picList = old('pic_rekomendasi_id', $item->selected_pic_user_ids ?? []);
                                    // Jika tidak ada selected, buat satu dropdown kosong
                                    if (empty($picList)) {
                                        $picList = [''];
                                    }
                                @endphp
                                @foreach($picList as $i => $picId)
                                <div class="input-group mb-2 pic-item">
                                    <select name="pic_rekomendasi_id[]" class="form-select pic-select" required>
                                        <option value="">Pilih PIC Rekomendasi</option>
                                        @foreach($picUsers as $picUser)
                                            <option value="{{ $picUser->id }}" 
                                                data-nama="{{ $picUser->nama }}" 
                                                data-divisi="{{ $picUser->auditee->divisi ?? '-' }}"
                                                {{ (is_numeric($picId) && $picId == $picUser->id) || $picId == $picUser->id ? 'selected' : '' }}>
                                                {{ $picUser->nama }} - {{ $picUser->auditee->divisi ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-danger btn-remove-pic" @if($i==0 && count($picList) == 1) style="display:none" @endif>-</button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-info" id="btn-add-pic">Tambah PIC</button>
                            <div class="form-text">
                                Pilih PIC yang bertanggung jawab atas rekomendasi ini (hanya user dengan role PIC Auditee).
                            </div>
                            @error('pic_rekomendasi_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            @error('pic_rekomendasi_id.*')<div class="text-danger small">{{ $message }}</div>@enderror
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
    // PIC dinamis
    document.getElementById('btn-add-pic').onclick = function() {
        var list = document.getElementById('pic-list');
        var item = document.createElement('div');
        item.className = 'input-group mb-2 pic-item';
        
        // Buat select dropdown dengan opsi PIC
        var selectHtml = '<select name="pic_rekomendasi_id[]" class="form-select pic-select" required><option value="">Pilih PIC Rekomendasi</option>';
        @foreach($picUsers as $picUser)
            selectHtml += '<option value="{{ $picUser->id }}" data-nama="{{ $picUser->nama }}" data-divisi="{{ $picUser->auditee->divisi ?? "-" }}">{{ $picUser->nama }} - {{ $picUser->auditee->divisi ?? "-" }}</option>';
        @endforeach
        selectHtml += '</select>';
        
        item.innerHTML = selectHtml + ' <button type="button" class="btn btn-danger btn-remove-pic">-</button>';
        list.appendChild(item);
        
        // Event handler untuk remove button
        item.querySelector('.btn-remove-pic').onclick = function() { 
            item.remove(); 
            // Update visibility tombol remove untuk item pertama
            updateRemovePicButtons();
        };
        
        // Update visibility tombol remove
        updateRemovePicButtons();
    };
    
    // Update visibility tombol remove
    function updateRemovePicButtons() {
        var items = document.querySelectorAll('.pic-item');
        items.forEach(function(item, index) {
            var removeBtn = item.querySelector('.btn-remove-pic');
            if (removeBtn) {
                removeBtn.style.display = items.length > 1 ? 'block' : 'none';
            }
        });
    }
    
    // Event handler untuk remove button yang sudah ada
    document.querySelectorAll('.btn-remove-pic').forEach(function(btn) {
        btn.onclick = function() { 
            btn.closest('.pic-item').remove(); 
            updateRemovePicButtons();
        };
    });
    
    // Inisialisasi visibility tombol remove saat halaman dimuat
    updateRemovePicButtons();
</script>
@endsection 