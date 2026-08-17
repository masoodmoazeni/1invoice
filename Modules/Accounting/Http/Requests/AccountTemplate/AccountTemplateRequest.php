<?php

namespace Modules\Accounting\Http\Requests\AccountTemplate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountTemplateRequest extends FormRequest
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
        return [
            'country_id' => 'required|integer|exists:countries,id',
            'name' => 'required|string|max:100|unique:account_templates,name,NULL,id,country_id,' . $this->country_id,
            'version' => 'sometimes|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'country_id.required' => 'شناسه کشور الزامی است',
            'country_id.integer' => 'شناسه کشور باید عددی باشد',
            'country_id.exists' => 'کشور انتخاب شده معتبر نیست',
            
            'name.required' => 'نام قالب الزامی است',
            'name.max' => 'نام قالب نباید بیشتر از ۱۰۰ کاراکتر باشد',
            'name.unique' => 'نام قالب قبلاً در این کشور ثبت شده است',
            
            'version.max' => 'نسخه قالب نباید بیشتر از ۲۰ کاراکتر باشد',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // مقدار پیش‌فرض برای version
        if (!$this->has('version')) {
            $this->merge([
                'version' => '1.0.0',
            ]);
        }
    }
}