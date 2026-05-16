@extends('layouts.vertical', ['title' => 'Detail TOD BPM Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Detail TOD BPM Audit</h4>
                <div>
                    @if($item->status_approval == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($item->status_approval == 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </div>
            </div>
            <div class="card-body">

                {{-- Info Dasar --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width:40%">Surat Tugas</td>
                                <td><strong>{{ $item->perencanaanAudit->nomor_surat_tugas ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Judul BPM</td>
                                <td>{{ $item->judul_bpm }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama BPO</td>
                                <td>{{ $item->nama_bpo }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width:40%">File BPM</td>
                                <td>
                                    @if($item->file_bpm)
                                        <a href="{{ asset('storage/' . $item->file_bpm) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="mdi mdi-file-pdf me-1"></i>Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">File KKA ToD</td>
                                <td>
                                    @if($item->file_kka_tod)
                                        <a href="{{ asset('storage/' . $item->file_kka_tod) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-download me-1"></i>Download
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Hasil Evaluasi</td>
                                <td>
                                    @foreach($item->evaluasi as $ev)
                                        <span class="badge {{ $ev->hasil_evaluasi === 'Sesuai' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $ev->hasil_evaluasi }}
                                        </span>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Risiko & Kontrol (dari relasi pivot) --}}
                <div class="mb-4">
                    <h5 class="fw-semibold mb-3">
                        <i class="mdi mdi-shield-alert-outline me-1 text-danger"></i>
                        Risiko &amp; Kontrol yang Diuji
                    </h5>

                    @if($risikoData->isNotEmpty())
                        <div class="list-group">
                            @foreach($risikoData as $i => $rd)
                                @php $risiko = $rd['risiko']; $kontrolDipilih = $rd['kontrolDipilih']; @endphp
                                <div class="list-group-item p-0 mb-2 border rounded">
                                    <div class="p-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-danger-subtle text-danger me-2 mt-1">R{{ $i + 1 }}</span>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $risiko->deskripsi_risiko }}</div>
                                                @if($risiko->penyebab_risiko)
                                                    <div class="text-muted small mt-1">
                                                        <strong>Penyebab:</strong> {{ $risiko->penyebab_risiko }}
                                                    </div>
                                                @endif
                                                @if($risiko->dampak_risiko)
                                                    <div class="text-muted small">
                                                        <strong>Dampak:</strong> {{ $risiko->dampak_risiko }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($kontrolDipilih->isNotEmpty())
                                        <div class="px-4 pb-3 border-top bg-light bg-opacity-50">
                                            <p class="text-muted small mb-2 mt-2 fw-semibold">Kontrol yang Diuji:</p>
                                            @foreach($kontrolDipilih as $j => $kontrol)
                                                <div class="d-flex align-items-start mb-1">
                                                    <span class="badge bg-primary-subtle text-primary me-2 mt-1">K{{ $j + 1 }}</span>
                                                    <span class="small">{{ $kontrol->deskripsi_kontrol }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="px-4 pb-2 border-top">
                                            <span class="text-muted small">Tidak ada kontrol yang dipilih untuk risiko ini.</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Belum ada risiko yang ditautkan ke TOD ini.
                        </div>
                    @endif
                </div>

                <a href="{{ route('audit.tod-bpm.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left me-1"></i>Kembali
                </a>
                <a href="{{ route('audit.tod-bpm.edit', $item->id) }}" class="btn btn-warning">
                    <i class="mdi mdi-pencil me-1"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection