@extends('layouts.vertical', ['title' => 'Detail Pelaporan Hasil Audit'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Pelaporan Hasil Audit</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Nomor LHA/LHK</dt>
                                <dd class="col-sm-8">{{ $item->nomor_lha_lhk ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">Jenis</dt>
                                <dd class="col-sm-8">{{ $item->jenis_lha_lhk ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">Jenis Audit</dt>
                                <dd class="col-sm-8">
                                    @php
                                        $jenisAudit = $item->jenisAudit ?? \App\Models\MasterData\MasterJenisAudit::where('kode', $item->kode_spi)->first();
                                    @endphp
                                    {{ $jenisAudit ? $jenisAudit->nama_jenis_audit : '-' }}
                                </dd>
                                
                                <dt class="col-sm-4">Kode SPI</dt>
                                <dd class="col-sm-8">{{ $item->kode_spi ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Status Approval</dt>
                                <dd class="col-sm-8">
                                    @if($item->status_approval == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($item->status_approval == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Tanggal</dt>
                                <dd class="col-sm-8">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($item->temuan && $item->temuan->count() > 0)
                    <div class="mt-4">
                        <h5>Daftar ISS</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor ISS</th>
                                        <th>Hasil Temuan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->temuan as $index => $temuan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $temuan->nomor_iss ?? '-' }}</td>
                                        <td>{{ Str::limit($temuan->hasil_temuan, 100) ?? '-' }}</td>
                                        <td>
                                            @if($temuan->status_approval == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($temuan->status_approval == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('audit.pelaporan-hasil-audit.index') }}" class="btn btn-secondary">Kembali</a>
                        <a href="{{ route('audit.pelaporan-hasil-audit.edit', $item->id) }}" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
