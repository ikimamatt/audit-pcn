@extends('layouts.vertical', ['title' => 'Pemantauan Hasil Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">PEMANTAUAN HASIL AUDIT</h4>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3 d-flex align-items-center" action="">
                    <label for="bulan" class="me-2 mb-0">Filter Bulan:</label>
                    <input type="month" name="bulan" id="bulan" class="form-control me-2" style="max-width:200px;" value="{{ request('bulan') }}">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </form>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="table-responsive" style="overflow-x:auto;">
                    <table class="table table-bordered table-hover" style="min-width:1200px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Auditee</th>
                                <th>Nomor Tugas</th>
                                <th>Nomor ISS</th>
                                <th>Temuan</th>
                                <th>Rekomendasi</th>
                                <th>Eviden</th>
                                <th>Target Waktu</th>
                                <th>PIC Rekomendasi</th>
                                <th>Status Tindak Lanjut</th>
                                <th>Aksi</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($row->temuan && $row->temuan->pelaporanHasilAudit && $row->temuan->pelaporanHasilAudit->perencanaanAudit && $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee)
                                        <strong>{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi ?? 'N/A' }}</strong>
                                        @if($row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat)
                                            <br>
                                            <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->direktorat }}</small>
                                        @endif
                                        @if($row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang)
                                            <br>
                                            <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->auditee->divisi_cabang }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->temuan && $row->temuan->pelaporanHasilAudit && $row->temuan->pelaporanHasilAudit->perencanaanAudit)
                                        <strong>{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->nomor_surat_tugas ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->perencanaanAudit->jenis_audit ?? 'N/A' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->temuan)
                                        <strong>{{ $row->temuan->nomor_iss ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $row->temuan->pelaporanHasilAudit->nomor_lha_lhk ?? 'N/A' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->temuan)
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $row->temuan->permasalahan ?? 'N/A' }}">
                                            <strong>Permasalahan:</strong><br>
                                            {{ Str::limit($row->temuan->permasalahan ?? 'N/A', 100) }}
                                        </div>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $row->temuan->penyebab ?? 'N/A' }}">
                                            <strong>Penyebab:</strong><br>
                                            {{ Str::limit($row->temuan->penyebab ?? 'N/A', 100) }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $row->rekomendasi }}">
                                        {{ Str::limit($row->rekomendasi, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $row->eviden_rekomendasi }}">
                                        {{ Str::limit($row->eviden_rekomendasi, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $row->target_waktu < now() ? 'bg-danger' : 'bg-success' }}">
                                        {{ \Carbon\Carbon::parse($row->target_waktu)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>{{ $row->pic_rekomendasi }}</td>
                                <td>
                                    @php
                                        // Ambil status tindak lanjut terbaru jika ada
                                        $latestTindakLanjut = $row->tindakLanjut->sortByDesc('created_at')->first();
                                        $statusTindakLanjut = $latestTindakLanjut ? $latestTindakLanjut->status_tindak_lanjut : $row->status_tindak_lanjut;
                                    @endphp
                                    
                                    @if($statusTindakLanjut == 'closed')
                                        <span class="badge bg-success">
                                            <i class="mdi mdi-check-circle me-1"></i>Closed
                                        </span>
                                    @elseif($statusTindakLanjut == 'on_progress')
                                        <span class="badge bg-info">
                                            <i class="mdi mdi-clock me-1"></i>On Progress
                                        </span>
                                    @elseif($statusTindakLanjut == 'open')
                                        <span class="badge bg-warning">
                                            <i class="mdi mdi-alert-circle me-1"></i>Open
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="mdi mdi-help-circle me-1"></i>{{ ucfirst($statusTindakLanjut ?? 'Unknown') }}
                                        </span>
                                    @endif
                                    
                                    @if($row->tindakLanjut->count() > 0)
                                        <br>
                                        <small class="text-muted">
                                            {{ $row->tindakLanjut->count() }} tindak lanjut
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <a href="{{ route('audit.pemantauan.edit', $row->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>
                                        @php
                                            $latestStatus = $row->tindakLanjut->sortByDesc('created_at')->first()?->status_tindak_lanjut ?? $row->status_tindak_lanjut;
                                        @endphp
                                        <a href="{{ route('audit.penutup-lha-rekomendasi.tindak-lanjut.form', $row->id) }}" 
                                           class="btn btn-success btn-sm mb-1"
                                           @if($latestStatus == 'closed')
                                               title="Meski status sudah closed, Anda masih bisa menambah tindak lanjut"
                                               data-bs-toggle="tooltip"
                                           @endif>
                                            @if($latestStatus == 'closed')
                                                <i class="mdi mdi-plus-circle me-1"></i>
                                            @endif
                                            Tindak Lanjut
                                        </a>
                                        <form action="{{ route('audit.penutup-lha-rekomendasi.destroy', $row->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('audit.pemantauan.tindak-lanjut.index', $row->id) }}" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="mdi mdi-information-outline mdi-36px mb-3"></i>
                                        <p class="mb-0">Belum ada data rekomendasi untuk ditampilkan.</p>
                                        <small>Silakan buat rekomendasi terlebih dahulu di menu Penutup LHA/LHK.</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection 