@extends('layout.master')
@section('content')<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Branches'],['label'=>'Show']]" /><div class="card"><div class="card-body"><h3>{{ $branch->full_name }}</h3><p>{{ $branch->full_address }}</p><p>{{ $branch->contact_info }}</p><a href="{{ route('company.branch.edit',$branch) }}">Edit</a></div></div></div>@endsection
