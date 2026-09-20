@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Branches'],['label'=>'Edit']]" /><div class="card"><div class="card-body">@include('company::branch._form',['action'=>route('company.branch.update',$branch),'method'=>'PUT'])</div></div></div>@endsection
