<?php

namespace Modules\System\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCountryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'iso2' => ['required','string','size:2','regex:/^[A-Z]{2}$/','unique:countries,iso2'],
            'iso3' => ['required','string','size:3','regex:/^[A-Z]{3}$/','unique:countries,iso3'],
            'name' => ['required','string','max:100','unique:countries,name'],
            'numeric_code' => ['nullable','string','max:10'],
            'phone_code' => ['nullable','string','max:10'],
            'capital' => ['nullable','string','max:100'],
            'is_active' => ['sometimes','boolean'],
        ];
    }
}
