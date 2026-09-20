@extends('layout.master')
@section('content')
<div class="container-fluid py-5">
    <x-breadcrumb :items="[['label' => 'System'], ['label' => 'Countries']]" />
    <x-data-table
        :headers="[
            ['key' => 'iso2', 'label' => 'ISO2'],
            ['key' => 'iso3', 'label' => 'ISO3'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'phone_code', 'label' => 'Phone code'],
            ['key' => 'capital', 'label' => 'Capital'],
            ['key' => 'is_active', 'label' => 'Active', 'format' => fn($value) => $value ? 'Yes' : 'No'],
        ]"
        :rows="$countries"
        title="Countries"
        title-create="Add country"
        resource-name="system.country"
        :actions="[
            ['type' => 'link', 'route' => 'system.country.show', 'label' => 'Show'],
            ['type' => 'link', 'route' => 'system.country.edit', 'label' => 'Edit'],
            ['type' => 'delete', 'route' => 'system.country.destroy', 'label' => 'Delete'],
        ]"
    />
</div>
@endsection
