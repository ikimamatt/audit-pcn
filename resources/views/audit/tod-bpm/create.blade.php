@extends('layouts.vertical', ['title' => 'Tambah BPM Audit (TOD)'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Tambah BPM Audit (TOD)</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.tod-bpm.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Surat Tugas --}}
                    <div class="mb-3">
                        <label for="perencanaan_audit_id" class="form-label">Surat Tugas Audit <span class="text-danger">*</span></label>
                        <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-control select2-search" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}" {{ old('perencanaan_audit_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->nomor_surat_tugas }}
                                    @if($st->jenis_audit) · {{ $st->jenis_audit }}@endif
                                    @if($st->auditee) · {{ $st->auditee->divisi }}@endif
                                    @if($st->tanggal_audit_mulai && $st->tanggal_audit_sampai)
                                        · [{{ \Carbon\Carbon::parse($st->tanggal_audit_mulai)->locale('id')->translatedFormat('d M Y') }}
                                        - {{ \Carbon\Carbon::parse($st->tanggal_audit_sampai)->locale('id')->translatedFormat('d M Y') }}]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Judul BPM --}}
                    <div class="mb-3">
                        <label for="judul_bpm" class="form-label">Judul BPM <span class="text-danger">*</span></label>
                        <textarea name="judul_bpm" id="judul_bpm" class="form-control" rows="2" required>{{ old('judul_bpm') }}</textarea>
                    </div>

                    {{-- Nama BPO --}}
                    <div class="mb-3">
                        <label for="nama_bpo" class="form-label">Nama BPO <span class="text-danger">*</span></label>
                        <textarea name="nama_bpo" id="nama_bpo" class="form-control" rows="2" required>{{ old('nama_bpo') }}</textarea>
                    </div>

                    {{-- Walkthrough --}}
                    <div class="mb-3">
                        <label for="walkthrough_id" class="form-label">File BPM dari Walkthrough <span class="text-danger">*</span></label>
                        <select name="walkthrough_id" id="walkthrough_id" class="form-control select2-search" required>
                            <option value="">Pilih Surat Tugas terlebih dahulu</option>
                        </select>
                        <small class="text-muted">Hanya walkthrough yang sudah approved dan memiliki file BPM</small>
                        <div id="walkthrough-file-info" class="mt-2" style="display:none;">
                            <div class="alert alert-info py-2">
                                <i class="mdi mdi-file-pdf me-1"></i>
                                <span id="walkthrough-file-name"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Risiko & Kontrol dari PKA --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Risiko &amp; Kontrol dari PKA</label>
                        <div id="hierarki-container">
                            <div class="alert alert-secondary text-center py-3" id="hierarki-placeholder">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Pilih Surat Tugas untuk memuat daftar risiko dari PKA.
                            </div>
                        </div>
                    </div>

                    {{-- File KKA TOD --}}
                    <div class="mb-3">
                        <label for="file_kka_tod" class="form-label">Upload File KKA ToD</label>
                        <input type="file" name="file_kka_tod" id="file_kka_tod" class="form-control" accept=".pdf">
                        <small class="text-muted">PDF, maks. 5MB — Opsional</small>
                    </div>

                    {{-- Hasil Evaluasi --}}
                    <div class="mb-3">
                        <label for="hasil_evaluasi" class="form-label">Hasil Evaluasi TOD <span class="text-danger">*</span></label>
                        <select name="hasil_evaluasi" id="hasil_evaluasi" class="form-control" required>
                            <option value="">Pilih Hasil Evaluasi</option>
                            <option value="Sesuai" {{ old('hasil_evaluasi') == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                            <option value="Tidak Sesuai" {{ old('hasil_evaluasi') == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('audit.tod-bpm.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    const walkthroughs      = @json($walkthroughs);
    const apiUrl            = "{{ url('audit/pka/hierarki-flat') }}";
    const hierarkiContainer = document.getElementById('hierarki-container');
    const walkthroughFileInfo = document.getElementById('walkthrough-file-info');
    const walkthroughFileName = document.getElementById('walkthrough-file-name');

    // ── Walkthrough filter ─────────────────────────────────────────────────
    function updateWalkthroughs(pid) {
        const $wt = $('#walkthrough_id');
        $wt.empty().append('<option value="">Pilih Walkthrough</option>');

        if (pid && walkthroughs[pid]) {
            walkthroughs[pid].forEach(function (wt) {
                $wt.append(new Option('Walkthrough – ' + (wt.tanggal_walkthrough || 'N/A'), wt.id));
                $wt.find('option:last').data('file', wt.file_bpm || '');
            });
            if ($wt.hasClass('select2-hidden-accessible')) $wt.trigger('change.select2');
        } else {
            $wt.append('<option value="" disabled>Tidak ada walkthrough untuk surat tugas ini</option>');
        }

        walkthroughFileInfo.style.display = 'none';
    }

    $('#walkthrough_id').on('change', function () {
        const sel  = this.options[this.selectedIndex];
        const file = sel ? ($(sel).data('file') || sel.getAttribute('data-file') || '') : '';
        if (file) {
            walkthroughFileName.textContent = 'File BPM: ' + file.split('/').pop();
            walkthroughFileInfo.style.display = 'block';
        } else {
            walkthroughFileInfo.style.display = 'none';
        }
    });

    // ── Hierarki PKA ───────────────────────────────────────────────────────
    function loadHierarki(pid) {
        if (!pid) {
            hierarkiContainer.innerHTML = `
                <div class="alert alert-secondary text-center py-3">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Pilih Surat Tugas untuk memuat daftar risiko dari PKA.
                </div>`;
            return;
        }

        hierarkiContainer.innerHTML = `
            <div class="text-center py-3 text-muted">
                <div class="spinner-border spinner-border-sm me-2"></div> Memuat data risiko...
            </div>`;

        fetch(`${apiUrl}/${pid}`)
            .then(r => r.json())
            .then(data => {
                if (!data.has_hierarki) {
                    const pkaUrl = data.pka_id
                        ? `{{ url('audit/pka') }}/${data.pka_id}/edit`
                        : `{{ url('audit/pka') }}`;
                    hierarkiContainer.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert-outline me-1"></i>
                            <strong>PKA belum memiliki hierarki risiko &amp; kontrol.</strong><br>
                            Silakan edit PKA terlebih dahulu untuk menambahkan Proses Bisnis → Risiko → Kontrol.
                            <a href="${pkaUrl}" class="btn btn-sm btn-warning ms-2" target="_blank">
                                <i class="mdi mdi-pencil me-1"></i>Buka PKA
                            </a>
                        </div>`;
                    return;
                }

                if (data.risiko.length === 0) {
                    hierarkiContainer.innerHTML = `<div class="alert alert-info">Tidak ada risiko di PKA ini.</div>`;
                    return;
                }

                renderHierarki(data.risiko);
            })
            .catch(() => {
                hierarkiContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat data risiko. Coba lagi.</div>`;
            });
    }

    function renderHierarki(risikoList) {
        let html = `<div class="list-group">`;

        risikoList.forEach((risiko, i) => {
            const rId = `risiko-${risiko.id}`;
            html += `
            <div class="list-group-item list-group-item-action p-0 mb-2 border rounded">
                <div class="p-3">
                    <div class="form-check">
                        <input class="form-check-input risiko-checkbox" type="checkbox"
                               name="pka_risiko_ids[]" value="${risiko.id}"
                               id="${rId}" data-target="kontrol-group-${risiko.id}" checked>
                        <label class="form-check-label fw-semibold" for="${rId}">
                            <span class="badge bg-danger-subtle text-danger me-1">R${i + 1}</span>
                            ${escHtml(risiko.deskripsi_risiko)}
                        </label>
                    </div>
                    ${risiko.penyebab_risiko ? `<div class="text-muted small ms-4 mt-1"><strong>Penyebab:</strong> ${escHtml(risiko.penyebab_risiko)}</div>` : ''}
                    ${risiko.dampak_risiko ? `<div class="text-muted small ms-4"><strong>Dampak:</strong> ${escHtml(risiko.dampak_risiko)}</div>` : ''}
                </div>`;

            if (risiko.kontrol && risiko.kontrol.length > 0) {
                html += `
                <div class="kontrol-group px-4 pb-3" id="kontrol-group-${risiko.id}" style="display:block;">
                    <div class="border-start border-2 border-primary ps-3">
                        <p class="text-muted small mb-2 fw-semibold">Pilih Kontrol yang diuji:</p>`;

                risiko.kontrol.forEach((k, j) => {
                    html += `
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox"
                                   name="pka_kontrol_ids[]" value="${k.id}"
                                   id="kontrol-${k.id}" checked>
                            <label class="form-check-label small" for="kontrol-${k.id}">
                                <span class="badge bg-primary-subtle text-primary me-1">K${j + 1}</span>
                                ${escHtml(k.deskripsi_kontrol)}
                            </label>
                        </div>`;
                });

                html += `</div></div>`;
            }

            html += `</div>`;
        });

        html += `</div>`;
        hierarkiContainer.innerHTML = html;

        // Toggle kontrol saat risiko dicentang
        document.querySelectorAll('.risiko-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                const group = document.getElementById(this.dataset.target);
                if (!group) return;
                group.style.display = this.checked ? 'block' : 'none';
                if (!this.checked) {
                    group.querySelectorAll('input[type="checkbox"]').forEach(k => k.checked = false);
                }
            });
        });
    }

    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Event: Select2 change ──────────────────────────────────────────────
    $('#perencanaan_audit_id').on('change', function () {
        const pid = $(this).val();
        updateWalkthroughs(pid);
        loadHierarki(pid);
    });

    // ── Init: jika sudah ada nilai terpilih saat halaman load ─────────────
    const initPid = $('#perencanaan_audit_id').val();
    if (initPid) {
        updateWalkthroughs(initPid);
        loadHierarki(initPid);
    }
});
</script>
@endsection