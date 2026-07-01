@extends('layouts.vertical', ['title' => 'Tambah TOE Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Tambah TOE Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.toe.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="return_url" value="{{ $returnUrl ?? '' }}">

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
                        <select name="judul_bpm" id="judul_bpm" class="form-control select2-search" required>
                            <option value="">-- Pilih Surat Tugas terlebih dahulu --</option>
                        </select>
                        <small class="text-muted">Judul BPM dari TOD yang sudah dibuat untuk surat tugas ini</small>
                    </div>

                    {{-- Pemilihan Sampel --}}
                    <div class="mb-3">
                        <label for="pemilihan_sampel_audit" class="form-label">Pemilihan Sampel Audit</label>
                        <textarea name="pemilihan_sampel_audit" id="pemilihan_sampel_audit" class="form-control" rows="3">{{ old('pemilihan_sampel_audit') }}</textarea>
                    </div>

                    {{-- Risiko & Kontrol dari PKA --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Risiko &amp; Kontrol dari PKA</label>
                        <div id="hierarki-container">
                            <div class="alert alert-secondary text-center py-3">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Pilih Surat Tugas untuk memuat daftar risiko dari PKA.
                            </div>
                        </div>
                    </div>

                    {{-- File KKA TOE --}}
                    <div class="mb-3">
                        <label for="file_kka_toe" class="form-label">Upload File KKA ToE</label>
                        <input type="file" name="file_kka_toe" id="file_kka_toe" class="form-control" accept=".pdf">
                        <small class="text-muted">PDF, maks. 5MB — Opsional</small>
                    </div>

                    {{-- Hasil Evaluasi --}}
                    <div class="mb-3">
                        <label for="hasil_evaluasi" class="form-label">Evaluasi Pengendalian <span class="text-danger">*</span></label>
                        <select name="hasil_evaluasi" id="hasil_evaluasi" class="form-control" required>
                            <option value="">Pilih Hasil Evaluasi</option>
                            <option value="Efektif" {{ old('hasil_evaluasi') == 'Efektif' ? 'selected' : '' }}>Efektif</option>
                            <option value="Tidak Efektif" {{ old('hasil_evaluasi') == 'Tidak Efektif' ? 'selected' : '' }}>Tidak Efektif</option>
                            <option value="Efektif Sebagian" {{ old('hasil_evaluasi') == 'Efektif Sebagian' ? 'selected' : '' }}>Efektif Sebagian</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ $returnUrl ?? route('audit.toe.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $bpmJson = $bpmList->map(fn($b) => [
        'judul_bpm'            => $b->judul_bpm,
        'perencanaan_audit_id' => $b->perencanaan_audit_id,
    ])->values();
@endphp

@section('script')
<script>
window.addEventListener('load', function () {
    const allBpmData        = @json($bpmJson);
    const apiUrl            = "{{ url('audit/pka/hierarki-flat') }}";
    const hierarkiContainer = document.getElementById('hierarki-container');

    // ── Filter Judul BPM ───────────────────────────────────────────────────
    function filterBpm(pid) {
        const $jb = $('#judul_bpm');
        $jb.empty();
        if (!pid) {
            $jb.append('<option value="">-- Pilih Surat Tugas terlebih dahulu --</option>');
            return;
        }
        const filtered = allBpmData.filter(b => b.perencanaan_audit_id == pid);
        if (filtered.length === 0) {
            $jb.append('<option value="">Tidak ada TOD untuk surat tugas ini</option>');
        } else {
            $jb.append('<option value="">Pilih Judul BPM</option>');
            filtered.forEach(b => $jb.append(new Option(b.judul_bpm, b.judul_bpm)));
        }
        if ($jb.hasClass('select2-hidden-accessible')) $jb.trigger('change.select2');
    }

    // ── Hierarki PKA ───────────────────────────────────────────────────────
    function loadHierarki(pid) {
        if (!pid) {
            hierarkiContainer.innerHTML = `<div class="alert alert-secondary text-center py-3">
                <i class="mdi mdi-information-outline me-1"></i>
                Pilih Surat Tugas untuk memuat daftar risiko dari PKA.
            </div>`;
            return;
        }

        hierarkiContainer.innerHTML = `<div class="text-center py-3 text-muted">
            <div class="spinner-border spinner-border-sm me-2"></div> Memuat data risiko...
        </div>`;

        fetch(`${apiUrl}/${pid}`)
            .then(r => r.json())
            .then(data => {
                if (!data.has_hierarki) {
                    const pkaUrl = data.pka_id ? `{{ url('audit/pka') }}/${data.pka_id}/edit` : `{{ url('audit/pka') }}`;
                    hierarkiContainer.innerHTML = `<div class="alert alert-warning">
                        <i class="mdi mdi-alert-outline me-1"></i>
                        <strong>PKA belum memiliki hierarki risiko &amp; kontrol.</strong><br>
                        <a href="${pkaUrl}" class="btn btn-sm btn-warning ms-2" target="_blank">Buka PKA</a>
                    </div>`;
                    return;
                }
                renderHierarki(data.risiko, [], []);
            })
            .catch(() => {
                hierarkiContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat data risiko.</div>`;
            });
    }

    function renderHierarki(risikoList, preRisiko, preKontrol) {
        let html = `<div class="list-group">`;
        risikoList.forEach((risiko, i) => {
            const rId = `risiko-${risiko.id}`;
            const isChecked = preRisiko.includes(risiko.id);
            html += `<div class="list-group-item p-0 mb-2 border rounded">
                <div class="p-3">
                    <div class="form-check">
                        <input class="form-check-input risiko-checkbox" type="checkbox"
                               name="pka_risiko_ids[]" value="${risiko.id}"
                               id="${rId}" data-target="kontrol-group-${risiko.id}"
                               checked>
                        <label class="form-check-label fw-semibold" for="${rId}">
                            <span class="badge bg-danger-subtle text-danger me-1">R${i+1}</span>
                            ${esc(risiko.deskripsi_risiko)}
                        </label>
                    </div>
                    ${risiko.penyebab_risiko ? `<div class="text-muted small ms-4 mt-1"><strong>Penyebab:</strong> ${esc(risiko.penyebab_risiko)}</div>` : ''}
                    ${risiko.dampak_risiko ? `<div class="text-muted small ms-4"><strong>Dampak:</strong> ${esc(risiko.dampak_risiko)}</div>` : ''}
                </div>`;
            if (risiko.kontrol && risiko.kontrol.length > 0) {
                html += `<div class="kontrol-group px-4 pb-3" id="kontrol-group-${risiko.id}" style="display:block;">
                    <div class="border-start border-2 border-primary ps-3">
                        <p class="text-muted small mb-2 fw-semibold">Pilih Kontrol yang diuji:</p>`;
                risiko.kontrol.forEach((k, j) => {
                    html += `<div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="pka_kontrol_ids[]"
                               value="${k.id}" id="kontrol-${k.id}" checked>
                        <label class="form-check-label small" for="kontrol-${k.id}">
                            <span class="badge bg-primary-subtle text-primary me-1">K${j+1}</span>
                            ${esc(k.deskripsi_kontrol)}
                        </label>
                    </div>`;
                });
                html += `</div></div>`;
            }
            html += `</div>`;
        });
        html += `</div>`;
        hierarkiContainer.innerHTML = html;

        document.querySelectorAll('.risiko-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                const g = document.getElementById(this.dataset.target);
                if (!g) return;
                g.style.display = this.checked ? 'block' : 'none';
                if (!this.checked) g.querySelectorAll('input[type="checkbox"]').forEach(k => k.checked = false);
            });
        });
    }

    function esc(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    // ── jQuery Select2 change event ───────────────────────────────────────
    $('#perencanaan_audit_id').on('change', function () {
        const pid = $(this).val();
        filterBpm(pid);
        loadHierarki(pid);
    });

    // ── Init saat halaman dimuat ──────────────────────────────────────────
    const initPid = $('#perencanaan_audit_id').val();
    if (initPid) {
        filterBpm(initPid);
        loadHierarki(initPid);
    }
});
</script>
@endsection