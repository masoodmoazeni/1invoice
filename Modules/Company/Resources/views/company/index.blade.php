@extends('layout.master')
@section('content')
<div class="container-fluid py-5">
    <x-breadcrumb :items="[['label' => 'Company'], ['label' => 'Companies']]" />
    <x-data-table :headers="[
        ['key' => 'code', 'label' => 'Code'], ['key' => 'name', 'label' => 'Name'],
        ['key' => 'email', 'label' => 'Email'], ['key' => 'phone', 'label' => 'Phone'],
        ['key' => 'city', 'label' => 'City'],
        ['key' => 'is_active', 'label' => 'Active', 'format' => fn($value) => $value ? 'Yes' : 'No'],
    ]" :rows="$companies" title="Companies" title-create="Add company" resource-name="company.company"
        :actions="[
            ['type' => 'link', 'route' => 'company.company.show', 'label' => 'Show'],
            ['type' => 'link', 'route' => 'company.company.edit', 'label' => 'Edit'],
            ['type' => 'delete', 'route' => 'company.company.destroy', 'label' => 'Delete'],
        ]" />
</div>
@endsection
