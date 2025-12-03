@extends('layouts.vertical', ['title' => 'Tambah Rekomendasi Penutup LHA/LHK'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Tambah Rekomendasi Penutup LHA/LHK
                        </h4>
                        <a href="{{ route('audit.penutup-lha-rekomendasi.index') }}" class="btn btn-secondary">
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
                    <form action="{{ route('audit.penutup-lha-rekomendasi.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="pelaporan_isi_lha_id" class="form-label">Nomor ISS (LHA/LHK) <span class="text-danger">*</span></label>
                            <select name="pelaporan_isi_lha_id" id="pelaporan_isi_lha_id" class="form-select" required>
                                <option value="">-- Pilih Nomor ISS --</option>
                                @foreach($approvedIss as $iss)
                                    <option value="{{ $iss['id'] }}" 
                                            data-nomor-lha-lhk="{{ $iss['nomor_lha_lhk'] }}"
                                            data-hasil-temuan="{{ $iss['hasil_temuan'] }}"
                                            data-permasalahan="{{ $iss['permasalahan'] }}">
                                        {{ $iss['nomor_iss'] }} - {{ $iss['nomor_lha_lhk'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih nomor ISS yang sudah diapprove untuk dibuatkan rekomendasi</div>
                            @error('pelaporan_isi_lha_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3" id="iss-details" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Detail ISS</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nomor LHA/LHK:</strong>
                                            <p id="iss-nomor-lha-lhk" class="mb-2"></p>
                                            <strong>Hasil Temuan:</strong>
                                            <p id="iss-hasil-temuan" class="mb-2"></p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Permasalahan:</strong>
                                            <p id="iss-permasalahan" class="mb-2"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="rekomendasi" class="form-label">Rekomendasi <span class="text-danger">*</span></label>
                            <textarea name="rekomendasi" id="rekomendasi" class="form-control" rows="3" maxlength="5000" required>{{ old('rekomendasi') }}</textarea>
                            <div class="form-text">Tulis rekomendasi dalam format paragraf point-point, maksimal 5000 karakter.</div>
                            @error('rekomendasi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="rencana_aksi" class="form-label">Rencana Aksi <span class="text-danger">*</span></label>
                            <textarea name="rencana_aksi" id="rencana_aksi" class="form-control" rows="3" maxlength="5000" required>{{ old('rencana_aksi') }}</textarea>
                            <div class="form-text">Tulis rencana aksi dalam format paragraf point-point, maksimal 5000 karakter.</div>
                            @error('rencana_aksi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="eviden_rekomendasi" class="form-label">Eviden Rekomendasi <span class="text-danger">*</span></label>
                            <textarea name="eviden_rekomendasi" id="eviden_rekomendasi" class="form-control" rows="3" maxlength="5000" required>{{ old('eviden_rekomendasi') }}</textarea>
                            <div class="form-text">Tulis eviden rekomendasi dalam format paragraf point-point, maksimal 5000 karakter.</div>
                            @error('eviden_rekomendasi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="pic_rekomendasi_id" class="form-label">PIC Rekomendasi <span class="text-danger">*</span></label>
                            <div id="pic-list">
                                @php 
                                    $picList = old('pic_rekomendasi_id', ['']); 
                                    // Jika old('pic_rekomendasi_id') adalah array dari ID, gunakan itu
                                    if (!empty(old('pic_rekomendasi_id')) && is_array(old('pic_rekomendasi_id'))) {
                                        $picList = old('pic_rekomendasi_id');
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
                                    <button type="button" class="btn btn-danger btn-remove-pic" @if($i==0) style="display:none" @endif>-</button>
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
                            <label for="target_waktu" class="form-label">Target Waktu <span class="text-danger">*</span></label>
                            <input type="date" name="target_waktu" id="target_waktu" class="form-control" value="{{ old('target_waktu') }}" required>
                            <div class="form-text">Pilih target waktu penyelesaian rekomendasi.</div>
                            @error('target_waktu')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-2"></i>Simpan Rekomendasi
                            </button>
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
$(document).ready(function() {
    // Handle ISS selection change
    $('#pelaporan_isi_lha_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const nomorLhaLhk = selectedOption.data('nomor-lha-lhk');
        const hasilTemuan = selectedOption.data('hasil-temuan');
        const permasalahan = selectedOption.data('permasalahan');
        
        if (selectedOption.val()) {
            // Show ISS details
            $('#iss-nomor-lha-lhk').text(nomorLhaLhk);
            $('#iss-hasil-temuan').text(hasilTemuan);
            $('#iss-permasalahan').text(permasalahan);
            $('#iss-details').show();
        } else {
            // Hide ISS details
            $('#iss-details').hide();
        }
    });
    
    // Auto-refresh ISS data if needed
    function refreshIssData() {
        const nomorSuratTugas = '{{ $nomorSuratTugas ?? "" }}';
        $.ajax({
            url: '{{ route("audit.penutup-lha-rekomendasi.get-iss-data") }}',
            type: 'GET',
            data: nomorSuratTugas ? { nomor_surat_tugas: nomorSuratTugas } : {},
            success: function(response) {
                const select = $('#pelaporan_isi_lha_id');
                const currentValue = select.val();
                
                // Clear existing options except the first one
                select.find('option:not(:first)').remove();
                
                // Add new options
                response.forEach(function(iss) {
                    const option = new Option(
                        iss.nomor_iss + ' - ' + iss.nomor_lha_lhk, 
                        iss.id, 
                        false, 
                        iss.id == currentValue
                    );
                    $(option).data({
                        'nomor-lha-lhk': iss.nomor_lha_lhk,
                        'hasil-temuan': iss.hasil_temuan,
                        'permasalahan': iss.permasalahan
                    });
                    select.append(option);
                });
                
                // Trigger change event to update details
                select.trigger('change');
            },
            error: function(xhr) {
                console.error('Error refreshing ISS data:', xhr);
            }
        });
    }
    
    // Refresh data every 30 seconds to keep it updated
    setInterval(refreshIssData, 30000);
    
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
});
</script>
@endsection 