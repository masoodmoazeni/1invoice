@extends('layout.master')
@section('content')
<div class="container-fluid py-5">
    <x-breadcrumb :items="[['label' => 'System'], ['label' => 'Languages']]" />
    <x-data-table
        :headers="[
            ['key' => 'code', 'label' => 'Code'], ['key' => 'name', 'label' => 'Name'],
            ['key' => 'native_name', 'label' => 'Native name'], ['key' => 'direction', 'label' => 'Direction'],
            ['key' => 'is_active', 'label' => 'Active', 'format' => fn($value) => $value ? 'Yes' : 'No'],
        ]"
        :rows="$languages" title="Languages" title-create="Add language"
        resource-name="system.language"
        :actions="[
            ['type' => 'link', 'route' => 'system.language.show', 'label' => 'Show'],
            ['type' => 'link', 'route' => 'system.language.edit', 'label' => 'Edit'],
            ['type' => 'delete', 'route' => 'system.language.destroy', 'label' => 'Delete'],
        ]"
    />
</div>
@endsection
