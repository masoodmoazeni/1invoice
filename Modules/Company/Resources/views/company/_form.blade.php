<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="row">
        <div class="col-md-4"><x-form.input name="code" label="Code" labelClass="" :value="old('code', $company->code ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="name" label="Name" labelClass="" :value="old('name', $company->name ?? null)" required /></div>
        <div class="col-md-4"><x-form.input name="legal_name" label="Legal name" labelClass="" :value="old('legal_name', $company->legal_name ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="email" type="email" label="Email" labelClass="" :value="old('email', $company->email ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="phone" label="Phone" labelClass="" :value="old('phone', $company->phone ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="mobile" label="Mobile" labelClass="" :value="old('mobile', $company->mobile ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="website" label="Website" labelClass="" :value="old('website', $company->website ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="city" label="City" labelClass="" :value="old('city', $company->city ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="state" label="State" labelClass="" :value="old('state', $company->state ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="postal_code" label="Postal code" labelClass="" :value="old('postal_code', $company->postal_code ?? null)" /></div>
        <div class="col-md-8"><x-form.input name="address" label="Address" labelClass="" :value="old('address', $company->address ?? null)" /></div>
        <div class="col-md-4"><x-form.input name="fiscal_year_start_month" type="number" label="Fiscal year start month" labelClass="" :value="old('fiscal_year_start_month', $company->fiscal_year_start_month ?? 1)" min="1" max="12" /></div>
        <div class="col-md-4"><x-form.checkbox name="is_active" id="is_active" label="Active" :checked="old('is_active', $company->is_active ?? true)" /></div>
    </div>
    <x-button type="submit" text="Save" />
    <x-button type="link" variant="light" text="Cancel" :href="route('company.company.index')" />
</form>
