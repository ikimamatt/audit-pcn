@extends('layouts.vertical', ['title' => 'Pelaporan Hasil Audit'])

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
        
        .btn-outline-primary:hover {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border-color: #007bff;
            transform: translateY(-1px);
        }
        
        .btn-outline-success:hover {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border-color: #28a745;
            transform: translateY(-1px);
        }
        
        .btn-outline-warning:hover {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            border-color: #ffc107;
            transform: translateY(-1px);
        }
        
        .btn-outline-danger:hover {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border-color: #dc3545;
            transform: translateY(-1px);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .status-badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .iss-count-badge {
            background: linear-gradient(45deg, #6c757d, #495057);
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .iss-count-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .iss-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }

        .iss-item:last-child {
            margin-bottom: 0;
        }

        .iss-header {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 10px 15px;
            margin: -15px -15px 15px -15px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .iss-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .iss-status {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 12px;
            background: rgba(255,255,255,0.2);
        }

        .field-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .field-value {
            color: #212529;
            margin-bottom: 15px;
        }

        .field-group {
            margin-bottom: 20px;
        }

        .significance-badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 12px;
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
                    <li class="breadcrumb-item active">Pelaporan Hasil Audit</li>
                </ol>
            </div>
            <h4 class="page-title">
                <i class="mdi mdi-file-document-outline me-2"></i>
                Pelaporan Hasil Audit
            </h4>
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
                            <i class="mdi mdi-table me-2"></i>
                            Data Pelaporan Hasil Audit
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <button id="testEditIssBtn" class="btn btn-warning" style="border-radius: 25px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <i class="mdi mdi-pencil me-2"></i>
                                Test Edit ISS
                            </button>
                            <a href="{{ route('audit.pelaporan-hasil-audit.create') }}" class="btn btn-primary" style="border-radius: 25px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <i class="mdi mdi-plus-circle me-2"></i>
                                Tambah Pelaporan
                            </a>
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle nav-user" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <span class="pro-user-name">
                                        Profile <i class="mdi mdi-chevron-down"></i>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                                        <span>My Account</span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);" class="dropdown-item notify-item" id="logout-link-card">
                                        <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    @include('components.alert')
                @endif

                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('audit.pelaporan-hasil-audit.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="jenis_lha_lhk" class="form-label">Jenis LHA/LHK</label>
                                <select name="jenis_lha_lhk" id="jenis_lha_lhk" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="LHA" {{ request('jenis_lha_lhk') == 'LHA' ? 'selected' : '' }}>LHA</option>
                                    <option value="LHK" {{ request('jenis_lha_lhk') == 'LHK' ? 'selected' : '' }}>LHK</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="po_audit_konsul" class="form-label">PO/Konsul</label>
                                <select name="po_audit_konsul" id="po_audit_konsul" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="PO AUDIT" {{ request('po_audit_konsul') == 'PO AUDIT' ? 'selected' : '' }}>PO AUDIT</option>
                                    <option value="KONSUL" {{ request('po_audit_konsul') == 'KONSUL' ? 'selected' : '' }}>KONSUL</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status_approval" class="form-label">Status Approval</label>
                                <select name="status_approval" id="status_approval" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status_approval') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved_level1" {{ request('status_approval') == 'approved_level1' ? 'selected' : '' }}>Approved Level 1</option>
                                    <option value="approved" {{ request('status_approval') == 'approved' ? 'selected' : '' }}>Approved (Final)</option>
                                    <option value="rejected_level1" {{ request('status_approval') == 'rejected_level1' ? 'selected' : '' }}>Rejected Level 1</option>
                                    <option value="rejected" {{ request('status_approval') == 'rejected' ? 'selected' : '' }}>Rejected (Final)</option>
                                </select>
                            </div>
                                                                <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" style="border-radius: 25px; font-weight: 500;">
                                                <i class="mdi mdi-magnify me-2"></i>
                                                Filter Data
                                            </button>
                                            <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-outline-secondary" style="border-radius: 25px; font-weight: 500;">
                                                <i class="mdi mdi-refresh me-2"></i>
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="pelaporanTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">No</th>
                                <th>Surat Tugas</th>
                                <th>Nomor LHA/LHK</th>
                                <th width="80">Jenis</th>
                                <th width="100">PO/Konsul</th>
                                <th width="100">Kode SPI</th>
                                <th width="80">ISS</th>
                                <th width="100">Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->perencanaanAudit->nomor_surat_tugas ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $item->nomor_lha_lhk }}</strong>
                                        <br>
                                        <small class="text-muted">Urut: {{ $item->nomor_urut }} | Tahun: {{ $item->tahun }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->jenis_lha_lhk == 'LHA' ? 'bg-primary' : 'bg-info' }} status-badge">
                                            {{ $item->jenis_lha_lhk }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->po_audit_konsul == 'PO AUDIT' ? 'bg-success' : 'bg-warning' }} status-badge">
                                            {{ $item->po_audit_konsul }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <code>{{ $item->kode_spi }}</code>
                                    </td>
                                    <td class="text-center">
                                        @if($item->temuan && $item->temuan->count() > 0)
                                            <span class="iss-count-badge" onclick="showIssModal({{ $item->id }}, '{{ $item->nomor_lha_lhk }}')" title="Klik untuk lihat detail ISS">
                                                {{ $item->temuan->count() }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusApprovalClass = '';
                                            $statusApprovalText = '';
                                            switch($item->status_approval) {
                                                case 'approved':
                                                    $statusApprovalClass = 'bg-success';
                                                    $statusApprovalText = 'Approved (Final)';
                                                    break;
                                                case 'approved_level1':
                                                    $statusApprovalClass = 'bg-info';
                                                    $statusApprovalText = 'Approved Level 1';
                                                    break;
                                                case 'rejected':
                                                    $statusApprovalClass = 'bg-danger';
                                                    $statusApprovalText = 'Rejected (Final)';
                                                    break;
                                                case 'rejected_level1':
                                                    $statusApprovalClass = 'bg-warning';
                                                    $statusApprovalText = 'Rejected Level 1';
                                                    break;
                                                default:
                                                    $statusApprovalClass = 'bg-secondary';
                                                    $statusApprovalText = 'Pending';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusApprovalClass }} status-badge">{{ $statusApprovalText }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            <a href="{{ route('audit.pelaporan-hasil-audit.edit', $item->id) }}" 
                                               class="btn btn-outline-primary btn-sm mb-1 btn-custom" 
                                               title="Edit">
                                                <i class="mdi mdi-pencil me-1"></i>Edit
                                            </a>
                                            
                                            @canApproveReject
                                                @if($item->status_approval == 'pending')
                                                    {{-- Level 1: ASMAN KSPI can approve/reject --}}
                                                    @isAsmanKspi
                                                        <form id="approval-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                            @csrf
                                                            <button type="button" class="btn btn-sm btn-success mb-1 btn-custom" onclick="approveData({{ $item->id }})">
                                                                <i class="mdi mdi-check me-1"></i> Approve Level 1
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-secondary mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                                <i class="mdi mdi-close me-1"></i> Reject Level 1
                                                            </button>
                                                            <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                        </form>
                                                    @endisAsmanKspi
                                                    {{-- Level 2: KSPI can only reject from pending (must wait for ASMAN KSPI approval first) --}}
                                                    @isKspi
                                                        <form id="approval-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                            @csrf
                                                            <button type="button" class="btn btn-sm btn-success mb-1 btn-custom" onclick="approveDataPending({{ $item->id }})" title="Data harus diapprove oleh ASMAN KSPI terlebih dahulu">
                                                                <i class="mdi mdi-check me-1"></i> Approve Level 2
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                                <i class="mdi mdi-close me-1"></i> Reject Level 2
                                                            </button>
                                                            <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                        </form>
                                                    @endisKspi
                                                @elseif($item->status_approval == 'approved_level1')
                                                    {{-- Level 2: KSPI can approve/reject after level 1 --}}
                                                    @isKspi
                                                        <form id="approval-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                            @csrf
                                                            <button type="button" class="btn btn-sm btn-success mb-1 btn-custom" onclick="approveData({{ $item->id }})">
                                                                <i class="mdi mdi-check me-1"></i> Approve Level 2
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-secondary mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                                <i class="mdi mdi-close me-1"></i> Reject Level 2
                                                            </button>
                                                            <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                        </form>
                                                    @endisKspi
                                                @elseif($item->status_approval == 'rejected_level1')
                                                    {{-- Level 2: KSPI can reject after ASMAN KSPI reject (berjenjang) --}}
                                                    @isKspi
                                                        <form id="approval-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.approval', $item->id) }}" method="POST" style="display:inline-block">
                                                            @csrf
                                                            <button type="button" class="btn btn-sm btn-danger mb-1 btn-custom" onclick="rejectData({{ $item->id }})">
                                                                <i class="mdi mdi-close me-1"></i> Reject Level 2
                                                            </button>
                                                            <input type="hidden" name="action" id="action-{{ $item->id }}" value="">
                                                        </form>
                                                    @endisKspi
                                                @endif
                                            @endcanApproveReject
                                            
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm btn-custom" 
                                                    title="Hapus"
                                                    onclick="deleteData({{ $item->id }})">
                                                <i class="mdi mdi-delete me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Detail ISS -->
<div class="modal fade" id="issModal" tabindex="-1" aria-labelledby="issModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="issModalLabel">
                    <i class="mdi mdi-file-document-outline me-2"></i>
                    Detail ISS - <span id="modalLhaLhkTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="issModalBody">
                <!-- ISS details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Edit ISS -->
<div class="modal fade" id="editIssModal" tabindex="-1" aria-labelledby="editIssModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editIssModalLabel">
                    <i class="mdi mdi-pencil me-2"></i>
                    Edit ISS
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editIssForm">
                    <input type="hidden" id="editTemuanId" name="temuan_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editHasilTemuan" class="form-label">Hasil Temuan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="editHasilTemuan" name="hasil_temuan" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editPermasalahan" class="form-label">Permasalahan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="editPermasalahan" name="permasalahan" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editKodeAoi" class="form-label">Kode AOI <span class="text-danger">*</span></label>
                                <select class="form-select" id="editKodeAoi" name="kode_aoi_id" required>
                                    <option value="">Pilih Kode AOI</option>
                                    @foreach($kodeAoi as $aoi)
                                        <option value="{{ $aoi->id }}">{{ $aoi->kode_area_of_improvement }} - {{ $aoi->deskripsi_area_of_improvement }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editKodeRisk" class="form-label">Kode Risiko <span class="text-danger">*</span></label>
                                <select class="form-select" id="editKodeRisk" name="kode_risk_id" required>
                                    <option value="">Pilih Kode Risiko</option>
                                    @foreach($kodeRisk as $risk)
                                        <option value="{{ $risk->id }}">{{ $risk->kode_risiko }} - {{ $risk->deskripsi_risiko }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editKriteria" class="form-label">Kriteria <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="editKriteria" name="kriteria" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editSignifikan" class="form-label">Signifikansi <span class="text-danger">*</span></label>
                                <select class="form-select" id="editSignifikan" name="signifikan" required>
                                    <option value="">Pilih Signifikansi</option>
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Rendah">Rendah</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editDampakTerjadi" class="form-label">Dampak yang Terjadi</label>
                                <textarea class="form-control" id="editDampakTerjadi" name="dampak_terjadi" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editDampakPotensi" class="form-label">Dampak Potensial</label>
                                <textarea class="form-control" id="editDampakPotensi" name="dampak_potensi" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editPenyebab" class="form-label">Penyebab (Root Cause Analysis) <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editPenyebab" name="penyebab" rows="4" required placeholder="Jelaskan penyebab masalah berdasarkan analisis 5M (Man, Machine, Method, Material, Environment)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="saveEditIss()">
                    <i class="mdi mdi-content-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log('Document ready, initializing DataTable...');
    
    // Initialize DataTable
    $('#pelaporanTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [8] } // Disable sorting for action column
        ]
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Test modal functionality
    console.log('Modal element exists:', $('#issModal').length > 0);
    console.log('Bootstrap modal available:', typeof bootstrap !== 'undefined');
    
    // Test button click handler (button is now in card header)
    $('#testEditIssBtn').click(function() {
        console.log('Test button clicked');
        editIss(1); // Test with ID 1
    });
    
    // Logout handler for profile menu in card header
    $('#logout-link-card').click(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('logout') }}';
                form.style.display = 'none';
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                const csrfInput = $('<input>').attr({
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                });
                form.appendChild(csrfInput[0]);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});

