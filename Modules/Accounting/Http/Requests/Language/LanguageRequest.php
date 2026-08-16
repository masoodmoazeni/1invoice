<?php

namespace Modules\Accounting\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
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
            'code' => 'required|string|max:5|unique:languages,code|regex:/^[a-z]{2,5}$/',
            'name' => 'required|string|max:100|unique:languages,name',
            'native_name' => 'nullable|string|max:100',
            'direction' => 'required|string|in:ltr,rtl',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'کد زبان الزامی است',
            'code.max' => 'کد زبان نباید بیشتر از ۵ کاراکتر باشد',
            'code.unique' => 'کد زبان قبلاً ثبت شده است',
            'code.regex' => 'کد زبان باید با حروف کوچک انگلیسی باشد',
            
            'name.required' => 'نام زبان الزامی است',
            'name.max' => 'نام زبان نباید بیشتر از ۱۰۰ کاراکتر باشد',
            'name.unique' => 'نام زبان قبلاً ثبت شده است',
            
            'native_name.max' => 'نام بومی زبان نباید بیشتر از ۱۰۰ کاراکتر باشد',
            
            'direction.required' => 'جهت نوشتار الزامی است',
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

        // مقدار پیش‌فرض برای direction
        if (!$this->has('direction')) {
            $this->merge([
                'direction' => 'ltr',
            ]);
        }

        // مقدار پیش‌فرض برای is_active
        if (!$this->has('is_active')) {
            $this->merge([
                'is_active' => true,
            ]);
        }
    }
}