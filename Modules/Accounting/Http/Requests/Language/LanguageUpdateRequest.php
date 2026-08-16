<?php

namespace Modules\Accounting\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageUpdateRequest extends FormRequest
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
        $languageId = $this->route('id');

        return [
            'code' => [
                'sometimes',
                'string',
                'max:5',
                'regex:/^[a-z]{2,5}$/',
                Rule::unique('languages', 'code')->ignore($languageId),
            ],
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('languages', 'name')->ignore($languageId),
            ],
            'native_name' => 'nullable|string|max:100',
            'direction' => 'sometimes|string|in:ltr,rtl',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.max' => 'کد زبان نباید بیشتر از ۵ کاراکتر باشد',
            'code.unique' => 'کد زبان قبلاً ثبت شده است',
            'code.regex' => 'کد زبان باید با حروف کوچک انگلیسی باشد',
            
            'name.max' => 'نام زبان نباید بیشتر از ۱۰۰ کاراکتر باشد',
            'name.unique' => 'نام زبان قبلاً ثبت شده است',
            
            'native_name.max' => 'نام بومی زبان نباید بیشتر از ۱۰۰ کاراکتر باشد',
            
            'direction.in' => 'جهت نوشتار باید یکی از مقادیر ltr یا rtl باشد',
            
            'is_active.boolean' => 'وضعیت فعال باید مقدار true یا false باشد',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تبدیل به حروف کوچک
        if ($this->has('code')) {
            $this->merge([
                'code' => strtolower($this->code),
            ]);
        }
    }
}