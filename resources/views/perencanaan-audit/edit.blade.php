@extends('layouts.vertical', ['title' => 'Edit Program Kerja Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">EDIT PROGRAM KERJA AUDIT</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('audit.pka.update', $item->id) }}" enctype="multipart/form-data" id="pkaEditForm">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Surat Tugas</label>
                        <select name="perencanaan_audit_id" class="form-select select2-search" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}" {{ $item->perencanaan_audit_id == $st->id ? 'selected' : '' }}>
                                    {{ $st->nomor_surat_tugas }}
                                    @if($st->jenis_audit) · {{ $st->jenis_audit }}@endif
                                    @if($st->auditee) · {{ $st->auditee->divisi }}@endif
                                    @if($st->tanggal_audit_mulai && $st->tanggal_audit_sampai)
                                        · [{{ \Carbon\Carbon::parse($st->tanggal_audit_mulai)->locale('id')->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($st->tanggal_audit_sampai)->locale('id')->translatedFormat('d M Y') }}]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal PKA</label>
                        <input type="date" name="tanggal_pka" class="form-control" value="{{ $item->tanggal_pka }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No PKA</label>
                        <input type="text" name="no_pka" class="form-control" value="{{ $item->no_pka }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul PKA</label>
                        <input type="text" name="judul_pka" class="form-control" value="{{ $item->judul_pka }}" required>
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

                    {{-- Data hierarki untuk pre-fill via JS --}}
                    @php
                        $hierarkiData = $item->prosesBisnis->map(function($pb) {
                            return [
                                'nama'   => $pb->nama_proses_bisnis,
                                'risiko' => $pb->risikoList->map(function($r) {
                                    return [
                                        'deskripsi_risiko' => $r->deskripsi_risiko,
                                        'level_risiko'     => $r->level_risiko,
                                        'penyebab_risiko'  => $r->penyebab_risiko,
                                        'dampak_risiko'    => $r->dampak_risiko,
                                        'kontrolList'      => $r->kontrolList->map(fn($k) => ['deskripsi_kontrol' => $k->deskripsi_kontrol])->values()->toArray(),
                                    ];
                                })->values()->toArray(),
                            ];
                        })->values()->toArray();
                    @endphp
                    <script id="hierarki-data" type="application/json">@json($hierarkiData)</script>

                    <div class="mb-3">
                        <label class="form-label">Informasi Umum</label>
                        <textarea name="informasi_umum" class="form-control">{{ $item->informasi_umum }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">KPI Tidak Tercapai</label>
                        <textarea name="kpi_tidak_tercapai" class="form-control">{{ $item->kpi_tidak_tercapai }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data Awal Yang Perlu Disiapkan</label>
                        @php
                            $dataAwal = is_array($item->data_awal_dokumen) ? $item->data_awal_dokumen : json_decode($item->data_awal_dokumen ?? '[]', true) ?? [];
                            if (empty($dataAwal) || !isset($dataAwal[0]['nama_dokumen'])) {
                                $dataAwal = [['nama_dokumen' => '', 'ruang_lingkup' => '', 'periode' => '']];
                            }
                        @endphp
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
                                    @foreach($dataAwal as $i => $da)
                                    <tr class="data-awal-item">
                                        <td class="text-center align-middle row-number">{{ $i + 1 }}</td>
                                        <td><input type="text" name="data_awal_dokumen[{{ $i }}][nama_dokumen]" class="form-control" value="{{ $da['nama_dokumen'] ?? '' }}" required></td>
                                        <td><input type="text" name="data_awal_dokumen[{{ $i }}][ruang_lingkup]" class="form-control" value="{{ $da['ruang_lingkup'] ?? '' }}" required></td>
                                        <td><input type="text" name="data_awal_dokumen[{{ $i }}][periode]" class="form-control" value="{{ $da['periode'] ?? '' }}" required></td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-data-awal"><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
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
                            @php $ms = $item->milestones->firstWhere('nama_milestone', $m); @endphp
                            <div class="col-md-6 mb-2">
                                <div class="card p-2">
                                    <strong>{{ $m }}</strong>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Mulai</label>
                                            <input type="date" name="milestone[{{ $m }}][mulai]" class="form-control" value="{{ $ms->tanggal_mulai ?? '' }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Selesai</label>
                                            <input type="date" name="milestone[{{ $m }}][selesai]" class="form-control" value="{{ $ms->tanggal_selesai ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Upload Dokumen PKA (DISABLED)
                    <div class="mb-3">
                        <label class="form-label">Upload Dokumen PKA (Tambah Baru)</label>
                        <input type="file" name="dokumen[]" class="form-control" multiple>
                        @if($item->dokumen->count() > 0)
                        <div class="mt-2">
                            <small class="text-muted fw-semibold">Dokumen terkini:</small>
                            @foreach($item->dokumen as $dok)
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <i class="mdi mdi-file-pdf-box text-danger"></i>
                                    <span style="font-size:.82rem;">{{ $dok->nama_dokumen }}</span>
                                    <a href="{{ asset('storage/'.$dok->file_path) }}" target="_blank" class="btn btn-xs btn-outline-primary" style="font-size:.75rem;padding:1px 8px;">Lihat</a>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    --}}

                    <div class="mb-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('audit.pka.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {

    let pbCounter       = 0;
    let risikoCounters  = {};
    let kontrolCounters = {};

    /* ── Template builders (sama persis dengan create) ── */
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

    /* ── Helpers ── */
    function addKontrol(pbIdx, risikoIdx, value) {
        const key = pbIdx + '_' + risikoIdx;
        if (kontrolCounters[key] === undefined) kontrolCounters[key] = 0;
        const kIdx = kontrolCounters[key]++;
        $(`#kontrol-${pbIdx}-${risikoIdx}`).append(buildKontrolRow(pbIdx, risikoIdx, kIdx, value || ''));
        renumberKontrol(pbIdx, risikoIdx);
    }

    function addRisiko(pbIdx, data, kontrolList) {
        if (risikoCounters[pbIdx] === undefined) risikoCounters[pbIdx] = 0;
        const risikoIdx = risikoCounters[pbIdx]++;
        kontrolCounters[pbIdx + '_' + risikoIdx] = 0;
        $(`#risiko-container-${pbIdx}`).append(buildRisikoBlock(pbIdx, risikoIdx, data || {}));
        if (kontrolList && kontrolList.length > 0) {
            kontrolList.forEach(function(k) {
                addKontrol(pbIdx, risikoIdx, k.deskripsi_kontrol || k);
            });
        }
    }

    function addPb(namaValue, risikoList) {
        const pbIdx = pbCounter++;
        risikoCounters[pbIdx] = 0;
        $('#pb-container').append(buildPbBlock(pbIdx, namaValue || ''));
        if (risikoList && risikoList.length > 0) {
            risikoList.forEach(function(r) {
                addRisiko(pbIdx, r, r.kontrolList || []);
            });
        }
    }

    /* ── Pre-fill dari data yang ada ── */
    const hierarkiRaw = document.getElementById('hierarki-data');
    if (hierarkiRaw) {
        try {
            const hierarki = JSON.parse(hierarkiRaw.textContent || '[]');
            if (hierarki.length > 0) {
                hierarki.forEach(function(pb) {
                    addPb(pb.nama, pb.risiko || []);
                });
            } else {
                addPb(); // default kosong jika tidak ada data
            }
        } catch(e) {
            addPb();
        }
    } else {
        addPb();
    }

    /* ── Event listeners ── */
    $('#btn-add-pb').on('click', function () { addPb(); });

    $(document).on('click', '.btn-remove-pb', function () {
        if ($('.pb-item').length <= 1) { alert('Minimal harus ada 1 Proses Bisnis.'); return; }
        $(this).closest('.pb-item').remove();
    });

    $(document).on('click', '.btn-add-risiko', function () {
        addRisiko($(this).data('pb'));
    });
    $(document).on('click', '.btn-remove-risiko', function () {
        $(this).closest('.risiko-item').remove();
    });

    $(document).on('click', '.btn-add-kontrol', function () {
        addKontrol($(this).data('pb'), $(this).data('risiko'));
    });
    $(document).on('click', '.btn-remove-kontrol', function () {
        const $i = $(this).closest('.kontrol-item');
        const pb = $i.data('pb'), r = $i.data('risiko');
        $i.remove(); renumberKontrol(pb, r);
    });

    function renumberKontrol(pb, r) {
        $(`#kontrol-${pb}-${r} .kontrol-item .input-group-text`).each(function(i){ $(this).text(i+1); });
    }

    /* ── Data Awal Dokumen ── */
    let dataAwalIndex = {{ count($dataAwal ?? []) }};
    function updateDataAwalNumbers() {
        $('#data-awal-container .row-number').each(function(i){ $(this).text(i+1); });
    }
    $('#btn-add-data-awal').on('click', function () {
        $('#data-awal-container').append(`<tr class="data-awal-item">
            <td class="text-center align-middle row-number"></td>
            <td><input type="text" name="data_awal_dokumen[${dataAwalIndex}][nama_dokumen]" class="form-control" required></td>
            <td><input type="text" name="data_awal_dokumen[${dataAwalIndex}][ruang_lingkup]" class="form-control" required></td>
            <td><input type="text" name="data_awal_dokumen[${dataAwalIndex}][periode]" class="form-control" required></td>
            <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-danger btn-remove-data-awal"><i class="mdi mdi-delete"></i></button></td>
        </tr>`);
        dataAwalIndex++; updateDataAwalNumbers();
    });
    $(document).on('click', '.btn-remove-data-awal', function () {
        if ($('.data-awal-item').length > 1) { $(this).closest('tr').remove(); updateDataAwalNumbers(); }
        else alert('Minimal harus ada 1 baris data awal');
    });
});
</script>
@endsection