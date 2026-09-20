<form action="{{ $action }}" method="POST">
    @csrf @if($method !== 'POST') @method($method) @endif
    <div class="row">
        <div class="col-md-4"><x-form.input name="code" label="Code" labelClass="" :value="$currency->code ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="name" label="Name" labelClass="" :value="$currency->name ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="symbol" label="Symbol" labelClass="" :value="$currency->symbol ?? null" /></div>
        <div class="col-md-4"><x-form.input type="number" name="decimal_places" label="Decimal places" labelClass="" :value="$currency->decimal_places ?? 2" min="0" max="8" /></div>
        <div class="col-md-4"><x-form.input type="number" name="rounding" label="Rounding" labelClass="" :value="$currency->rounding ?? '0.01'" step="0.0001" /></div>
        <div class="col-md-4"><x-form.checkbox name="is_active" id="is_active" label="Active" :checked="old('is_active', $currency->is_active ?? true)" /></div>
    </div>
    <x-button type="submit" text="Save" /> <x-button type="link" variant="light" text="Cancel" :href="route('system.currency.index')" />
</form>
