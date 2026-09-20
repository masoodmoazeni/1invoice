<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="row">
        <div class="col-md-4"><x-form.input name="iso2" label="ISO2" labelClass="" :value="$country->iso2 ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="iso3" label="ISO3" labelClass="" :value="$country->iso3 ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="name" label="Name" labelClass="" :value="$country->name ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="numeric_code" label="Numeric code" labelClass="" :value="$country->numeric_code ?? null" /></div>
        <div class="col-md-4"><x-form.input name="phone_code" label="Phone code" labelClass="" :value="$country->phone_code ?? null" /></div>
        <div class="col-md-4"><x-form.input name="capital" label="Capital" labelClass="" :value="$country->capital ?? null" /></div>
        <div class="col-md-4"><x-form.checkbox name="is_active" id="is_active" label="Active" :checked="old('is_active', $country->is_active ?? true)" /></div>
    </div>
    <x-button type="submit" text="Save" />
    <x-button type="link" variant="light" text="Cancel" :href="route('system.country.index')" />
</form>
