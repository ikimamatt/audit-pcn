@extends('layouts.vertical', ['title' => 'Master Auditee'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
     ])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.auditee.index') }}">Master Data</a></li>
                    <li class="breadcrumb-item active">Auditee</li>
                </ol>
            </div>
            <h4 class="page-title">Master Auditee</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-5">
                        <a href="{{ route('master.auditee.create') }}" class="btn btn-primary mb-2">
                            <i class="mdi mdi-plus-circle me-2"></i> Tambah Auditee
                        </a>
                    </div>
                </div>

                @include('components.alert')

                <div class="table-responsive">
                    <table id="responsive-datatable" class="table table-centered w-100 dt-responsive nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Divisi/Cabang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->divisi }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('master.auditee.edit', $item->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('master.auditee.destroy', $item->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
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
