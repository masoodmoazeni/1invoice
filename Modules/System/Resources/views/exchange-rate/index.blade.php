@extends('layout.master')
@section('content')
<div class="container-fluid py-5">
    <x-breadcrumb :items="[['label' => 'System'], ['label' => 'Exchange rates']]" />
    <x-data-table
        :headers="[
            ['key' => 'fromCurrency.code', 'label' => 'From'], ['key' => 'toCurrency.code', 'label' => 'To'],
            ['key' => 'rate', 'label' => 'Rate'], ['key' => 'effective_date', 'label' => 'Effective date'],
            ['key' => 'source_label', 'label' => 'Source'],
        ]"
        :rows="$exchangeRates" title="Exchange rates" title-create="Add exchange rate"
        resource-name="system.exchange-rate"
        :actions="[
            ['type' => 'link', 'route' => 'system.exchange-rate.show', 'label' => 'Show'],
            ['type' => 'link', 'route' => 'system.exchange-rate.edit', 'label' => 'Edit'],
            ['type' => 'delete', 'route' => 'system.exchange-rate.destroy', 'label' => 'Delete'],
        ]"
    />
</div>
@endsection
