<form action="{{ $action }}" method="POST">
    @csrf @if($method !== 'POST') @method($method) @endif
    <div class="row">
        <div class="col-md-4"><x-form.input name="code" label="Code" labelClass="" :value="$language->code ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="name" label="Name" labelClass="" :value="$language->name ?? null" required /></div>
        <div class="col-md-4"><x-form.input name="native_name" label="Native name" labelClass="" :value="$language->native_name ?? null" /></div>
        <div class="col-md-4"><x-form.select name="direction" label="Direction" :options="['ltr'=>'LTR','rtl'=>'RTL']" :value="$language->direction ?? 'ltr'" required placeholder="Select direction" /></div>
        <div class="col-md-4"><x-form.checkbox name="is_active" id="is_active" label="Active" :checked="old('is_active', $language->is_active ?? true)" /></div>
    </div>
    <x-button type="submit" text="Save" /> <x-button type="link" variant="light" text="Cancel" :href="route('system.language.index')" />
</form>
