@extends('layout.master')
@section('content')
<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Companies'],['label'=>'Create']]" />
<div class="card"><div class="card-header"><h3>Create company</h3></div><div class="card-body">
@include('company::company._form', ['action' => route('company.company.store'), 'method' => 'POST'])
</div></div></div>
@endsection
