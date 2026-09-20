<?php

namespace Modules\Company\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $company = $this->route('company') ?? $this->route('id');
        $id = is_object($company) ? $company->id : $company;

        return [
            'code' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('companies', 'code')->ignore($id)],
            'name' => ['sometimes', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'base_currency_id' => ['nullable', 'integer', 'exists:currencies,id'],
            'language_id' => ['nullable', 'integer', 'exists:languages,id'],
            'timezone_id' => ['nullable', 'integer', 'exists:time_zones,id'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'string', 'max:255'],
            'fiscal_year_start_month' => ['nullable', 'integer', 'between:1,12'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
