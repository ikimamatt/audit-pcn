@extends('layouts.vertical', ['title' => 'Master Bidang'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
<style>
/* ===== HERO HEADER ===== */
.em-hero {
    background: #fff;
    border-radius: 16px;
    padding: 24px 28px;
    color: #1a3a5c;
    margin-bottom: 24px;
    border: 1px solid #e8edf5;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.em-hero h2 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 4px;
    letter-spacing: -0.5px;
    color: #1a3a5c;
}
.em-hero .subtitle {
    font-size: 0.85rem;
    color: #6b7a99;
}
.btn-add-em {
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
.btn-add-em:hover {
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

/* Kode bidang badge */
.kd-badge {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1a3a5c;
    background: #eef3fb;
    border-radius: 6px;
    padding: 4px 10px;
    display: inline-block;
    font-family: 'Courier New', monospace;
}

/* Sub bidang count badge */
.sub-count-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-block;
}
.sub-count-badge.has-sub {
    color: #059669;
    background: #d1fae5;
}
.sub-count-badge.no-sub {
    color: #9ca3af;
    background: #f3f4f6;
}

/* UP badge */
.up-badge {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-block;
}
.up-badge.active { background: #d1fae5; color: #059669; }
.up-badge.inactive { background: #fee2e2; color: #dc2626; }

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
.btn-act-sub      { background: #d1fae5; color: #059669; }
.btn-act-sub:hover    { background: #a7f3d0; color: #047857; }
.btn-act-edit     { background: #fef3c7; color: #d97706; }
.btn-act-edit:hover   { background: #fde68a; color: #b45309; }
.btn-act-delete   { background: #fee2e2; color: #dc2626; }
.btn-act-delete:hover { background: #fecaca; color: #b91c1c; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}
.empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; opacity: .4; }

/* ===== OFFCANVAS SUB BIDANG ===== */
#offcanvasSubBidang {
    width: 460px;
    border-left: 3px solid #2d6a9f;
}
#offcanvasSubBidang .offcanvas-header {
    background: #f8fafd;
    border-bottom: 1px solid #e8edf5;
    padding: 16px 20px;
}
#offcanvasSubBidang .offcanvas-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a3a5c;
}
.sub-bidang-form {
    background: #f8fafd;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 16px;
    border: 1px dashed #d1d5db;
}
.sub-bidang-form .form-control {
    border-radius: 8px;
    font-size: 0.85rem;
}
.sub-bidang-list .list-group-item {
    border: 1px solid #e8edf5;
    border-radius: 10px !important;
    margin-bottom: 6px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all .15s;
}
.sub-bidang-list .list-group-item:hover {
    background: #f4f8ff;
    border-color: #c7d4e8;
}
.sub-bidang-list .sub-nama {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}
.sub-bidang-list .sub-actions {
    display: flex;
    gap: 4px;
}
.sub-bidang-list .btn-sub-act {
    width: 28px; height: 28px;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem;
    border: none;
    cursor: pointer;
    transition: all .15s;
}
.sub-bidang-list .btn-sub-edit { background: #fef3c7; color: #d97706; }
.sub-bidang-list .btn-sub-edit:hover { background: #fde68a; color: #b45309; }
.sub-bidang-list .btn-sub-delete { background: #fee2e2; color: #dc2626; }
.sub-bidang-list .btn-sub-delete:hover { background: #fecaca; color: #b91c1c; }
.sub-empty {
    text-align: center;
    padding: 30px 20px;
    color: #9ca3af;
    font-size: 0.85rem;
}
.sub-empty i { font-size: 2rem; display: block; margin-bottom: 8px; opacity: .3; }

/* Inline edit in offcanvas */
.sub-edit-input {
    border: 2px solid #2d6a9f;
    border-radius: 8px;
    font-size: 0.85rem;
    padding: 4px 10px;
    flex: 1;
}
.btn-sub-save { background: #d1fae5; color: #059669; }
.btn-sub-save:hover { background: #a7f3d0; color: #047857; }
.btn-sub-cancel { background: #f3f4f6; color: #6b7280; }
.btn-sub-cancel:hover { background: #e5e7eb; color: #374151; }
</style>
@endsection

@section('content')

{{-- ===== HERO HEADER ===== --}}
<div class="em-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-domain" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Master Bidang</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Master Data &rsaquo; Bidang
            </div>
        </div>
        <a href="{{ route('master.auditee.create') }}" class="btn-add-em">
            <i class="mdi mdi-plus-circle"></i> Tambah Bidang
        </a>
    </div>
</div>

@include('components.alert')

{{-- ===== TABLE ===== --}}
@php $total = $data->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar Bidang</h5>
        </div>
        <span class="badge" style="background:#eef3fb;color:#2d6a9f;font-size:0.78rem;font-weight:600;padding:6px 12px;border-radius:20px;">
            {{ $total }} Data
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="responsive-datatable" class="table table-centered dt-responsive w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="width:70px;">Kode</th>
                        <th>Nama Bidang</th>
                        <th style="width:90px;">Status UP</th>
                        <th style="width:100px;">Sub Bidang</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td><span class="row-num">{{ $index + 1 }}</span></td>
                            <td><span class="kd-badge">{{ $item->kd_bidang }}</span></td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->nama_bidang }}</span>
                            </td>
                            <td>
                                @if($item->is_available_for_up)
                                    <span class="up-badge active"><i class="mdi mdi-check-circle me-1"></i>Aktif</span>
                                @else
                                    <span class="up-badge inactive"><i class="mdi mdi-close-circle me-1"></i>Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="sub-count-badge {{ $item->sub_bidang_count > 0 ? 'has-sub' : 'no-sub' }}">
                                    {{ $item->sub_bidang_count }} sub
                                </span>
                            </td>
                            <td>
                                <div class="action-wrap">
                                    <button type="button"
                                            class="btn-act btn-act-sub"
                                            onclick="openSubBidang({{ $item->id }}, '{{ addslashes($item->nama_bidang) }}')"
                                            title="Kelola Sub Bidang">
                                        <i class="mdi mdi-file-tree"></i>
                                    </button>
                                    <a href="{{ route('master.auditee.edit', $item->id) }}"
                                       class="btn-act btn-act-edit"
                                       title="Edit Bidang">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('master.auditee.destroy', $item->id) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn-act btn-act-delete"
                                                onclick="deleteData('{{ $item->id }}')"
                                                title="Hapus Bidang">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty state handled below --}}
                    @endforelse
                </tbody>
            </table>
            @if($data->isEmpty())
                <div class="empty-state">
                    <i class="mdi mdi-domain"></i>
                    <p class="mb-0 fw-semibold">Belum ada data Bidang</p>
                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah Bidang</strong> untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== OFFCANVAS SUB BIDANG ===== --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSubBidang" aria-labelledby="offcanvasSubBidangLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSubBidangLabel">
            <i class="mdi mdi-file-tree me-2" style="color:#2d6a9f;"></i>
            Sub Bidang: <span id="subBidangParentName">-</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {{-- Form tambah sub bidang --}}
        <div class="sub-bidang-form">
            <div class="d-flex gap-2">
                <input type="text" id="inputSubBidangNama" class="form-control" placeholder="Nama sub bidang baru..." maxlength="255">
                <button type="button" class="btn btn-sm btn-primary px-3" onclick="storeSubBidang()" style="border-radius:8px; white-space:nowrap;">
                    <i class="mdi mdi-plus"></i> Tambah
                </button>
            </div>
        </div>

        {{-- Loading --}}
        <div id="subBidangLoading" class="text-center py-4" style="display:none;">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2 text-muted" style="font-size:0.85rem;">Memuat data...</span>
        </div>

        {{-- List sub bidang --}}
        <div id="subBidangListContainer">
            <div class="sub-bidang-list" id="subBidangList">
                {{-- Diisi via JS --}}
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ===== CSRF Token =====
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // ===== State =====
        let currentBidangId = null;
        let currentBidangName = '';
        let offcanvasInstance = null;

        // ===== Delete Bidang =====
        function deleteData(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        // ===== Open Sub Bidang Offcanvas =====
        function openSubBidang(bidangId, bidangName) {
            currentBidangId = bidangId;
            currentBidangName = bidangName;
            document.getElementById('subBidangParentName').textContent = bidangName;
            document.getElementById('inputSubBidangNama').value = '';

            // Init offcanvas
            const offcanvasEl = document.getElementById('offcanvasSubBidang');
            offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvasInstance.show();

            // Load sub bidang data
            loadSubBidang();
        }

        // ===== Load Sub Bidang via AJAX =====
        async function loadSubBidang() {
            const loading = document.getElementById('subBidangLoading');
            const listContainer = document.getElementById('subBidangList');

            loading.style.display = 'block';
            listContainer.innerHTML = '';

            try {
                const response = await fetch(`/master/auditee/${currentBidangId}/sub-bidang`, {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();

                loading.style.display = 'none';

                if (result.success && result.data.length > 0) {
                    listContainer.innerHTML = result.data.map((item, idx) => renderSubBidangItem(item, idx)).join('');
                } else {
                    listContainer.innerHTML = `
                        <div class="sub-empty">
                            <i class="mdi mdi-file-tree-outline"></i>
                            <p class="mb-0">Belum ada sub bidang</p>
                            <p class="mb-0" style="font-size:0.78rem;">Gunakan form di atas untuk menambahkan</p>
                        </div>
                    `;
                }
            } catch (error) {
                loading.style.display = 'none';
                listContainer.innerHTML = '<div class="text-center text-danger py-3"><i class="mdi mdi-alert-circle me-1"></i>Gagal memuat data</div>';
                console.error('Error loading sub bidang:', error);
            }
        }

        // ===== Render Sub Bidang Item =====
        function renderSubBidangItem(item, idx) {
            return `
                <div class="list-group-item" id="sub-item-${item.id}">
                    <div class="d-flex align-items-center gap-2">
                        <span class="row-num">${idx + 1}</span>
                        <span class="sub-nama" id="sub-nama-${item.id}">${escapeHtml(item.nama)}</span>
                    </div>
                    <div class="sub-actions" id="sub-actions-${item.id}">
                        <button class="btn-sub-act btn-sub-edit" onclick="editSubBidang(${item.id}, '${escapeJs(item.nama)}')" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button class="btn-sub-act btn-sub-delete" onclick="deleteSubBidang(${item.id}, '${escapeJs(item.nama)}')" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        // ===== Store Sub Bidang =====
        async function storeSubBidang() {
            const input = document.getElementById('inputSubBidangNama');
            const nama = input.value.trim();

            if (!nama) {
                input.classList.add('is-invalid');
                input.focus();
                return;
            }
            input.classList.remove('is-invalid');

            try {
                const response = await fetch('{{ route("master.sub-bidang.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        nama: nama,
                        master_bidang_id: currentBidangId,
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    input.value = '';
                    loadSubBidang();
                    updateSubBidangCount(currentBidangId, 1);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Gagal menambahkan sub bidang.' });
                }
            } catch (error) {
                console.error('Error storing sub bidang:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan pada server.' });
            }
        }

        // ===== Edit Sub Bidang (Inline) =====
        function editSubBidang(id, currentNama) {
            const namaEl = document.getElementById(`sub-nama-${id}`);
            const actionsEl = document.getElementById(`sub-actions-${id}`);

            // Replace with inline input
            namaEl.outerHTML = `
                <input type="text" class="sub-edit-input" id="sub-edit-input-${id}" value="${escapeHtml(currentNama)}" maxlength="255">
            `;
            actionsEl.innerHTML = `
                <button class="btn-sub-act btn-sub-save" onclick="saveSubBidang(${id})" title="Simpan">
                    <i class="mdi mdi-check"></i>
                </button>
                <button class="btn-sub-act btn-sub-cancel" onclick="loadSubBidang()" title="Batal">
                    <i class="mdi mdi-close"></i>
                </button>
            `;

            // Focus & select input
            const editInput = document.getElementById(`sub-edit-input-${id}`);
            editInput.focus();
            editInput.select();

            // Enter key to save
            editInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') saveSubBidang(id);
                if (e.key === 'Escape') loadSubBidang();
            });
        }

        // ===== Save Sub Bidang (Update) =====
        async function saveSubBidang(id) {
            const input = document.getElementById(`sub-edit-input-${id}`);
            const nama = input.value.trim();

            if (!nama) {
                input.classList.add('is-invalid');
                input.focus();
                return;
            }

            try {
                const response = await fetch(`/master/sub-bidang/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ nama: nama }),
                });

                const result = await response.json();

                if (result.success) {
                    loadSubBidang();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Gagal memperbarui sub bidang.' });
                }
            } catch (error) {
                console.error('Error updating sub bidang:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan pada server.' });
            }
        }

        // ===== Delete Sub Bidang =====
        function deleteSubBidang(id, nama) {
            Swal.fire({
                title: 'Hapus Sub Bidang?',
                text: `Sub bidang "${nama}" akan dihapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/master/sub-bidang/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        });

                        const data = await response.json();

                        if (data.success) {
                            loadSubBidang();
                            updateSubBidangCount(currentBidangId, -1);
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Gagal menghapus sub bidang.' });
                        }
                    } catch (error) {
                        console.error('Error deleting sub bidang:', error);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan pada server.' });
                    }
                }
            });
        }

        // ===== Update Sub Bidang Count in Table =====
        function updateSubBidangCount(bidangId, delta) {
            // Find the table row and update the sub bidang count badge
            document.querySelectorAll('tr').forEach(row => {
                const deleteForm = row.querySelector(`#delete-form-${bidangId}`);
                if (deleteForm) {
                    const badge = row.querySelector('.sub-count-badge');
                    if (badge) {
                        const currentCount = parseInt(badge.textContent) || 0;
                        const newCount = currentCount + delta;
                        badge.textContent = newCount + ' sub';
                        badge.className = 'sub-count-badge ' + (newCount > 0 ? 'has-sub' : 'no-sub');
                    }
                }
            });
        }

        // ===== Utility Functions =====
        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function escapeJs(str) {
            return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        // Allow Enter key on add input
        document.addEventListener('DOMContentLoaded', function() {
            const addInput = document.getElementById('inputSubBidangNama');
            if (addInput) {
                addInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') storeSubBidang();
                });
            }
        });
    </script>
@endsection
