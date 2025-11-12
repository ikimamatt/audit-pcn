@extends('layouts.vertical', ['title' => 'Tambah Pelaporan Hasil Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Input Judul LHA/LHK & Temuan</h4>
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
                <form method="POST" action="{{ route('audit.pelaporan-hasil-audit.store') }}">
                    @csrf
                    
                    <!-- Row 1: Surat Tugas dan Jenis -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Surat Tugas Audit <span class="text-danger">*</span></label>
                            <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-select" required>
                                <option value="">Pilih Surat Tugas</option>
                                @foreach($suratTugas as $s)
                                    <option value="{{ $s->id }}">{{ $s->nomor_surat_tugas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis_lha_lhk" id="jenis_lha_lhk" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="LHA">LHA</option>
                                <option value="LHK">LHK</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">PO/Konsul <span class="text-danger">*</span></label>
                            <select name="po_audit_konsul" id="po_audit_konsul" class="form-select" required disabled>
                                <option value="">Pilih Jenis terlebih dahulu</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Nomor LHA/LHK dan Kode SPI -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor LHA/LHK <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="nomor_lha_lhk" id="nomor_lha_lhk" class="form-control" value="" placeholder="xxx/AA/BB/CC/SPI.PCN.yyyy" required readonly>
                                <button type="button" class="btn btn-outline-secondary" id="generate-lha-lhk-btn">
                                    <i class="mdi mdi-refresh"></i> Generate
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode SPI <span class="text-danger">*</span></label>
                            <select name="kode_spi" id="kode_spi" class="form-select" required>
                                <option value="">Pilih Kode SPI</option>
                                <option value="SPI.01.02">SPI.01.02</option>
                                <option value="SPI.01.03">SPI.01.03</option>
                                <option value="SPI.01.04">SPI.01.04</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Area ISS -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Area ISS (Issues)</h6>
                                    <small>Tambahkan kode AOI dan risiko untuk generate nomor ISS</small>
                                </div>
                                <div class="card-body">
                                    <div id="iss-container">
                                        <!-- ISS items will be added here -->
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" id="add-iss-btn">
                                        <i class="mdi mdi-plus"></i> Tambah ISS
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Buttons -->
                    <div class="row g-3 mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan
                            </button>
                            <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-close"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template untuk ISS Item -->
<template id="iss-item-template">
    <div class="iss-item border rounded p-3 mb-3" data-iss-index="">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Hasil Temuan (AOI) <span class="text-danger">*</span></label>
                <textarea name="hasil_temuan[]" class="form-control" rows="2" required></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode AOI <span class="text-danger">*</span></label>
                <select name="kode_aoi_id[]" class="form-select kode-aoi-select" required>
                    <option value="">Pilih Kode AOI</option>
                    @foreach($kodeAoi as $aoi)
                        <option value="{{ $aoi->id }}">{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode Risiko <span class="text-danger">*</span></label>
                <select name="kode_risk_id[]" class="form-select kode-risk-select" required>
                    <option value="">Pilih Kode Risiko</option>
                    @foreach($kodeRisk as $risk)
                        <option value="{{ $risk->id }}">{{ $risk->kode_risiko }} - {{ $risk->deskripsi_risiko }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nomor ISS</label>
                <input type="text" name="nomor_iss[]" class="form-control nomor-iss-input" placeholder="ISS.xxx/PO_KONSUL/MM/NN/PP/yyyy" readonly>
                <input type="hidden" name="nomor_urut_iss[]" class="nomor-urut-iss-input">
                <button type="button" class="btn btn-outline-primary btn-sm mt-1 generate-iss-btn">
                    <i class="mdi mdi-refresh"></i> Generate
                </button>
            </div>
        </div>
        
        <!-- ISS Detail Fields -->
        <div class="row mt-3">
            <div class="col-md-12">
                <button type="button" class="btn btn-info btn-sm expand-iss-detail-btn" data-bs-toggle="collapse" data-bs-target="#iss-detail-{index}">
                    <i class="mdi mdi-chevron-down"></i> Detail ISS
                </button>
            </div>
        </div>
        
        <div class="collapse" id="iss-detail-{index}">
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Permasalahan <span class="text-danger">*</span></label>
                    <textarea name="permasalahan[]" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kriteria <span class="text-danger">*</span></label>
                    <textarea name="kriteria[]" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dampak yang Terjadi</label>
                    <textarea name="dampak_terjadi[]" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dampak Potensial</label>
                    <textarea name="dampak_potensi[]" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Signifikansi <span class="text-danger">*</span></label>
                    <select name="signifikan[]" class="form-select" required>
                        <option value="">Pilih Signifikansi</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Medium">Medium</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <h6 class="text-muted">Analisis Penyebab (Root Cause Analysis)</h6>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Penyebab <span class="text-danger">*</span></label>
                    <textarea name="penyebab[]" class="form-control" rows="4" placeholder="Jelaskan penyebab dari permasalahan yang ditemukan (People, Process, Policy, System, Eksternal)" required></textarea>
                    <small class="form-text text-muted">
                        <strong>Petunjuk:</strong> Jelaskan penyebab dari berbagai aspek seperti People (SDM), Process (Proses), Policy (Kebijakan), System (Sistem), dan Eksternal (Faktor luar).
                    </small>
                </div>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-iss-btn">
                    <i class="mdi mdi-delete"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Modal untuk Edit ISS -->
<div class="modal fade" id="editIssModal" tabindex="-1" aria-labelledby="editIssModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIssModalLabel">Edit Detail ISS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editIssForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Hasil Temuan <span class="text-danger">*</span></label>
                            <textarea name="edit_hasil_temuan" id="edit_hasil_temuan" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Permasalahan <span class="text-danger">*</span></label>
                            <textarea name="edit_permasalahan" id="edit_permasalahan" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kriteria <span class="text-danger">*</span></label>
                            <textarea name="edit_kriteria" id="edit_kriteria" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Signifikansi <span class="text-danger">*</span></label>
                            <select name="edit_signifikan" id="edit_signifikan" class="form-select" required>
                                <option value="">Pilih Signifikansi</option>
                                <option value="Tinggi">Tinggi</option>
                                <option value="Medium">Medium</option>
                                <option value="Rendah">Rendah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dampak yang Terjadi</label>
                            <textarea name="edit_dampak_terjadi" id="edit_dampak_terjadi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dampak Potensial</label>
                            <textarea name="edit_dampak_potensi" id="edit_dampak_potensi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Penyebab <span class="text-danger">*</span></label>
                            <textarea name="edit_penyebab" id="edit_penyebab" class="form-control" rows="4" placeholder="Jelaskan penyebab dari permasalahan yang ditemukan (People, Process, Policy, System, Eksternal)" required></textarea>
                            <small class="form-text text-muted">
                                <strong>Petunjuk:</strong> Jelaskan penyebab dari berbagai aspek seperti People (SDM), Process (Proses), Policy (Kebijakan), System (Sistem), dan Eksternal (Faktor luar).
                            </small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveIssChanges">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let issIndex = 0;
    let currentEditingIndex = -1;

    // Auto-set PO/Konsul berdasarkan jenis LHA/LHK
    $('#jenis_lha_lhk').change(function() {
        const jenis = $(this).val();
        const poKonsulSelect = $('#po_audit_konsul');
        
        // Reset nomor LHA/LHK ketika jenis berubah
        $('#nomor_lha_lhk').val('');
        
        if (jenis === 'LHA') {
            poKonsulSelect.html('<option value="PO AUDIT">PO AUDIT</option>');
            poKonsulSelect.val('PO AUDIT');
        } else if (jenis === 'LHK') {
            poKonsulSelect.html('<option value="KONSUL">KONSUL</option>');
            poKonsulSelect.val('KONSUL');
        } else {
            poKonsulSelect.html('<option value="">Pilih Jenis terlebih dahulu</option>');
        }
        
        poKonsulSelect.prop('disabled', false);
        
        // Auto-generate nomor LHA/LHK jika semua field sudah dipilih
        autoGenerateNomorLhaLhk();
    });

    // Reset nomor LHA/LHK ketika surat tugas audit berubah
    $('#perencanaan_audit_id').change(function() {
        $('#nomor_lha_lhk').val('');
    });

    // Auto-generate nomor LHA/LHK ketika PO/Konsul atau Kode SPI berubah
    $('#po_audit_konsul, #kode_spi').change(function() {
        // Reset nomor LHA/LHK ketika ada perubahan
        $('#nomor_lha_lhk').val('');
        // Reset semua nomor ISS ketika kode SPI berubah
        $('.nomor-iss-input').val('');
        autoGenerateNomorLhaLhk();
    });

    // Function untuk auto-generate nomor LHA/LHK
    function autoGenerateNomorLhaLhk() {
        const jenis = $('#jenis_lha_lhk').val();
        const poKonsul = $('#po_audit_konsul').val();
        const kodeSpi = $('#kode_spi').val();
        
        if (jenis && poKonsul && kodeSpi) {
            // Auto-generate langsung tanpa perlu klik tombol
            $.ajax({
                url: '{{ route("audit.pelaporan-hasil-audit.generate-nomor-lhk") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    jenis_lha_lhk: jenis,
                    po_audit_konsul: poKonsul,
                    kode_spi: kodeSpi
                },
                success: function(response) {
                    $('#nomor_lha_lhk').val(response.nomor_lha_lhk);
                    // Reset semua nomor ISS ketika nomor LHA/LHK berubah
                    $('.nomor-iss-input').val('');
                },
                error: function(xhr) {
                    console.error('Error generating nomor LHA/LHK:', xhr);
                    alert('Gagal generate nomor LHA/LHK');
                }
            });
        }
    }

    // Generate Nomor LHA/LHK
    $('#generate-lha-lhk-btn').click(function() {
        const jenis = $('#jenis_lha_lhk').val();
        const poKonsul = $('#po_audit_konsul').val();
        const kodeSpi = $('#kode_spi').val();
        
        if (!jenis || !poKonsul || !kodeSpi) {
            alert('Mohon pilih Jenis, PO/Konsul, dan Kode SPI terlebih dahulu');
            return;
        }
        
        $.ajax({
            url: '{{ route("audit.pelaporan-hasil-audit.generate-nomor-lhk") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                jenis_lha_lhk: jenis,
                po_audit_konsul: poKonsul,
                kode_spi: kodeSpi
            },
            success: function(response) {
                $('#nomor_lha_lhk').val(response.nomor_lha_lhk);
            },
            error: function(xhr) {
                console.error('Error generating nomor LHA/LHK:', xhr);
                alert('Gagal generate nomor LHA/LHK');
            }
        });
    });

    // Add ISS Item
    $('#add-iss-btn').click(function() {
        const template = document.getElementById('iss-item-template');
        const clone = template.content.cloneNode(true);
        
        // Set unique index
        $(clone).find('.iss-item').attr('data-iss-index', issIndex);
        
        // Update collapse target
        $(clone).find('.collapse').attr('id', `iss-detail-${issIndex}`);
        $(clone).find('[data-bs-target]').attr('data-bs-target', `#iss-detail-${issIndex}`);
        
        // Add to container
        $('#iss-container').append(clone);
        
        // Bind events for new item
        bindIssEvents(issIndex);
        
        issIndex++;
    });

    // Bind events for ISS items
    function bindIssEvents(index) {
        const container = $(`[data-iss-index="${index}"]`);
        
        // Generate ISS button
        container.find('.generate-iss-btn').click(function() {
            generateIssNumber(index);
        });
        
        // Remove ISS button
        container.find('.remove-iss-btn').click(function() {
            container.remove();
        });
        
        // Auto-generate when AOI and Risk are selected
        container.find('.kode-aoi-select, .kode-risk-select').change(function() {
            // Reset nomor ISS ketika ada perubahan
            container.find('.nomor-iss-input').val('');
            
            const aoival = container.find('.kode-aoi-select').val();
            const riskval = container.find('.kode-risk-select').val();
            const nomorLhaLhk = $('#nomor_lha_lhk').val();
            const kodeSpi = $('#kode_spi').val();
            
            if (aoival && riskval && nomorLhaLhk && kodeSpi) {
                generateIssNumber(index);
            }
        });
        
        // Edit ISS button
        container.find('.expand-iss-detail-btn').click(function() {
            openEditIssModal(index);
        });
    }

    // Generate ISS Number
    function generateIssNumber(index) {
        const container = $(`[data-iss-index="${index}"]`);
        const nomorLhaLhk = $('#nomor_lha_lhk').val();
        const kodeSpi = $('#kode_spi').val();
        const kodeAoiId = container.find('.kode-aoi-select').val();
        const kodeRiskId = container.find('.kode-risk-select').val();

        if (!nomorLhaLhk || !kodeSpi || !kodeAoiId || !kodeRiskId) {
            alert('Mohon lengkapi nomor LHA/LHK, kode SPI, kode AOI, dan kode risiko untuk generate nomor ISS');
            return;
        }

        $.ajax({
            url: '{{ route("audit.pelaporan-hasil-audit.generate-nomor-iss") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nomor_lha_lhk: nomorLhaLhk,
                kode_spi: kodeSpi,
                kode_aoi_id: kodeAoiId,
                kode_risk_id: kodeRiskId
            },
            success: function(response) {
                container.find('.nomor-iss-input').val(response.nomor_iss);
                // Simpan nomor urut ISS ke field hidden
                container.find('.nomor-urut-iss-input').val(response.nomor_urut_iss);
            },
            error: function(xhr) {
                console.error('Error generating nomor ISS:', xhr);
                alert('Gagal generate nomor ISS');
            }
        });
    }

    // Open Edit ISS Modal
    function openEditIssModal(index) {
        currentEditingIndex = index;
        const container = $(`[data-iss-index="${index}"]`);
        
        // Populate modal with current values
        $('#edit_hasil_temuan').val(container.find('textarea[name="hasil_temuan[]"]').val());
        $('#edit_permasalahan').val(container.find('textarea[name="permasalahan[]"]').val());
        $('#edit_kriteria').val(container.find('textarea[name="kriteria[]"]').val());
        $('#edit_signifikan').val(container.find('select[name="signifikan[]"]').val());
        $('#edit_dampak_terjadi').val(container.find('textarea[name="dampak_terjadi[]"]').val());
        $('#edit_dampak_potensi').val(container.find('textarea[name="dampak_potensi[]"]').val());
        $('#edit_penyebab').val(container.find('textarea[name="penyebab[]"]').val());
        
        $('#editIssModal').modal('show');
    }

    // Save ISS Changes
    $('#saveIssChanges').click(function() {
        if (currentEditingIndex === -1) return;
        
        const container = $(`[data-iss-index="${currentEditingIndex}"]`);
        
        // Update form fields with modal values
        container.find('textarea[name="hasil_temuan[]"]').val($('#edit_hasil_temuan').val());
        container.find('textarea[name="permasalahan[]"]').val($('#edit_permasalahan').val());
        container.find('textarea[name="kriteria[]"]').val($('#edit_kriteria').val());
        container.find('select[name="signifikan[]"]').val($('#edit_signifikan').val());
        container.find('textarea[name="dampak_terjadi[]"]').val($('#edit_dampak_terjadi').val());
        container.find('textarea[name="dampak_potensi[]"]').val($('#edit_dampak_potensi').val());
        container.find('textarea[name="penyebab[]"]').val($('#edit_penyebab').val());
        
        $('#editIssModal').modal('hide');
        currentEditingIndex = -1;
        
        // Show success message
        alert('Detail ISS berhasil diperbarui!');
    });

    // Add first ISS item by default
    $('#add-iss-btn').click();
});
</script>
@endsection 