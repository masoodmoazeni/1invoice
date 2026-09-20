@extends('layout.master')
@section('content')
<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Companies'],['label'=>'Edit']]" />
<div class="card"><div class="card-header"><h3>Edit company</h3></div><div class="card-body">
@include('company::company._form', ['action' => route('company.company.update', $company), 'method' => 'PUT'])
</div></div></div>
@endsection
