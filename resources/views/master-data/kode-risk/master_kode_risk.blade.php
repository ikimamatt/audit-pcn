@extends('layouts.vertical', ['title' => 'Master Kode Risk'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
@endsection

@section('content')
<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold m-0">Master Kode Risk</h4>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tabel Master Kode Risk</h5>
            </div>
            <div class="card-body">
                <table id="responsive-datatable" class="table table-bordered dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kelompok Risiko</th>
                            <th>Kode Risiko</th>
                            <th>Kelompok Risiko Detail</th>
                            <th>Deskripsi Risiko</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->kelompok_risiko }}</td>
                            <td>{{ $row->kode_risiko }}</td>
                            <td>{{ $row->kelompok_risiko_detail }}</td>
                            <td>{{ $row->deskripsi_risiko }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @vite([ 'resources/js/pages/datatable.init.js'])
@endsection 