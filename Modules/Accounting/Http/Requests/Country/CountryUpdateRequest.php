<?php

namespace Modules\Accounting\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $countryId = $this->route('id');

        return [
            'iso2' => [
                'sometimes',
                'string',
                'max:2',
                'regex:/^[A-Z]{2}$/',
                Rule::unique('countries', 'iso2')->ignore($countryId),
            ],
            'iso3' => [
                'sometimes',
                'string',
                'max:3',
                'regex:/^[A-Z]{3}$/',
                Rule::unique('countries', 'iso3')->ignore($countryId),
            ],
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('countries', 'name')->ignore($countryId),
            ],
            'numeric_code' => 'nullable|string|max:10',
            'phone_code' => 'nullable|string|max:10',
            'capital' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'iso2.max' => 'کد دو حرفی کشور نباید بیشتر از ۲ کاراکتر باشد',
            'iso2.unique' => 'کد دو حرفی کشور قبلاً ثبت شده است',
            'iso2.regex' => 'کد دو حرفی کشور باید با حروف بزرگ انگلیسی باشد',
            
            'iso3.max' => 'کد سه حرفی کشور نباید بیشتر از ۳ کاراکتر باشد',
            'iso3.unique' => 'کد سه حرفی کشور قبلاً ثبت شده است',
            'iso3.regex' => 'کد سه حرفی کشور باید با حروف بزرگ انگلیسی باشد',
            
            'name.max' => 'نام کشور نباید بیشتر از ۱۰۰ کاراکتر باشد',
            'name.unique' => 'نام کشور قبلاً ثبت شده است',
            
            'numeric_code.max' => 'کد عددی کشور نباید بیشتر از ۱۰ کاراکتر باشد',
            'phone_code.max' => 'کد تلفن کشور نباید بیشتر از ۱۰ کاراکتر باشد',
            'capital.max' => 'نام پایتخت نباید بیشتر از ۱۰۰ کاراکتر باشد',
            
            'is_active.boolean' => 'وضعیت فعال باید مقدار true یا false باشد',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تبدیل به حروف بزرگ
        if ($this->has('iso2')) {
            $this->merge([
                'iso2' => strtoupper($this->iso2),
            ]);
        }

        if ($this->has('iso3')) {
            $this->merge([
                'iso3' => strtoupper($this->iso3),
            ]);
        }
    }
}