function showIssModal(id, nomorLhaLhk) {
    console.log('showIssModal called with:', { id, nomorLhaLhk });
    
    // Show loading state
    $('#modalLhaLhkTitle').text(nomorLhaLhk);
    $('#issModalBody').html('<div class="text-center py-4"><i class="mdi mdi-loading mdi-spin mdi-36px"></i><p class="mt-2">Memuat data ISS...</p></div>');
    
    // Show modal using Bootstrap 5
    const modal = new bootstrap.Modal(document.getElementById('issModal'));
    modal.show();
    
    console.log('Modal should be visible now');
    
    // Load ISS data via AJAX using new endpoint
    $.ajax({
        url: `/audit/pelaporan-hasil-audit/${id}/temuan`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('AJAX response:', response);
            
            if (!response.success) {
                $('#issModalBody').html('<div class="text-center py-4"><i class="mdi mdi-alert-circle mdi-36px text-danger"></i><p class="mt-2">Gagal memuat data: ' + (response.message || 'Unknown error') + '</p></div>');
                return;
            }
            
            const temuanData = response.data || [];
            
            if (temuanData.length === 0) {
                $('#issModalBody').html('<div class="text-center py-4"><i class="mdi mdi-alert-circle mdi-36px text-warning"></i><p class="mt-2">Tidak ada data ISS untuk dokumen ini.</p></div>');
                return;
            }
            
            let html = '';
            temuanData.forEach((temuan, index) => {
                html += `
                    <div class="iss-item">
                        <div class="iss-header">
                            <div>
                                <span class="iss-number">ISS ${temuan.nomor_urut_iss}</span>
                                <span class="ms-2">${temuan.nomor_iss}</span>
                            </div>
                            <span class="iss-status ${temuan.status_approval === 'approved' ? 'bg-success' : (temuan.status_approval === 'rejected' ? 'bg-danger' : 'bg-warning')}">
                                ${temuan.status_approval === 'approved' ? 'Approved' : (temuan.status_approval === 'rejected' ? 'Rejected' : 'Pending')}
                            </span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Hasil Temuan:</div>
                                    <div class="field-value">${temuan.hasil_temuan || '-'}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Permasalahan:</div>
                                    <div class="field-value">${temuan.permasalahan || '-'}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Kode AOI:</div>
                                    <div class="field-value">
                                        <strong>${temuan.kode_aoi?.kode_area_of_improvement || '-'}</strong><br>
                                        <small>${temuan.kode_aoi?.deskripsi_area_of_improvement || '-'}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Kode Risiko:</div>
                                    <div class="field-value">
                                        <strong>${temuan.kode_risk?.kode_risiko || '-'}</strong><br>
                                        <small>${temuan.kode_risk?.deskripsi_risiko || '-'}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Kriteria:</div>
                                    <div class="field-value">${temuan.kriteria || '-'}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Signifikansi:</div>
                                    <div class="field-value">
                                        <span class="badge ${temuan.signifikan === 'Tinggi' ? 'bg-danger' : (temuan.signifikan === 'Medium' ? 'bg-warning' : 'bg-success')} significance-badge">
                                            ${temuan.signifikan || '-'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Dampak yang Terjadi:</div>
                                    <div class="field-value">${temuan.dampak_terjadi || '-'}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <div class="field-label">Dampak Potensial:</div>
                                    <div class="field-value">${temuan.dampak_potensi || '-'}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <div class="field-label">Penyebab (Root Cause Analysis):</div>
                            <div class="field-value">${temuan.penyebab || '-'}</div>
                        </div>
                        
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editIss(${temuan.id})">
                                <i class="mdi mdi-pencil me-1"></i>Edit ISS
                            </button>
                        </div>
                    </div>
                `;
            });
            
            $('#issModalBody').html(html);
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', { xhr, status, error });
            let errorMessage = 'Gagal memuat data ISS. Silakan coba lagi.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Data tidak ditemukan.';
            } else if (xhr.status === 500) {
                errorMessage = 'Terjadi kesalahan server.';
            }
            
            $('#issModalBody').html(`
                <div class="text-center py-4">
                    <i class="mdi mdi-alert-circle mdi-36px text-danger"></i>
                    <p class="mt-2">${errorMessage}</p>
                    <small class="text-muted">Status: ${xhr.status} | Error: ${error}</small>
                </div>
            `);
        }
    });
}

