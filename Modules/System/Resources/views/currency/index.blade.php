@extends('layout.master')
@section('content')
<div class="container-fluid py-5">
    <x-breadcrumb :items="[['label' => 'System'], ['label' => 'Currencies']]" />
    <x-data-table
        :headers="[
            ['key' => 'code', 'label' => 'Code'], ['key' => 'name', 'label' => 'Name'],
            ['key' => 'symbol', 'label' => 'Symbol'], ['key' => 'decimal_places', 'label' => 'Decimals'],
            ['key' => 'rounding', 'label' => 'Rounding'],
            ['key' => 'is_active', 'label' => 'Active', 'format' => fn($value) => $value ? 'Yes' : 'No'],
        ]"
        :rows="$currencies" title="Currencies" title-create="Add currency"
        resource-name="system.currency"
        :actions="[
            ['type' => 'link', 'route' => 'system.currency.show', 'label' => 'Show'],
            ['type' => 'link', 'route' => 'system.currency.edit', 'label' => 'Edit'],
            ['type' => 'delete', 'route' => 'system.currency.destroy', 'label' => 'Delete'],
        ]"
    />
</div>
@endsection
