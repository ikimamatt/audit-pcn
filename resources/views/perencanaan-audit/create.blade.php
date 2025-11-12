@extends('layouts.vertical', ['title' => 'Tambah Program Kerja Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">CREATE PROGRAM KERJA AUDIT</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('audit.pka.store') }}" enctype="multipart/form-data" id="pkaForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Surat Tugas</label>
                        <select name="perencanaan_audit_id" class="form-select" required>
                            <option value="">Pilih Surat Tugas</option>
                            @forelse($suratTugas as $st)
                                <option value="{{ $st->id }}">{{ $st->nomor_surat_tugas }}</option>
                            @empty
                                <option value="" disabled>Semua surat tugas sudah memiliki PKA</option>
                            @endforelse
                        </select>
                        @if($suratTugas->isEmpty())
                            <div class="alert alert-info mt-2">
                                <i class="mdi mdi-information-outline me-2"></i>
                                Semua surat tugas sudah memiliki Program Kerja Audit. 
                                Silakan buat surat tugas baru terlebih dahulu.
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal PKA</label>
                        <input type="date" name="tanggal_pka" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No PKA</label>
                        <input type="text" name="no_pka" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Informasi Umum</label>
                        <textarea name="informasi_umum" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">KPI Tidak Tercapai</label>
                        <textarea name="kpi_tidak_tercapai" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Awal Dokumen Audit</label>
                        <textarea name="data_awal_dokumen" class="form-control"></textarea>
                    </div>
                    <!-- Risk Based Audit -->
                    <div class="mb-3">
                        <label class="form-label">Risk Based Audit</label>
                        <div id="risk-list">
                            <!-- Dynamic risk input, JS akan menambah/menghapus -->
                        </div>
                        <button type="button" class="btn btn-sm btn-info" id="btn-add-risk">Tambah Risk</button>
                    </div>
                    <!-- Milestone -->
                    <div class="mb-3">
                        <label class="form-label">Milestone</label>
                        @php
                        $milestones = ['Entry Meeting', 'Walkthrough', 'TOD', 'TOE', 'Draf LHA', 'Exit Meeting'];
                        @endphp
                        <div class="row">
                            @foreach($milestones as $m)
                            <div class="col-md-6 mb-2">
                                <div class="card p-2">
                                    <strong>{{ $m }}</strong>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Mulai</label>
                                            <input type="date" name="milestone[{{ $m }}][mulai]" class="form-control milestone-date" data-milestone="{{ $m }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Selesai</label>
                                            <input type="date" name="milestone[{{ $m }}][selesai]" class="form-control milestone-date" data-milestone="{{ $m }}" required>
                                        </div>
                                    </div>
                                    <div class="text-danger mt-1" id="error-{{ str_replace(' ', '_', $m) }}" style="display: none; font-size: 12px;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Upload Dokumen -->
                    <div class="mb-3">
                        <label class="form-label">Upload Dokumen PKA</label>
                        <input type="file" name="dokumen[]" class="form-control" multiple accept=".pdf,.xlsx,.xls" id="dokumenUpload">
                        <small class="text-muted">Format yang diizinkan: PDF, Excel (.xlsx, .xls). Maksimal 5MB per file.</small>
                        <div class="text-danger mt-1" id="fileError" style="display: none; font-size: 12px;"></div>
                    </div>
                    <!-- Approval akan diimplementasikan selanjutnya -->
                    <div class="mb-3 d-flex gap-2">
                        @if($suratTugas->isNotEmpty())
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        @else
                            <button type="submit" class="btn btn-primary" disabled>Simpan</button>
                        @endif
                        <a href="{{ route('audit.pka.index') }}" class="btn btn-secondary">Batal</a>
                        @if($suratTugas->isEmpty())
                            <a href="{{ route('audit.perencanaan.create') }}" class="btn btn-info">
                                <i class="mdi mdi-plus-circle me-1"></i>
                                Buat Surat Tugas Baru
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Dynamic Risk Based Audit
let riskIndex = 0;
function riskInput(idx, data = {}) {
    return `<div class='card mb-2 p-2 risk-item'>
        <div class='row'>
            <div class='col-md-3'>
                <label class='form-label'>Deskripsi Risiko</label>
                <textarea name='risk[${idx}][deskripsi_resiko]' class='form-control' placeholder='Deskripsi Risiko' required>${data.deskripsi_resiko||''}</textarea>
            </div>
            <div class='col-md-2'>
                <label class='form-label'>Penyebab Risiko</label>
                <textarea name='risk[${idx}][penyebab_resiko]' class='form-control' placeholder='Penyebab Risiko' required>${data.penyebab_resiko||''}</textarea>
            </div>
            <div class='col-md-2'>
                <label class='form-label'>Dampak Risiko</label>
                <textarea name='risk[${idx}][dampak_resiko]' class='form-control' placeholder='Dampak Risiko' required>${data.dampak_resiko||''}</textarea>
            </div>
            <div class='col-md-3'>
                <label class='form-label'>Pengendalian Eksisting</label>
                <textarea name='risk[${idx}][pengendalian_eksisting]' class='form-control' placeholder='Pengendalian Eksisting' required>${data.pengendalian_eksisting||''}</textarea>
            </div>
            <div class='col-md-2 d-flex align-items-end'>
                <button type='button' class='btn btn-danger btn-remove-risk'>Hapus</button>
            </div>
        </div>
    </div>`;
}
$(document).on('click', '#btn-add-risk', function() {
    $('#risk-list').append(riskInput(riskIndex++));
});
$(document).on('click', '.btn-remove-risk', function() {
    $(this).closest('.risk-item').remove();
});

// Milestone date validation
$(document).ready(function() {
    // Function to create safe ID
    function getSafeId(milestone) {
        return milestone.replace(/ /g, '_');
    }
    
    // Function to validate milestone dates
    function validateMilestoneDates() {
        let isValid = true;
        let milestoneDates = {};
        
        // Collect all milestone dates
        $('.milestone-date').each(function() {
            const milestone = $(this).data('milestone');
            const type = $(this).attr('name').includes('mulai]') ? 'mulai' : 'selesai';
            const date = $(this).val();
            
            if (!milestoneDates[milestone]) {
                milestoneDates[milestone] = {};
            }
            milestoneDates[milestone][type] = date;
        });
        
        // Validate each milestone
        Object.keys(milestoneDates).forEach(function(milestone) {
            const dates = milestoneDates[milestone];
            const errorElement = $(`#error-${getSafeId(milestone)}`);
            
            // Check if both dates are filled
            if (dates.mulai && dates.selesai) {
                const startDate = new Date(dates.mulai);
                const endDate = new Date(dates.selesai);
                
                // Check if start date is after end date
                if (startDate > endDate) {
                    errorElement.text('Tanggal mulai tidak boleh setelah tanggal selesai').show();
                    isValid = false;
                }
                // Check if start date and end date are the same day - REMOVED: milestone bisa di hari yang sama
                // else if (startDate.getTime() === endDate.getTime()) {
                //     errorElement.text('Tanggal mulai dan selesai tidak boleh sama hari').show();
                //     isValid = false;
                // }
                else {
                    errorElement.hide();
                }
            } else {
                errorElement.hide();
            }
        });
        
        // Check for overlapping dates between milestones - milestone tidak boleh overlap
        let allDates = [];
        Object.keys(milestoneDates).forEach(function(milestone) {
            const dates = milestoneDates[milestone];
            if (dates.mulai && dates.selesai) {
                allDates.push({
                    milestone: milestone,
                    start: new Date(dates.mulai),
                    end: new Date(dates.selesai)
                });
            }
        });
        
        // Check for overlaps
        for (let i = 0; i < allDates.length; i++) {
            for (let j = i + 1; j < allDates.length; j++) {
                const date1 = allDates[i];
                const date2 = allDates[j];
                
                // Check if dates overlap
                if (date1.start <= date2.end && date2.start <= date1.end) {
                    const errorElement1 = $(`#error-${getSafeId(date1.milestone)}`);
                    const errorElement2 = $(`#error-${getSafeId(date2.milestone)}`);
                    
                    errorElement1.text(`Tanggal bertabrakan dengan ${date2.milestone}`).show();
                    errorElement2.text(`Tanggal bertabrakan dengan ${date1.milestone}`).show();
                    isValid = false;
                }
            }
        }
        
        return isValid;
    }
    
    // Add event listeners for date changes
    $(document).on('change', '.milestone-date', function() {
        validateMilestoneDates();
    });
    
    // Form submission validation
    $('#pkaForm').on('submit', function(e) {
        if (!validateMilestoneDates()) {
            e.preventDefault();
            alert('Mohon perbaiki tanggal milestone yang bertabrakan atau tidak valid.');
            return false;
        }
        
        // Validate file upload
        if (!validateFileUpload()) {
            e.preventDefault();
            return false;
        }
    });
    
    // File upload validation
    function validateFileUpload() {
        const fileInput = document.getElementById('dokumenUpload');
        const files = fileInput.files;
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        const allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        const allowedExtensions = ['.pdf', '.xlsx', '.xls'];
        
        if (files.length === 0) {
            return true; // No files selected is OK
        }
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Check file size
            if (file.size > maxSize) {
                showFileError(`File "${file.name}" terlalu besar. Maksimal 5MB per file.`);
                return false;
            }
            
            // Check file type
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                showFileError(`File "${file.name}" tidak didukung. Hanya PDF dan Excel yang diizinkan.`);
                return false;
            }
            
            // Additional MIME type check
            if (!allowedTypes.includes(file.type) && file.type !== '') {
                showFileError(`File "${file.name}" tidak didukung. Hanya PDF dan Excel yang diizinkan.`);
                return false;
            }
        }
        
        hideFileError();
        return true;
    }
    
    function showFileError(message) {
        $('#fileError').text(message).show();
    }
    
    function hideFileError() {
        $('#fileError').hide();
    }
    
    // Real-time file validation
    $('#dokumenUpload').on('change', function() {
        validateFileUpload();
    });
});
</script>
@endsection 