@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'System'],['label'=>'Languages'],['label'=>'Edit']]" /><div class="card"><div class="card-header"><h3>Edit language</h3></div><div class="card-body">@include('system::language._form', ['action'=>route('system.language.update',$language),'method'=>'PUT'])</div></div></div>@endsection
