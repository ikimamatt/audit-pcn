@extends('layouts.vertical', ['title' => 'Detail TOD BPM Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">DETAIL TOD BPM AUDIT</h4>
            </div>
            <div class="card-body">
                <div class="mb-3"><strong>Surat Tugas:</strong> {{ $item->perencanaanAudit->nomor_surat_tugas ?? '-' }}</div>
                <div class="mb-3"><strong>Judul BPM:</strong> {{ $item->judul_bpm }}</div>
                <div class="mb-3"><strong>Nama BPO:</strong> {{ $item->nama_bpo }}</div>
                <div class="mb-3">
                    <strong>Resiko:</strong>
                    @php
                        $resikoArray = $item->resiko ? (is_string($item->resiko) && (strpos($item->resiko, '[') === 0 || strpos($item->resiko, '{') === 0) ? json_decode($item->resiko, true) : [$item->resiko]) : [];
                        $kontrolArray = $item->kontrol ? (is_string($item->kontrol) && (strpos($item->kontrol, '[') === 0 || strpos($item->kontrol, '{') === 0) ? json_decode($item->kontrol, true) : [$item->kontrol]) : [];
                    @endphp
                    @if(count($resikoArray) > 0)
                        <ol>
                            @foreach($resikoArray as $index => $resiko)
                                <li class="mb-2">
                                    <strong>Resiko {{ $index + 1 }}:</strong> {{ $resiko }}
                                    @if(isset($kontrolArray[$index]) && $kontrolArray[$index])
                                        <br><strong>Kontrol {{ $index + 1 }}:</strong> {{ $kontrolArray[$index] }}
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
                <div class="mb-3"><strong>File BPM:</strong>
                    @if($item->file_bpm)
                        <a href="{{ asset('storage/' . $item->file_bpm) }}" target="_blank" class="btn btn-info btn-sm">View</a>
                        <a href="{{ asset('storage/' . $item->file_bpm) }}" download class="btn btn-primary btn-sm">Download</a>
                    @else
                        -
                    @endif
                </div>
                <div class="mb-3"><strong>Status Approval:</strong>
                    @if($item->status_approval == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($item->status_approval == 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Evaluasi BPM:</strong>
                    <ul>
                        @foreach($item->evaluasi as $ev)
                        <li>{{ $ev->hasil_evaluasi }}</li>
                        @endforeach
                    </ul>
                </div>
                <a href="{{ route('audit.tod-bpm.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('audit.tod-bpm.edit', $item->id) }}" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection 