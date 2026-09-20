@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Departments'],['label'=>'Create']]" /><div class="card"><div class="card-body">@include('company::department._form',['action'=>route('company.department.store'),'method'=>'POST'])</div></div></div>@endsection
