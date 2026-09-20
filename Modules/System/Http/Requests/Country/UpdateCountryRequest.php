<?php

namespace Modules\System\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('country') ?? $this->route('id');
        $id = is_object($id) ? $id->id : $id;
        return [
            'iso2' => ['sometimes','string','size:2','regex:/^[A-Z]{2}$/',Rule::unique('countries','iso2')->ignore($id)],
            'iso3' => ['sometimes','string','size:3','regex:/^[A-Z]{3}$/',Rule::unique('countries','iso3')->ignore($id)],
            'name' => ['sometimes','string','max:100',Rule::unique('countries','name')->ignore($id)],
            'numeric_code' => ['nullable','string','max:10'], 'phone_code' => ['nullable','string','max:10'],
            'capital' => ['nullable','string','max:100'], 'is_active' => ['sometimes','boolean'],
        ];
    }
}
