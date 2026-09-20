<form action="{{ $action }}" method="POST">
    @csrf @if($method !== 'POST') @method($method) @endif
    <div class="row">
        <div class="col-md-4"><x-form.select name="country_id" label="Country" collection="$countries" optionLabel="name" :value="$timeZone->country_id ?? null" required placeholder="Select country" /></div>
        <div class="col-md-4"><x-form.input name="name" label="Name" labelClass="" :value="$timeZone->name ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="utc_offset" label="UTC offset" labelClass="" :value="$timeZone->utc_offset ?? null" placeholder="+03:30" required /></div>
        <div class="col-md-4"><x-form.checkbox name="is_default" id="is_default" label="Default" :checked="old('is_default', $timeZone->is_default ?? false)" /></div>
    </div>
    <x-button type="submit" text="Save" /> <x-button type="link" variant="light" text="Cancel" :href="route('system.time-zone.index')" />
</form>
