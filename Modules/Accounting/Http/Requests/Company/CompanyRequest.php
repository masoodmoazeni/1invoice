<?php

namespace Modules\Accounting\Http\Requests\company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $companyId = $this->route('company') ? $this->route('company')->id : null;

        return [
            // فیلدهای اجباری
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('companies', 'code')->ignore($companyId),
            ],
            'name' => 'required|string|max:255',

            // فیلدهای nullable (اختیاری)
            'legal_name' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'base_currency_id' => 'nullable|exists:currencies,id',
            'language_id' => 'nullable|exists:languages,id',
            'timezone_id' => 'nullable|exists:timezones,id',

            // اطلاعات مالی و ثبتی
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',

            // اطلاعات تماس
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',

            // آدرس
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',

            // لوگو
            'logo' => 'nullable|string|max:255',

            // سال مالی
            'fiscal_year_start_month' => 'required|integer|min:1|max:12',

            // وضعیت
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required' => 'کد شرکت الزامی است.',
            'code.unique' => 'کد شرکت قبلاً ثبت شده است.',
            'name.required' => 'نام شرکت الزامی است.',
            'country_id.exists' => 'کشور انتخاب شده معتبر نیست.',
            'base_currency_id.exists' => 'ارز پایه انتخاب شده معتبر نیست.',
            'language_id.exists' => 'زبان انتخاب شده معتبر نیست.',
            'timezone_id.exists' => 'منطقه زمانی انتخاب شده معتبر نیست.',
            'email.email' => 'فرمت ایمیل وارد شده صحیح نیست.',
            'website.url' => 'فرمت وبسایت وارد شده صحیح نیست.',
            'fiscal_year_start_month.required' => 'ماه شروع سال مالی الزامی است.',
            'fiscal_year_start_month.min' => 'ماه شروع سال مالی باید بین 1 تا 12 باشد.',
            'fiscal_year_start_month.max' => 'ماه شروع سال مالی باید بین 1 تا 12 باشد.',
            'is_active.boolean' => 'وضعیت فعال باید true یا false باشد.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // تبدیل رشته‌های خالی به null برای فیلدهای nullable
        $this->merge(array_filter([
            'code' => $this->code,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'country_id' => $this->country_id,
            'base_currency_id' => $this->base_currency_id,
            'language_id' => $this->language_id,
            'timezone_id' => $this->timezone_id,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'state' => $this->state,
            'logo' => $this->logo,
            'fiscal_year_start_month' => $this->fiscal_year_start_month,
            'is_active' => $this->is_active,
        ], function ($value) {
            return $value !== '' && $value !== null;
        }));
    }
}