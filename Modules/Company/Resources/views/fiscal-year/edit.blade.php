@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Fiscal Years'],['label'=>'Edit']]" /><div class="card"><div class="card-body">@include('company::fiscal-year._form',['action'=>route('company.fiscal-year.update',$fiscalYear),'method'=>'PUT'])</div></div></div>@endsection
