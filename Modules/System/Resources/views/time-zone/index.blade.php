@extends('layout.master')
@section('content')
<div class="container-fluid py-5">
    <x-breadcrumb :items="[['label' => 'System'], ['label' => 'Time zones']]" />
    <x-data-table
        :headers="[
            ['key' => 'country.name', 'label' => 'Country'], ['key' => 'name', 'label' => 'Name'],
            ['key' => 'utc_offset', 'label' => 'UTC offset'],
            ['key' => 'is_default', 'label' => 'Default', 'format' => fn($value) => $value ? 'Yes' : 'No'],
        ]"
        :rows="$timeZones" title="Time zones" title-create="Add time zone"
        resource-name="system.time-zone"
        :actions="[
            ['type' => 'link', 'route' => 'system.time-zone.show', 'label' => 'Show'],
            ['type' => 'link', 'route' => 'system.time-zone.edit', 'label' => 'Edit'],
            ['type' => 'delete', 'route' => 'system.time-zone.destroy', 'label' => 'Delete'],
        ]"
    />
</div>
@endsection
