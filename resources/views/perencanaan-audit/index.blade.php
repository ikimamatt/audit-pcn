@extends('layouts.vertical', ['title' => 'Program Kerja Audit'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
        'node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'
     ])
<style>
/* ===== HERO HEADER ===== */
.pka-hero {
    background: #fff;
    border-radius: 16px;
    padding: 24px 28px;
    color: #1a3a5c;
    margin-bottom: 24px;
    border: 1px solid #e8edf5;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.pka-hero h2 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 4px;
    letter-spacing: -0.5px;
    color: #1a3a5c;
}
.pka-hero .subtitle {
    font-size: 0.85rem;
    color: #6b7a99;
}
.btn-add-pka {
    background: #1a3a5c;
    color: #fff;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    padding: 10px 22px;
    font-size: 0.9rem;
    box-shadow: 0 2px 10px rgba(26,58,92,0.18);
    transition: all .2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-add-pka:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(26,58,92,0.25);
    color: #fff;
    background: #2d6a9f;
}

/* ===== TABLE CARD ===== */
.table-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 2px 20px rgba(0,0,0,0.06);
    overflow: hidden;
}
.table-card .card-header-custom {
    background: #fff;
    padding: 20px 24px 0;
    border-bottom: 1px solid #f0f0f0;
}
.table-card .card-header-custom h5 {
    font-size: 1rem;
    font-weight: 700;
    color: #1a3a5c;
}

#responsive-datatable thead th {
    background: #f8fafd;
    color: #6b7a99;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 2px solid #e8edf5;
    padding: 13px 14px;
    white-space: nowrap;
}
#responsive-datatable tbody tr {
    transition: background .15s;
}
#responsive-datatable tbody tr:hover {
    background: #f4f8ff !important;
}
#responsive-datatable tbody td {
    padding: 13px 14px;
    vertical-align: middle;
    border-color: #f0f3f9;
    font-size: 0.875rem;
    color: #374151;
}

/* No baris */
.row-num {
    font-size: 0.78rem;
    font-weight: 700;
    color: #9ca3af;
    background: #f9fafb;
    border-radius: 6px;
    padding: 3px 8px;
    display: inline-block;
}

/* No surat tugas */
.no-surat {
    font-weight: 600;
    font-size: 0.82rem;
    color: #1a3a5c;
    background: #eef3fb;
    border-radius: 8px;
    padding: 5px 10px;
    display: inline-block;
    max-width: 200px;
    word-break: break-word;
    line-height: 1.4;
}

/* No PKA */
.no-pka-badge {
    font-weight: 700;
    font-size: 0.82rem;
    color: #6366f1;
    background: #eef2ff;
    border-radius: 8px;
    padding: 4px 10px;
    display: inline-block;
    white-space: nowrap;
}

/* Judul PKA */
.judul-pka {
    font-size: 0.85rem;
    font-weight: 600;
    color: #1f2937;
    max-width: 200px;
    line-height: 1.4;
}

/* Auditee chip */
.auditee-chip {
    font-size: 0.82rem;
    font-weight: 600;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 7px;
}
.auditee-chip .dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2d6a9f, #3a8dcc);
    flex-shrink: 0;
}

/* Tanggal PKA */
.tgl-pka {
    font-size: 0.8rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}

