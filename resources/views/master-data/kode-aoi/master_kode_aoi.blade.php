@extends('layouts.vertical', ['title' => 'Master Kode AOI'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
@endsection

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">Master Kode AOI</h4>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tabel Master Kode AOI</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <table id="responsive-datatable" class="table table-bordered dt-responsive nowrap align-middle">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th style="min-width: 180px;">Indikator Pengawasan</th>
                            <th style="min-width: 120px;">Kode Area Of Improvement</th>
                            <th style="min-width: 250px; max-width: 400px; word-break: break-word; white-space: pre-line;">Deskripsi Area Of Improvement</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->indikator_pengawasan }}</td>
                            <td>{{ $row->kode_area_of_improvement }}</td>
                            <td style="word-break: break-word; white-space: pre-line;">{{ $row->deskripsi_area_of_improvement }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
@endsection 