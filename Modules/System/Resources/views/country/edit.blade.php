@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'System'],['label'=>'Countries'],['label'=>'Edit']]" /><div class="card"><div class="card-header"><h3>Edit country</h3></div><div class="card-body">@include('system::country._form', ['action'=>route('system.country.update',$country),'method'=>'PUT'])</div></div></div>@endsection
