@extends('layouts.vertical', ['title' => 'Persetujuan Dokumen'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
    <style>
        .btn-custom {
            transition: all 0.3s ease;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transition: all 0.2s ease;
        }

        .status-badge {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .stat-card {
            border-radius: 12px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .bg-gradient-primary-soft {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 86, 179, 0.05));
            border-left: 5px solid #007bff;
        }

        .bg-gradient-warning-soft {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(224, 168, 0, 0.05));
            border-left: 5px solid #ffc107;
        }

        .bg-gradient-info-soft {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(17, 122, 139, 0.05));
            border-left: 5px solid #1724ab;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Audit</a></li>
                    <li class="breadcrumb-item active">Persetujuan Dokumen</li>
                </ol>
            </div>
            <h4 class="page-title">
                <i class="mdi mdi-check-circle-outline text-success me-2"></i>
                Persetujuan Dokumen (Pending Approvals)
            </h4>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card bg-gradient-primary-soft">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fs-12 font-weight-bold mb-1">Total Menunggu Tindakan</h6>
                        <h3 class="fw-bold mb-0 text-primary">{{ $allPendingItems->count() }}</h3>
                    </div>
                    <div class="avatar-lg bg-primary-soft rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-file-document-multiple-outline fs-32 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card bg-gradient-warning-soft">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fs-12 font-weight-bold mb-1">Menunggu Level 1 (Ketua Tim)</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $allPendingItems->where('status_approval', 'pending')->count() }}</h3>
                    </div>
                    <div class="avatar-lg bg-warning-soft rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-account-clock-outline fs-32 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card bg-gradient-info-soft">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fs-12 font-weight-bold mb-1">Menunggu Level 2 (Koordinator)</h6>
                        <h3 class="fw-bold mb-0 text-info" style="color: #1724ab !important;">{{ $allPendingItems->where('status_approval', 'approved_level1')->count() }}</h3>
                    </div>
                    <div class="avatar-lg bg-info-soft rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-shield-account-outline fs-32 text-info" style="color: #1724ab !important;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-table me-2 text-primary"></i>
                            Daftar Pengajuan Menunggu Persetujuan Anda
                        </h5>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    @include('components.alert')
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Filter Form -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="filter-doc-type" class="form-label font-weight-bold">Tipe Dokumen</label>
                        <select id="filter-doc-type" class="form-select">
                            <option value="">Semua Dokumen</option>
                            <option value="Program Kerja Audit (PKA)">Program Kerja Audit (PKA)</option>
                            <option value="Entry Meeting">Entry Meeting</option>
                            <option value="Walkthrough Audit">Walkthrough Audit</option>
                            <option value="TOD BPM Audit">TOD BPM Audit</option>
                            <option value="TOE Audit">TOE Audit</option>
                            <option value="Exit Meeting">Exit Meeting</option>
                            <option value="Pelaporan Hasil Audit (LHA/LHK)">Pelaporan Hasil Audit (LHA/LHK)</option>
                            <option value="Pemantauan Hasil Audit">Pemantauan Hasil Audit</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filter-level" class="form-label font-weight-bold">Tingkat Approval</label>
                        <select id="filter-level" class="form-select">
                            <option value="">Semua Tingkat</option>
                            <option value="Level 1">Level 1 (Ketua Tim)</option>
                            <option value="Level 2">Level 2 (Koordinator)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary w-100" style="border-radius: 20px;">
                            <i class="mdi mdi-refresh me-2"></i> Reset Filter
                        </button>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="persetujuanTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="180">Surat Tugas</th>
                                <th width="150">Auditee / Divisi</th>
                                <th width="200">Jenis</th>
                                <th>Keterangan / Judul</th>
                                <th width="180" class="text-center">Tingkat Approval</th>
                                <th width="130" class="text-center">Tanggal Pengajuan</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allPendingItems as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item['nomor_surat_tugas'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary status-badge">{{ $item['auditee_name'] }}</span>
                                    </td>
                                    <td><span class="font-weight-bold text-dark">{{ $item['document_name'] }}</span></td>
                                    <td>
                                        <span class="text-muted">{{ $item['title'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item['status_approval'] === 'pending')
                                            <span class="badge bg-warning text-dark status-badge">
                                                <i class="mdi mdi-account-clock-outline me-1"></i>
                                                {{ $item['approval_level'] }}
                                            </span>
                                        @else
                                            <span class="badge bg-info text-white status-badge" style="background-color: #1724ab !important;">
                                                <i class="mdi mdi-shield-account-outline me-1"></i>
                                                {{ $item['approval_level'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($item['date'])->locale('id')->translatedFormat('d M Y') }}
                                            <br>
                                            {{ \Carbon\Carbon::parse($item['date'])->format('H:i') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column gap-1 justify-content-center align-items-center">
                                            <a href="{{ $item['detail_url'] }}" class="btn btn-outline-primary btn-sm w-100 btn-custom" title="Lihat Detail Halaman Dokumen">
                                                <i class="mdi mdi-eye me-1"></i> Detail
                                            </a>
                                            
                                            @if($item['can_approve'])
                                                <button type="button" class="btn btn-success btn-sm w-100 btn-custom" 
                                                        onclick="approveItem('{{ $item['model_type'] }}', {{ $item['id'] }}, '{{ $item['document_name'] }}')">
                                                    <i class="mdi mdi-check-bold me-1"></i> Approve
                                                </button>
                                            @endif

                                            @if($item['can_reject'])
                                                <button type="button" class="btn btn-danger btn-sm w-100 btn-custom" 
                                                        onclick="rejectItem('{{ $item['model_type'] }}', {{ $item['id'] }}, '{{ $item['document_name'] }}')">
                                                    <i class="mdi mdi-close-thick me-1"></i> Reject
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($allPendingItems->isEmpty())
                        <div class="text-center py-4 border-top">
                            <i class="mdi mdi-check-all text-success fs-36"></i>
                            <p class="mt-2 mb-0 font-weight-bold text-muted">Hebat! Tidak ada persetujuan yang tertunda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forms & Modals for Quick Approval / Rejection -->
<form id="quick-approve-form" action="{{ route('audit.persetujuan.proses') }}" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="model_type" id="approve-model-type">
    <input type="hidden" name="id" id="approve-id">
    <input type="hidden" name="action" value="approve">
</form>

<div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="quick-reject-form" action="{{ route('audit.persetujuan.proses') }}" method="POST">
                @csrf
                <input type="hidden" name="model_type" id="reject-model-type">
                <input type="hidden" name="id" id="reject-id">
                <input type="hidden" name="action" value="reject">
                
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectionModalLabel">
                        <i class="mdi mdi-close-circle-outline me-2"></i>
                        Penolakan Dokumen
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" id="rejection-doc-info"></p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label font-weight-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan (minimal 10 karakter)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Dokumen</button>
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
    window.addEventListener('load', function() {
        const table = $('#persetujuanTable').DataTable({
            responsive: false,
            pageLength: 25,
            order: [[6, 'desc']], // Order by Tanggal Pengajuan descending
            columnDefs: [
                { orderable: false, targets: [7] } // Action column is not orderable
            ]
        });

        // Doc type filter
        $('#filter-doc-type').on('change', function() {
            const val = $(this).val();
            table.column(3).search(val ? `^${val}$` : '', true, false).draw();
        });

        // Level filter
        $('#filter-level').on('change', function() {
            const val = $(this).val();
            table.column(5).search(val ? val : '', false, false).draw();
        });

        // Reset filter button
        $('#btn-reset-filters').on('click', function() {
            $('#filter-doc-type').val('');
            $('#filter-level').val('');
            table.search('').columns().search('').draw();
        });

        // Auto-hide alert
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    function approveItem(modelType, id, docName) {
        Swal.fire({
            title: 'Konfirmasi Approval',
            text: `Apakah Anda yakin ingin menyetujui ${docName} ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Memproses Approval...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $('#approve-model-type').val(modelType);
                $('#approve-id').val(id);
                $('#quick-approve-form').submit();
            }
        });
    }

    function rejectItem(modelType, id, docName) {
        $('#reject-model-type').val(modelType);
        $('#reject-id').val(id);
        $('#rejection_reason').val('');
        $('#rejection-doc-info').text(`Dokumen: ${docName}`);
        
        // Show modal
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectionModal'));
        rejectModal.show();
    }
</script>
@endsection
