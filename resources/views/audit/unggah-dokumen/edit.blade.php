@extends('layouts.vertical', ['title' => 'Edit Dokumen Audit'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Edit Dokumen Audit</h4>
            </div>
            <div class="card-body">
                @include('audit.unggah-dokumen._form', [
                    'edit' => $edit,
                    'auditees' => $auditees,
                    'lhaList' => $lhaList
                ])
            </div>
        </div>
    </div>
</div>
@endsection 