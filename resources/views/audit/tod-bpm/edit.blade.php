@extends('layouts.vertical', ['title' => 'Edit BPM Audit (TOD)'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit BPM Audit (TOD)</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.tod-bpm.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    {{-- Surat Tugas --}}
                    <div class="mb-3">
                        <label for="perencanaan_audit_id" class="form-label">Surat Tugas Audit <span class="text-danger">*</span></label>
                        <select name="perencanaan_audit_id" id="perencanaan_audit_id" class="form-control select2-search" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($suratTugas as $st)
                                <option value="{{ $st->id }}" {{ $item->perencanaan_audit_id == $st->id ? 'selected' : '' }}>
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
                        <textarea name="judul_bpm" id="judul_bpm" class="form-control" rows="2" required>{{ $item->judul_bpm }}</textarea>
                    </div>

                    {{-- Nama BPO --}}
                    <div class="mb-3">
                        <label for="nama_bpo" class="form-label">Nama BPO <span class="text-danger">*</span></label>
                        <textarea name="nama_bpo" id="nama_bpo" class="form-control" rows="2" required>{{ $item->nama_bpo }}</textarea>
                    </div>

                    {{-- Walkthrough --}}
                    <div class="mb-3">
                        <label class="form-label">File BPM Saat Ini</label>
                        @if($item->file_bpm)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_bpm) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="mdi mdi-download me-1"></i> Download File BPM
                                </a>
                            </div>
                        @endif
                        <label for="walkthrough_id" class="form-label">Ganti File BPM dari Walkthrough (Opsional)</label>
                        <select name="walkthrough_id" id="walkthrough_id" class="form-control select2-search">
                            <option value="">Pertahankan File Saat Ini</option>
                        </select>
                        <small class="text-muted">Kosongkan untuk mempertahankan file BPM saat ini</small>
                    </div>

                    {{-- Risiko & Kontrol dari PKA --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Risiko &amp; Kontrol dari PKA</label>
                        <div id="hierarki-container">
                            <div class="text-center py-3 text-muted">
                                <div class="spinner-border spinner-border-sm me-2"></div> Memuat data risiko...
                            </div>
                        </div>
                    </div>

                    {{-- File KKA TOD --}}
                    <div class="mb-3">
                        <label for="file_kka_tod" class="form-label">File KKA ToD</label>
                        @if($item->file_kka_tod)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_kka_tod) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="mdi mdi-download me-1"></i> Download KKA ToD
                                </a>
                            </div>
                        @endif
                        <input type="file" name="file_kka_tod" id="file_kka_tod" class="form-control" accept=".pdf">
                        <small class="text-muted">PDF, maks. 5MB — Kosongkan jika tidak ingin mengganti</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
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
    const hierarkiContainer  = document.getElementById('hierarki-container');
    const walkthroughs       = @json($walkthroughs);
    const apiUrl             = "{{ url('audit/pka/hierarki-flat') }}";
    const selectedRisikoIds  = @json($selectedRisikoIds);
    const selectedKontrolIds = @json($selectedKontrolIds);
    const originalPid        = '{{ $item->perencanaan_audit_id }}';

    // ── Walkthrough filter ─────────────────────────────────────────────────
    function updateWalkthroughs(pid) {
        const $wt = $('#walkthrough_id');
        $wt.empty().append('<option value="">Pertahankan File Saat Ini</option>');
        if (pid && walkthroughs[pid]) {
            walkthroughs[pid].forEach(wt => {
                $wt.append(new Option('Walkthrough – ' + (wt.tanggal_walkthrough || 'N/A'), wt.id));
            });
        }
        if ($wt.hasClass('select2-hidden-accessible')) $wt.trigger('change.select2');
    }

    // ── Hierarki PKA ───────────────────────────────────────────────────────
    function loadHierarki(pid) {
        if (!pid) {
            hierarkiContainer.innerHTML = `<div class="alert alert-secondary text-center py-3">
                <i class="mdi mdi-information-outline me-1"></i>
                Pilih Surat Tugas untuk memuat daftar risiko.
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
                const usePreselect = (pid == originalPid);
                renderHierarki(data.risiko, usePreselect ? selectedRisikoIds : [], usePreselect ? selectedKontrolIds : []);
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
                               ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label fw-semibold" for="${rId}">
                            <span class="badge bg-danger-subtle text-danger me-1">R${i+1}</span>
                            ${esc(risiko.deskripsi_risiko)}
                        </label>
                    </div>
                    ${risiko.penyebab_risiko ? `<div class="text-muted small ms-4 mt-1"><strong>Penyebab:</strong> ${esc(risiko.penyebab_risiko)}</div>` : ''}
                    ${risiko.dampak_risiko ? `<div class="text-muted small ms-4"><strong>Dampak:</strong> ${esc(risiko.dampak_risiko)}</div>` : ''}
                </div>`;
            if (risiko.kontrol && risiko.kontrol.length > 0) {
                html += `<div class="kontrol-group px-4 pb-3" id="kontrol-group-${risiko.id}" style="display:${isChecked?'block':'none'};">
                    <div class="border-start border-2 border-primary ps-3">
                        <p class="text-muted small mb-2 fw-semibold">Pilih Kontrol yang diuji:</p>`;
                risiko.kontrol.forEach((k, j) => {
                    html += `<div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="pka_kontrol_ids[]"
                               value="${k.id}" id="kontrol-${k.id}" ${preKontrol.includes(k.id)?'checked':''}>
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
        updateWalkthroughs(pid);
        loadHierarki(pid);
    });

    // ── Init saat halaman dimuat ──────────────────────────────────────────
    const initPid = $('#perencanaan_audit_id').val();
    if (initPid) {
        updateWalkthroughs(initPid);
        loadHierarki(initPid);
    }
});
</script>
@endsection