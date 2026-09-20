<form action="{{ $action }}" method="POST">
    @csrf @if($method !== 'POST') @method($method) @endif
    <div class="row">
        <div class="col-md-4"><x-form.select name="from_currency_id" label="From currency" collection="$currencies" optionLabel="name" :value="$exchangeRate->from_currency_id ?? null" required placeholder="Select currency" /></div>
        <div class="col-md-4"><x-form.select name="to_currency_id" label="To currency" collection="$currencies" optionLabel="name" :value="$exchangeRate->to_currency_id ?? null" required placeholder="Select currency" /></div>
        <div class="col-md-4"><x-form.input type="number" name="rate" label="Rate" labelClass="" :value="$exchangeRate->rate ?? null" step="0.000001" required /></div>
        <div class="col-md-4"><x-form.date-picker name="effective_date" label="Effective date" :value="optional($exchangeRate->effective_date ?? null)->format('Y-m-d')" required /></div>
        <div class="col-md-4"><x-form.select name="source" label="Source" :options="['manual'=>'Manual','api'=>'API','central_bank'=>'Central bank','exchange_market'=>'Exchange market','import'=>'Import']" :value="$exchangeRate->source ?? null" placeholder="Select source" /></div>
    </div>
    <x-button type="submit" text="Save" /> <x-button type="link" variant="light" text="Cancel" :href="route('system.exchange-rate.index')" />
</form>
