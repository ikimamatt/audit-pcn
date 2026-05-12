@extends('layouts.vertical', ['title' => 'Detail Program Kerja Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">DETAIL PROGRAM KERJA AUDIT</h4>
            </div>
            <div class="card-body">
                <div class="mb-3"><strong>Surat Tugas:</strong> {{ $item->perencanaanAudit->nomor_surat_tugas ?? '-' }}</div>
                <div class="mb-3"><strong>No PKA:</strong> {{ $item->no_pka }}</div>
                <div class="mb-3"><strong>Tanggal PKA:</strong> {{ $item->tanggal_pka }}</div>
                <div class="mb-3"><strong>Informasi Umum:</strong> {{ $item->informasi_umum }}</div>
                <div class="mb-3"><strong>KPI Tidak Tercapai:</strong> {{ $item->kpi_tidak_tercapai }}</div>
                @php
                    $dataAwal = is_array($item->data_awal_dokumen) ? $item->data_awal_dokumen : json_decode($item->data_awal_dokumen ?? '[]', true);
                @endphp
                <div class="mb-3">
                    <strong>Data Awal Yang Perlu Disiapkan:</strong>
                    @if(empty($dataAwal))
                        -
                    @elseif(!is_array($dataAwal))
                        {{ $item->data_awal_dokumen }}
                    @else
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dokumen</th>
                                        <th>Ruang Lingkup</th>
                                        <th>Periode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataAwal as $idx => $da)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $da['nama_dokumen'] ?? '-' }}</td>
                                        <td>{{ $da['ruang_lingkup'] ?? '-' }}</td>
                                        <td>{{ $da['periode'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Risk Based Audit:</strong>
                    <ul>
                        @foreach($item->risks as $risk)
                        <li>
                            <b>Deskripsi:</b> {{ $risk->deskripsi_resiko }}<br>
                            <b>Penyebab:</b> {{ $risk->penyebab_resiko }}<br>
                            <b>Dampak:</b> {{ $risk->dampak_resiko }}<br>
                            <b>Pengendalian:</b> {{ $risk->pengendalian_eksisting }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mb-3">
                    <strong>Milestone:</strong>
                    <ul>
                        @foreach($item->milestones as $ms)
                        <li>{{ $ms->nama_milestone }}: {{ $ms->tanggal_mulai }} - {{ $ms->tanggal_selesai }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="mb-3">
                    <strong>Dokumen:</strong>
                    <ul>
                        @foreach($item->dokumen as $dok)
                        <li>
                            {{ $dok->nama_dokumen }} - <a href="{{ asset('storage/'.$dok->file_path) }}" target="_blank">Lihat</a>
                            @if($dok->status_approval == 'pending')
                                <form action="{{ route('audit.pka.approval', [$item->id, $dok->id]) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            @else
                                <span class="badge bg-{{ $dok->status_approval == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($dok->status_approval) }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                <a href="{{ route('audit.pka.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection 