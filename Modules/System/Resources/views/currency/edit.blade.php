@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'System'],['label'=>'Currencies'],['label'=>'Edit']]" /><div class="card"><div class="card-header"><h3>Edit currency</h3></div><div class="card-body">@include('system::currency._form', ['action'=>route('system.currency.update',$currency),'method'=>'PUT'])</div></div></div>@endsection
