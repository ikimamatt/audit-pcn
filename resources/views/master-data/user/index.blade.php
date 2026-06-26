@extends('layouts.vertical', ['title' => 'Master User'])

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
.btn-act-edit     { background: #fef3c7; color: #d97706; }
.btn-act-edit:hover   { background: #fde68a; color: #b45309; }
.btn-act-delete   { background: #fee2e2; color: #dc2626; }
.btn-act-delete:hover { background: #fecaca; color: #b91c1c; }
.btn-act-reset    { background: #e0f2fe; color: #0369a1; }
.btn-act-reset:hover  { background: #bae6fd; color: #0284c7; }

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
<div class="em-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="mdi mdi-account-circle-outline" style="font-size:1.4rem; color:#2d6a9f;"></i>
                <h2 class="mb-0">Master User</h2>
            </div>
            <div class="subtitle">
                <i class="mdi mdi-home-outline me-1"></i>Home &rsaquo; Master Data &rsaquo; User
            </div>
        </div>
        @canModifyData
        <a href="{{ route('master.user.create') }}" class="btn-add-em">
            <i class="mdi mdi-plus-circle"></i> Tambah User
        </a>
        @endcanModifyData
    </div>
</div>

@include('components.alert')

{{-- ===== TABLE ===== --}}
@php $total = $data->count(); @endphp
<div class="card table-card">
    <div class="card-header-custom d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-table-search" style="color:#2d6a9f;font-size:1.2rem;"></i>
            <h5 class="mb-0">Daftar User</h5>
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
                        <th>Nama</th>
                        <th>Username</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th>No. Telpon</th>
                        <th>Jabatan</th>
                        <th>Bidang</th>
                        <th>Area</th>
                        <th>Akses</th>
                        <th style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td><span class="row-num">{{ $index + 1 }}</span></td>
                            <td class="fw-semibold text-dark">{{ $item->nama }}</td>
                            <td>{{ $item->username }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.8rem; font-weight:600; border-radius:6px;">
                                    {{ $item->nip }}
                                </span>
                            </td>
                            <td>{{ $item->email ?? '-' }}</td>
                            <td>{{ $item->no_telpon ?? '-' }}</td>
                            <td>{{ $item->jabatan ?? '-' }}</td>
                            <td>{{ $item->auditee->divisi ?? '-' }}</td>
                            <td>
                                @if($item->area)
                                    <span class="badge bg-info-subtle text-info px-2 py-1" style="font-size:0.75rem; border-radius:6px;">
                                        [{{ $item->area->kd_area }}] {{ $item->area->nama_area }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->akses)
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1" style="font-size:0.75rem; border-radius:6px;">
                                        {{ str_contains(strtoupper($item->akses->nama_akses), 'VIEW BOD') ? 'VIEW BOD/BOC' : $item->akses->nama_akses }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-wrap">
                                    <button type="button" 
                                            class="btn-act btn-act-reset" 
                                            onclick="openResetModal({{ $item->id }}, '{{ addslashes($item->nama) }}')" 
                                            title="Reset Password">
                                        <i class="mdi mdi-key-variant"></i>
                                    </button>
                                    <a href="{{ route('master.user.edit', $item->id) }}" 
                                       class="btn-act btn-act-edit" 
                                       title="Edit User">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('master.user.destroy', $item->id) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn-act btn-act-delete"
                                                onclick="deleteData('{{ $item->id }}')"
                                                title="Hapus User">
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
                    <i class="mdi mdi-account-circle-outline"></i>
                    <p class="mb-0 fw-semibold">Belum ada data User</p>
                    <p class="mb-0" style="font-size:.82rem;">Klik tombol <strong>Tambah User</strong> untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== MODAL RESET PASSWORD ===== --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0;">
                <h5 class="modal-title fw-bold text-dark" id="resetPasswordModalLabel" style="font-size: 1.15rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="mdi mdi-key-variant text-primary" style="font-size: 1.4rem;"></i>
                    Reset Password User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reset-password-form" method="POST" action="">
                @csrf
                <div class="modal-body" style="padding: 20px 24px;">
                    <p class="text-muted mb-3" style="font-size: 0.85rem;">
                        Anda akan mereset password untuk user <strong id="reset-user-name" class="text-dark"></strong>. Silakan masukkan password baru di bawah ini.
                    </p>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.82rem;">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px;"><i class="mdi mdi-lock-outline"></i></span>
                            <input type="password" 
                                   name="password" 
                                   id="new_password" 
                                   class="form-control border-start-0" 
                                   style="border-radius: 0 8px 8px 0; font-size: 0.875rem;" 
                                   placeholder="Minimal 6 karakter" 
                                   required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password')" style="border-radius: 0 8px 8px 0;">
                                <i class="mdi mdi-eye" id="new_password-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label for="new_password_confirmation" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.82rem;">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px;"><i class="mdi mdi-lock-outline"></i></span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="new_password_confirmation" 
                                   class="form-control border-start-0" 
                                   style="border-radius: 0 8px 8px 0; font-size: 0.875rem;" 
                                   placeholder="Ulangi password baru" 
                                   required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password_confirmation')" style="border-radius: 0 8px 8px 0;">
                                <i class="mdi mdi-eye" id="new_password_confirmation-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0" style="padding: 0 24px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem; padding: 8px 16px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem; padding: 8px 16px; background: #1a3a5c; border-color: #1a3a5c;">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openResetModal(userId, userName) {
            const form = document.getElementById('reset-password-form');
            form.action = `/master/user/${userId}/reset-password`;
            document.getElementById('reset-user-name').innerText = userName;
            
            // Clear inputs
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';
            
            // Reset eye icons and input types to password
            document.getElementById('new_password').type = 'password';
            document.getElementById('new_password_confirmation').type = 'password';
            document.getElementById('new_password-eye').className = 'mdi mdi-eye';
            document.getElementById('new_password_confirmation-eye').className = 'mdi mdi-eye';

            // Show Modal
            const myModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            myModal.show();
        }

        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            const eye = document.getElementById(id + '-eye');
            if (input.type === 'password') {
                input.type = 'text';
                eye.className = 'mdi mdi-eye-off';
            } else {
                input.type = 'password';
                eye.className = 'mdi mdi-eye';
            }
        }

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
    </script>
@endsection
