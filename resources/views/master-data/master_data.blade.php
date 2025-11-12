@extends('layouts.vertical', ['title' => 'Master Data'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Master Data</h4>
            </div>
            <div class="card-body">
                <ul>
                    <li><a href="{{ route('master.kode-aoi.index') }}">Master Kode AOI</a></li>
                    <li><a href="{{ route('master.kode-risk.index') }}">Master Kode Risk</a></li>
                    <li><a href="{{ route('master.auditee.index') }}">Master Auditee</a></li>
                    <li><a href="{{ route('master.user.index') }}">Master User</a></li>
                    <!-- <li><a href="{{ route('master.akses-user.index') }}">Master Akses User</a></li> -->
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 