function editIss(temuanId) {
    console.log('Edit ISS with ID:', temuanId);
    console.log('URL:', `/audit/pelaporan-hasil-audit/temuan/${temuanId}`);
    
    // Fetch temuan data for editing
    $.ajax({
        url: `/audit/pelaporan-hasil-audit/temuan/${temuanId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const temuan = response.data;
                
                // Populate form fields
                $('#editTemuanId').val(temuan.id);
                $('#editHasilTemuan').val(temuan.hasil_temuan);
                $('#editPermasalahan').val(temuan.permasalahan);
                $('#editPenyebab').val(temuan.penyebab);
                $('#editKodeAoi').val(temuan.kode_aoi_id);
                $('#editKodeRisk').val(temuan.kode_risk_id);
                $('#editKriteria').val(temuan.kriteria);
                $('#editDampakTerjadi').val(temuan.dampak_terjadi);
                $('#editDampakPotensi').val(temuan.dampak_potensi);
                $('#editSignifikan').val(temuan.signifikan);
                
                // Show edit modal
                const editModal = new bootstrap.Modal(document.getElementById('editIssModal'));
                editModal.show();
                
                // Hide detail modal
                const detailModal = bootstrap.Modal.getInstance(document.getElementById('issModal'));
                if (detailModal) {
                    detailModal.hide();
                }
                
            } else {
                Swal.fire('Error', response.message || 'Gagal mengambil data ISS', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching temuan data:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            Swal.fire('Error', 'Gagal mengambil data ISS untuk diedit', 'error');
        }
    });
}

function saveEditIss() {
    const temuanId = $('#editTemuanId').val();
    
    // Collect form data manually
    const data = {
        hasil_temuan: $('#editHasilTemuan').val(),
        permasalahan: $('#editPermasalahan').val(),
        penyebab: $('#editPenyebab').val(),
        kode_aoi_id: $('#editKodeAoi').val(),
        kode_risk_id: $('#editKodeRisk').val(),
        kriteria: $('#editKriteria').val(),
        dampak_terjadi: $('#editDampakTerjadi').val(),
        dampak_potensi: $('#editDampakPotensi').val(),
        signifikan: $('#editSignifikan').val(),
        _token: $('meta[name="csrf-token"]').attr('content'),
        _method: 'PUT'
    };
    
    // Client-side validation
    const requiredFields = ['hasil_temuan', 'permasalahan', 'penyebab', 'kode_aoi_id', 'kode_risk_id', 'kriteria', 'signifikan'];
    const missingFields = [];
    
    requiredFields.forEach(field => {
        if (!data[field] || data[field].trim() === '') {
            missingFields.push(field);
        }
    });
    
    if (missingFields.length > 0) {
        Swal.fire('Error', `Field berikut harus diisi: ${missingFields.join(', ')}`, 'error');
        return;
    }
    
    // Show loading state
    const saveBtn = document.querySelector('button[onclick="saveEditIss()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...';
    saveBtn.disabled = true;
    
    // Log data being sent
    console.log('Data being sent:', data);
    console.log('CSRF Token:', data._token);
    console.log('Method:', data._method);
    
    $.ajax({
        url: `/audit/pelaporan-hasil-audit/temuan/${temuanId}`,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            console.log('Success response:', response);
            if (response.success) {
                // Show success message
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Hide edit modal
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editIssModal'));
                    editModal.hide();
                    
                    // Refresh ISS data in detail modal
                    const currentAuditId = $('#issModal').data('current-audit-id');
                    if (currentAuditId) {
                        showIssModal(currentAuditId, $('#modalLhaLhkTitle').text());
                    }
                });
                
            } else {
                Swal.fire('Error', response.message || 'Gagal menyimpan perubahan', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving temuan:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            let errorMessage = 'Gagal menyimpan perubahan';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 422) {
                errorMessage = 'Data tidak valid. Silakan periksa input Anda.';
            }
            
            Swal.fire('Error', errorMessage, 'error');
        },
        complete: function() {
            // Restore button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });
}

function deleteData(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data pelaporan hasil audit yang dihapus tidak dapat dikembalikan!",
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

function approveData(id) {
    Swal.fire({
        title: 'Approve Data',
        text: 'Anda yakin ingin approve data pelaporan hasil audit ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('approval-form-' + id);
            const actionInput = document.getElementById('action-' + id);
            if (actionInput) {
                actionInput.value = 'approve';
            }
            form.submit();
        }
    });
}

function approveDataPending(id) {
    Swal.fire({
        title: 'Tidak Dapat Approve',
        html: '<div class="text-start">' +
              '<p><strong>Data belum diapprove oleh ASMAN KSPI!</strong></p>' +
              '<p>Untuk melakukan approval Level 2, data harus diapprove oleh ASMAN KSPI terlebih dahulu (Level 1).</p>' +
              '<p class="text-muted">Status saat ini: <strong>Pending</strong></p>' +
              '</div>',
        icon: 'warning',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Mengerti'
    });
}

function rejectData(id) {
    Swal.fire({
        title: 'Reject Data',
        text: 'Masukkan alasan reject (minimal 10 karakter):',
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Ketik alasan reject di sini...',
        inputAttributes: {
            'aria-label': 'Alasan reject',
            'minlength': 10
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reject!',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Alasan reject harus diisi!'
            }
            if (value.length < 10) {
                return 'Alasan reject minimal 10 karakter!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('approval-form-' + id);
            const actionInput = document.getElementById('action-' + id);
            if (actionInput) {
                actionInput.value = 'reject';
            }
            const rejectionInput = document.createElement('input');
            rejectionInput.type = 'hidden';
            rejectionInput.name = 'rejection_reason';
            rejectionInput.value = result.value;
            form.appendChild(rejectionInput);
            form.submit();
        }
    });
}
</script>

<!-- Hidden Forms -->
@foreach($data as $item)
    <form id="delete-form-{{ $item->id }}" action="{{ route('audit.pelaporan-hasil-audit.destroy', $item->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection 