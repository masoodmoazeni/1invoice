@extends('layout.master')
@section('content')
<div class="container-fluid py-5"><x-breadcrumb :items="[['label'=>'Company'],['label'=>'Companies'],['label'=>'Show']]" />
<div class="card"><div class="card-header"><h3>{{ $company->display_name }}</h3></div><div class="card-body">
<dl class="row"><dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $company->code }}</dd><dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $company->name }}</dd><dt class="col-sm-3">Legal name</dt><dd class="col-sm-9">{{ $company->legal_name ?: '-' }}</dd><dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $company->email ?: '-' }}</dd><dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $company->phone ?: '-' }}</dd><dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $company->full_address ?: '-' }}</dd><dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ $company->is_active ? 'Active' : 'Inactive' }}</dd></dl>
<x-button type="link" text="Edit" :href="route('company.company.edit', $company)" /> <x-button type="link" variant="light" text="Back" :href="route('company.company.index')" />
</div></div></div>
@endsection