/* Action buttons */
.action-wrap {
    display: flex;
    gap: 5px;
    align-items: center;
}
.btn-act {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
    border: none;
    transition: all .2s;
    cursor: pointer;
    text-decoration: none;
    flex-shrink: 0;
}
.btn-act:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.15); }
.btn-act-view     { background: #e0f2fe; color: #0369a1; }
.btn-act-view:hover   { background: #bae6fd; color: #0284c7; }
.btn-act-edit     { background: #fef3c7; color: #d97706; }
.btn-act-edit:hover   { background: #fde68a; color: #b45309; }
.btn-act-download { background: #dcfce7; color: #16a34a; }
.btn-act-download:hover { background: #bbf7d0; color: #15803d; }
.btn-act-delete   { background: #fee2e2; color: #dc2626; }
.btn-act-delete:hover { background: #fecaca; color: #b91c1c; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}
.empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; opacity: .4; }
</style>
@endsection

@section('content')

{{-- ===== HERO HEADER ===== --}}
<div class="pka-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-file-document-edit-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Program Kerja Audit</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Program Kerja Audit
            </div>
        </div>
        @canModifyData
        <a href="{{ route('audit.pka.create') }}" class="btn-add-pka">
            <i class="mdi mdi-plus-circle"></i> Tambah PKA
        </a>
        @endcanModifyData
    </div>
</div>

{{-- ===== TABLE ===== --}}
@php $total = $data->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Program Kerja Audit</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} PKA
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-centered dt-responsive w-100 mb-0" id="responsive-datatable">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Surat Tugas</th>
                        <th>No PKA</th>
                        <th>Judul PKA</th>
                        <th>Auditee</th>
                        <th>Unit</th>
                        <th>Tanggal PKA</th>
                        <th>Status</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $i => $item)
                    <tr>
                        {{-- No --}}
                        <td><span class="row-num">{{ $i+1 }}</span></td>

                        {{-- Surat Tugas --}}
                        <td>
                            <div class="no-surat">
                                <i class="mdi mdi-file-document-outline me-1" style="color:#2d6a9f;"></i>{{ $item->perencanaanAudit->nomor_surat_tugas ?? '-' }}
                            </div>
                        </td>

                        {{-- No PKA --}}
                        <td>
                            <span class="no-pka-badge">{{ $item->no_pka }}</span>
                        </td>

                        {{-- Judul PKA --}}
                        <td>
                            <div class="judul-pka">{{ $item->judul_pka ?? '-' }}</div>
                        </td>

                        {{-- Auditee --}}
                        <td>
                            <div class="auditee-chip">
                                <span class="dot"></span>
                                {{ $item->perencanaanAudit->auditee->divisi ?? '-' }}
                            </div>
                        </td>

                        {{-- Unit --}}
                        <td>
                            <div class="auditee-chip">
                                <span class="dot" style="background:linear-gradient(135deg,#7c3aed,#a855f7);"></span>
                                {{ $item->perencanaanAudit->unit->nama_unit ?? '-' }}
                            </div>
                        </td>

                        {{-- Tanggal PKA --}}
                        <td>
                            <div class="tgl-pka">
                                <i class="mdi mdi-calendar-outline" style="color:#9ca3af;"></i>
                                {{ $item->tanggal_pka ? \Carbon\Carbon::parse($item->tanggal_pka)->format('d M Y') : '-' }}
                            </div>
                        </td>

                        {{-- Status Approval --}}
                        <td>
                            @if($item->status_approval == 'approved')
                                <span class="badge" style="background:#d1fae5;color:#065f46;font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:20px;"><i class="mdi mdi-check-circle me-1"></i>Approved (Final)</span>
                            @elseif($item->status_approval == 'approved_level1')
                                <span class="badge" style="background:#cff4fc;color:#055160;font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:20px;"><i class="mdi mdi-check me-1"></i>Approved Lvl 1</span>
                            @elseif($item->status_approval == 'rejected' || $item->status_approval == 'rejected_level1')
                                <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:20px;"><i class="mdi mdi-close-circle me-1"></i>Rejected</span>
                            @else
                                <span class="badge" style="background:#fef3c7;color:#92400e;font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:20px;"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td>
                            <div class="action-wrap">
                                <a href="{{ route('audit.pka.show', $item->id) }}"
                                   class="btn-act btn-act-view" title="Detail PKA">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="{{ route('audit.pka.edit', $item->id) }}"
                                   class="btn-act btn-act-edit" title="Edit PKA">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <a href="{{ route('audit.pka.download', $item->id) }}"
                                   class="btn-act btn-act-download" title="Download PKA">
                                    <i class="mdi mdi-download"></i>
                                </a>
                                <form action="{{ route('audit.pka.destroy', $item->id) }}"
                                      method="POST" class="delete-form m-0"
                                      id="delete-form-{{ $item->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        class="btn-act btn-act-delete btn-delete-swal"
                                        data-id="{{ $item->id }}"
                                        data-check-url="{{ route('audit.pka.check-relations', $item->id) }}"
                                        title="Hapus PKA">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($data->isEmpty())
            <div class="empty-state">
                <i class="mdi mdi-file-document-off-outline"></i>
                <p class="mb-0 fw-semibold">Belum ada data Program Kerja Audit</p>
                <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah PKA</strong> untuk memulai</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = btn.dataset.id;
                const checkUrl = btn.dataset.checkUrl;
                const form = document.getElementById('delete-form-' + id);

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(checkUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-delete"></i>';

                    let htmlContent = '';
                    if (data.has_relations) {
                        const relList = data.relations.map(r => `<li>${r}</li>`).join('');
                        htmlContent = `
                            <p class="mb-2">PKA <strong>${data.no_pka}</strong> (Surat Tugas: <strong>${data.surat_tugas}</strong>) memiliki data terkait yang akan <strong class="text-danger">ikut terhapus permanen</strong>:</p>
                            <ul class="text-start text-danger mb-2">${relList}</ul>
                            <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                        `;
                    } else {
                        htmlContent = `<p>Yakin ingin menghapus PKA <strong>${data.no_pka}</strong>? Tindakan ini tidak dapat dibatalkan.</p>`;
                    }

                    Swal.fire({
                        title: data.has_relations ? '⚠️ Peringatan! Data Terkait Akan Terhapus' : 'Hapus Data PKA?',
                        html: htmlContent,
                        icon: data.has_relations ? 'warning' : 'question',
                        confirmButtonText: 'Ya, Hapus Semua',
                        cancelButtonText: 'Batal',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-delete"></i>';
                    Swal.fire({
                        title: 'Hapus Data?',
                        text: 'Yakin ingin menghapus data PKA ini beserta seluruh proses audit terkait?',
                        icon: 'warning',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    });
</script>
@endsection