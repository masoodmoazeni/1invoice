@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Fiscal Years'],['label'=>'Create']]" /><div class="card"><div class="card-body">@include('company::fiscal-year._form',['action'=>route('company.fiscal-year.store'),'method'=>'POST'])</div></div></div>@endsection
