@extends('layouts.vertical', ['title' => 'Edit TOE Audit'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit TOE Audit</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('audit.toe.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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
                        <select name="judul_bpm" id="judul_bpm" class="form-control select2-search" required>
                            <option value="">Pilih Judul BPM</option>
                            @foreach($bpmList as $bpm)
                                <option value="{{ $bpm->judul_bpm }}"
                                    {{ trim($item->judul_bpm) === trim($bpm->judul_bpm) ? 'selected' : '' }}>
                                    {{ $bpm->judul_bpm }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pemilihan Sampel --}}
                    <div class="mb-3">
                        <label for="pemilihan_sampel_audit" class="form-label">Pemilihan Sampel Audit</label>
                        <textarea name="pemilihan_sampel_audit" id="pemilihan_sampel_audit" class="form-control" rows="3">{{ $item->pemilihan_sampel_audit }}</textarea>
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

                    {{-- File KKA TOE --}}
                    <div class="mb-3">
                        <label for="file_kka_toe" class="form-label">File KKA ToE</label>
                        @if($item->file_kka_toe)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $item->file_kka_toe) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="mdi mdi-download me-1"></i> Download KKA ToE
                                </a>
                            </div>
                        @endif
                        <input type="file" name="file_kka_toe" id="file_kka_toe" class="form-control" accept=".pdf">
                        <small class="text-muted">PDF, maks. 5MB — Kosongkan jika tidak ingin mengganti</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('audit.toe.index') }}" class="btn btn-secondary">Batal</a>
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
    const apiUrl             = "{{ url('audit/pka/hierarki-flat') }}";
    const selectedRisikoIds  = @json($selectedRisikoIds);
    const selectedKontrolIds = @json($selectedKontrolIds);
    const originalPid        = '{{ $item->perencanaan_audit_id }}';

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
                renderHierarki(data.risiko, usePreselect, usePreselect ? selectedRisikoIds : [], usePreselect ? selectedKontrolIds : []);
            })
            .catch(() => {
                hierarkiContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat data risiko.</div>`;
            });
    }

    function renderHierarki(risikoList, usePreselect, preRisiko, preKontrol) {
        let html = `<div class="list-group">`;
        risikoList.forEach((risiko, i) => {
            const rId = `risiko-${risiko.id}`;
            const isChecked = usePreselect ? preRisiko.includes(risiko.id) : true;
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
                    const isKontrolChecked = usePreselect ? preKontrol.includes(k.id) : true;
                    html += `<div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="pka_kontrol_ids[]"
                               value="${k.id}" id="kontrol-${k.id}" ${isKontrolChecked?'checked':''}>
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
        loadHierarki($(this).val());
    });

    // ── Init saat halaman dimuat ──────────────────────────────────────────
    const initPid = $('#perencanaan_audit_id').val();
    if (initPid) {
        loadHierarki(initPid);
    }
});
</script>
@endsection