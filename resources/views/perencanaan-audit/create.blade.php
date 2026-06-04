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
                        <select name="perencanaan_audit_id" class="form-select select2-search" required>
                            <option value="">Pilih Surat Tugas</option>
                            @forelse($suratTugas as $st)
                                <option value="{{ $st->id }}" {{ old('perencanaan_audit_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->nomor_surat_tugas }}
                                    @if($st->jenis_audit) · {{ $st->jenis_audit }}@endif
                                    @if($st->auditee) · {{ $st->auditee->divisi }}@endif
                                    @if($st->tanggal_audit_mulai && $st->tanggal_audit_sampai)
                                        · [{{ \Carbon\Carbon::parse($st->tanggal_audit_mulai)->locale('id')->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($st->tanggal_audit_sampai)->locale('id')->translatedFormat('d M Y') }}]
                                    @endif
                                </option>
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
                        <label class="form-label">Judul PKA</label>
                        <input type="text" name="judul_pka" class="form-control" required>
                    </div>

                    {{-- ===== PROSES BISNIS + RISK BASED AUDIT (Hierarki Baru) ===== --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="mdi mdi-sitemap me-1 text-primary"></i>Proses Bisnis &amp; Risk Based Audit
                        </label>
                        <small class="text-muted d-block mb-2">
                            Setiap Proses Bisnis dapat memiliki beberapa Risiko. Setiap Risiko dapat memiliki beberapa Kontrol.
                        </small>
                        <div id="pb-container"></div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" id="btn-add-pb">
                            <i class="mdi mdi-plus-circle me-1"></i>Tambah Proses Bisnis
                        </button>
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
                        <label class="form-label">Data Awal Yang Perlu Disiapkan</label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="35%">Nama Dokumen</th>
                                        <th width="35%">Ruang Lingkup</th>
                                        <th width="20%">Periode</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="data-awal-container">
                                    <tr class="data-awal-item">
                                        <td class="text-center align-middle row-number">1</td>
                                        <td><input type="text" name="data_awal_dokumen[0][nama_dokumen]" class="form-control" placeholder="Nama Dokumen" required></td>
                                        <td><input type="text" name="data_awal_dokumen[0][ruang_lingkup]" class="form-control" placeholder="Ruang Lingkup" required></td>
                                        <td><input type="text" name="data_awal_dokumen[0][periode]" class="form-control" placeholder="Periode" required></td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-data-awal"><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-info mt-1" id="btn-add-data-awal"><i class="mdi mdi-plus"></i> Tambah Data Awal</button>
                    </div>

                    <!-- Milestone -->
                    <div class="mb-3">
                        <label class="form-label">Milestone</label>
                        @php
                        $milestones = ['Surat Permintaan Dokumen kepada Auditee', 'Ekspose PKA Internal', 'Entry Meeting', 'Walkthrough', 'TOD', 'TOE', 'Draf LHA', 'Pra Exit Meeting untuk Finalisasi LHA', 'Exit Meeting'];
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

                    {{-- Upload Dokumen PKA (DISABLED)
                    <div class="mb-3">
                        <label class="form-label">Upload Dokumen PKA</label>
                        <input type="file" name="dokumen[]" class="form-control" multiple accept=".pdf,.xlsx,.xls" id="dokumenUpload">
                        <small class="text-muted">Format yang diizinkan: PDF, Excel (.xlsx, .xls). Maksimal 5MB per file.</small>
                        <div class="text-danger mt-1" id="fileError" style="display: none; font-size: 12px;"></div>
                    </div>
                    --}}

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
window.addEventListener('load', function () {

    let pbCounter       = 0;
    let risikoCounters  = {};
    let kontrolCounters = {};

    /* ── Template builders ── */
    function buildKontrolRow(pbIdx, risikoIdx, kIdx, value) {
        value = value || '';
        return `<div class="input-group mb-1 kontrol-item" data-pb="${pbIdx}" data-risiko="${risikoIdx}" data-kontrol="${kIdx}">
            <span class="input-group-text bg-success text-white" style="font-size:.75rem;min-width:32px;justify-content:center;">${kIdx + 1}</span>
            <input type="text"
                   name="proses_bisnis[${pbIdx}][risiko][${risikoIdx}][kontrol][${kIdx}][deskripsi_kontrol]"
                   class="form-control form-control-sm"
                   placeholder="Deskripsi kontrol pengendalian..."
                   value="${value}">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-kontrol"><i class="mdi mdi-close"></i></button>
        </div>`;
    }

    function buildRisikoBlock(pbIdx, risikoIdx, data) {
        data = data || {};
        const p = `proses_bisnis[${pbIdx}][risiko][${risikoIdx}]`;
        return `<div class="card mb-2 risiko-item" style="border:1.5px solid #f59e0b;border-radius:8px;" data-pb="${pbIdx}" data-risiko="${risikoIdx}">
            <div class="card-header d-flex align-items-center gap-2 py-2" style="background:#fffbeb;border-bottom:1px solid #fde68a;border-radius:7px 7px 0 0;">
                <i class="mdi mdi-alert-outline text-warning"></i>
                <span class="fw-semibold" style="font-size:.85rem;color:#92400e;">Risiko #${risikoIdx + 1}</span>
                <button type="button" class="btn btn-sm btn-outline-danger ms-auto btn-remove-risiko py-0 px-2" data-pb="${pbIdx}">
                    <i class="mdi mdi-delete-outline"></i> Hapus Risiko
                </button>
            </div>
            <div class="card-body py-2 px-3">
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Deskripsi Risiko <span class="text-danger">*</span></label>
                        <textarea name="${p}[deskripsi_risiko]" class="form-control form-control-sm" rows="2" placeholder="Deskripsi risiko..." required>${data.deskripsi_risiko || ''}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Level Risiko</label>
                        <select name="${p}[level_risiko]" class="form-select form-select-sm">
                            <option value="">-- Pilih Level --</option>
                            <option value="low" ${data.level_risiko === 'low' ? 'selected' : ''}>Low</option>
                            <option value="low to moderate" ${data.level_risiko === 'low to moderate' ? 'selected' : ''}>Low to Moderate</option>
                            <option value="moderate" ${data.level_risiko === 'moderate' ? 'selected' : ''}>Moderate</option>
                            <option value="moderate to high" ${data.level_risiko === 'moderate to high' ? 'selected' : ''}>Moderate to High</option>
                            <option value="high" ${data.level_risiko === 'high' ? 'selected' : ''}>High</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Penyebab Risiko</label>
                        <textarea name="${p}[penyebab_risiko]" class="form-control form-control-sm" rows="2" placeholder="Penyebab risiko...">${data.penyebab_risiko || ''}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Dampak Risiko</label>
                        <textarea name="${p}[dampak_risiko]" class="form-control form-control-sm" rows="2" placeholder="Dampak risiko...">${data.dampak_risiko || ''}</textarea>
                    </div>
                </div>
                <div>
                    <label class="form-label form-label-sm"><i class="mdi mdi-shield-check-outline text-success me-1"></i>Kontrol Pengendalian</label>
                    <div class="kontrol-container" id="kontrol-${pbIdx}-${risikoIdx}"></div>
                    <button type="button" class="btn btn-sm btn-outline-success btn-add-kontrol mt-1" data-pb="${pbIdx}" data-risiko="${risikoIdx}">
                        <i class="mdi mdi-plus me-1"></i>Tambah Kontrol
                    </button>
                </div>
            </div>
        </div>`;
    }

    function buildPbBlock(pbIdx, namaValue) {
        namaValue = namaValue || '';
        return `<div class="card mb-3 pb-item" style="border:2px solid #3b82f6;border-radius:10px;" data-pb="${pbIdx}">
            <div class="card-header d-flex align-items-center gap-2 py-2" style="background:#eff6ff;border-bottom:1px solid #bfdbfe;border-radius:9px 9px 0 0;">
                <i class="mdi mdi-sitemap text-primary"></i>
                <span class="fw-bold" style="font-size:.9rem;color:#1e40af;">Proses Bisnis #${pbIdx + 1}</span>
                <button type="button" class="btn btn-sm btn-outline-danger ms-auto btn-remove-pb py-0 px-2" data-pb="${pbIdx}">
                    <i class="mdi mdi-delete-outline"></i> Hapus
                </button>
            </div>
            <div class="card-body py-2 px-3">
                <div class="mb-2">
                    <label class="form-label form-label-sm fw-semibold">Nama Proses Bisnis <span class="text-danger">*</span></label>
                    <input type="text" name="proses_bisnis[${pbIdx}][nama]" class="form-control form-control-sm"
                           placeholder="Masukkan nama proses bisnis..." value="${namaValue}" required>
                </div>
                <div class="risiko-container mb-2" id="risiko-container-${pbIdx}"></div>
                <button type="button" class="btn btn-sm btn-warning btn-add-risiko" data-pb="${pbIdx}">
                    <i class="mdi mdi-plus me-1"></i>Tambah Risiko
                </button>
            </div>
        </div>`;
    }

    /* ── Tambah / Hapus PB ── */
    function addPb(namaValue, prefilledRisikoList) {
        const pbIdx = pbCounter++;
        risikoCounters[pbIdx] = 0;
        $('#pb-container').append(buildPbBlock(pbIdx, namaValue));

        if (prefilledRisikoList && prefilledRisikoList.length > 0) {
            prefilledRisikoList.forEach(function(risiko) {
                addRisiko(pbIdx, risiko, risiko.kontrolList || []);
            });
        }
    }

    addPb(); // default 1 PB kosong

    $('#btn-add-pb').on('click', function () { addPb(); });

    $(document).on('click', '.btn-remove-pb', function () {
        if ($('.pb-item').length <= 1) { alert('Minimal harus ada 1 Proses Bisnis.'); return; }
        $(this).closest('.pb-item').remove();
    });

    /* ── Tambah / Hapus Risiko ── */
    function addRisiko(pbIdx, data, prefilledKontrolList) {
        if (risikoCounters[pbIdx] === undefined) risikoCounters[pbIdx] = 0;
        const risikoIdx = risikoCounters[pbIdx]++;
        kontrolCounters[pbIdx + '_' + risikoIdx] = 0;
        $(`#risiko-container-${pbIdx}`).append(buildRisikoBlock(pbIdx, risikoIdx, data || {}));

        if (prefilledKontrolList && prefilledKontrolList.length > 0) {
            prefilledKontrolList.forEach(function(k) {
                addKontrol(pbIdx, risikoIdx, k.deskripsi_kontrol || k);
            });
        }
    }

    $(document).on('click', '.btn-add-risiko', function () {
        addRisiko($(this).data('pb'));
    });

    $(document).on('click', '.btn-remove-risiko', function () {
        $(this).closest('.risiko-item').remove();
    });

    /* ── Tambah / Hapus Kontrol ── */
    function addKontrol(pbIdx, risikoIdx, value) {
        const key = pbIdx + '_' + risikoIdx;
        if (kontrolCounters[key] === undefined) kontrolCounters[key] = 0;
        const kIdx = kontrolCounters[key]++;
        $(`#kontrol-${pbIdx}-${risikoIdx}`).append(buildKontrolRow(pbIdx, risikoIdx, kIdx, value || ''));
        renumberKontrol(pbIdx, risikoIdx);
    }

    $(document).on('click', '.btn-add-kontrol', function () {
        addKontrol($(this).data('pb'), $(this).data('risiko'));
    });

    $(document).on('click', '.btn-remove-kontrol', function () {
        const $i = $(this).closest('.kontrol-item');
        const pb = $i.data('pb'); const r = $i.data('risiko');
        $i.remove(); renumberKontrol(pb, r);
    });

    function renumberKontrol(pb, r) {
        $(`#kontrol-${pb}-${r} .kontrol-item .input-group-text`).each(function(i){ $(this).text(i+1); });
    }

    /* ── Data Awal Dokumen ── */
    let dataAwalIndex = 1;
    function updateDataAwalNumbers() {
        $('#data-awal-container .row-number').each(function(i){ $(this).text(i+1); });
    }
    $('#btn-add-data-awal').on('click', function () {
        $('#data-awal-container').append(`<tr class="data-awal-item">
            <td class="text-center align-middle row-number"></td>
            <td><input type="text" name="data_awal_dokumen[${dataAwalIndex}][nama_dokumen]" class="form-control" placeholder="Nama Dokumen" required></td>
            <td><input type="text" name="data_awal_dokumen[${dataAwalIndex}][ruang_lingkup]" class="form-control" placeholder="Ruang Lingkup" required></td>
            <td><input type="text" name="data_awal_dokumen[${dataAwalIndex}][periode]" class="form-control" placeholder="Periode" required></td>
            <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-danger btn-remove-data-awal"><i class="mdi mdi-delete"></i></button></td>
        </tr>`);
        dataAwalIndex++; updateDataAwalNumbers();
    });
    $(document).on('click', '.btn-remove-data-awal', function () {
        if ($('.data-awal-item').length > 1) { $(this).closest('tr').remove(); updateDataAwalNumbers(); }
        else alert('Minimal harus ada 1 baris data awal');
    });

    /* ── Milestone Validation ── */
    function getSafeId(m) { return m.replace(/ /g, '_'); }
    function validateMilestoneDates() {
        let valid = true, dates = {};
        $('.milestone-date').each(function () {
            const m = $(this).data('milestone');
            const t = $(this).attr('name').includes('mulai]') ? 'mulai' : 'selesai';
            if (!dates[m]) dates[m] = {};
            dates[m][t] = $(this).val();
        });
        Object.keys(dates).forEach(function (m) {
            const d = dates[m], $e = $(`#error-${getSafeId(m)}`);
            if (d.mulai && d.selesai && new Date(d.mulai) > new Date(d.selesai)) {
                $e.text('Tanggal mulai tidak boleh setelah tanggal selesai').show(); valid = false;
            } else { $e.hide(); }
        });
        return valid;
    }
    $(document).on('change', '.milestone-date', function () { validateMilestoneDates(); });

    /* ── File Upload Validation ── */
    function validateFileUpload() {
        const files = document.getElementById('dokumenUpload').files;
        const ext   = ['.pdf','.xlsx','.xls'];
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 5*1024*1024) { $('#fileError').text(`File "${files[i].name}" terlalu besar. Max 5MB.`).show(); return false; }
            if (!ext.includes('.'+files[i].name.split('.').pop().toLowerCase())) {
                $('#fileError').text(`File "${files[i].name}" tidak didukung.`).show(); return false;
            }
        }
        $('#fileError').hide(); return true;
    }
    $('#dokumenUpload').on('change', function () { validateFileUpload(); });

    /* ── Form Submit ── */
    $('#pkaForm').on('submit', function (e) {
        if (!validateMilestoneDates()) { e.preventDefault(); alert('Perbaiki tanggal milestone.'); return false; }
        if (!validateFileUpload())     { e.preventDefault(); return false; }
    });
});
</script>
@endsection
