@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Branches'],['label'=>'Create']]" /><div class="card"><div class="card-body">@include('company::branch._form',['action'=>route('company.branch.store'),'method'=>'POST'])</div></div></div>@endsection
