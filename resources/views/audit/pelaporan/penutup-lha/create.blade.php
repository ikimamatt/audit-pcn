@extends('layouts.vertical', ['title' => 'Tambah Rekomendasi Penutup LHA/LHK'])

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
                            <label class="form-label">PIC Rekomendasi <span class="text-danger">*</span></label>
                            
                            <div class="mb-3">
                                <label for="pic_business_contact" class="form-label fw-bold">BUSINESS CONTACT</label>
                                <select name="pic_business_contact" id="pic_business_contact" class="form-select" required>
                                    <option value="">Pilih PIC Business Contact</option>
                                    @foreach($picUsers as $picUser)
                                        <option value="{{ $picUser->id }}" 
                                            {{ old('pic_business_contact') == $picUser->id ? 'selected' : '' }}>
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
                                            {{ old('pic_approval_1_spi') == $picUser->id ? 'selected' : '' }}>
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
                                            {{ old('pic_approval_2_spi') == $picUser->id ? 'selected' : '' }}>
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
window.addEventListener('load', function() {
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
});
</script>
@endsection 