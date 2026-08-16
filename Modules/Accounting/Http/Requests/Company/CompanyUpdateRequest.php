<?php

namespace Modules\Accounting\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $companyId = $this->route('id');

        return [
            'code' => 'sometimes|string|max:50|unique:companies,code,' . $companyId,
            'name' => 'sometimes|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'country_id' => 'nullable|integer|exists:countries,id',
            'base_currency_id' => 'nullable|integer|exists:currencies,id',
            'language_id' => 'nullable|integer|exists:languages,id',
            'timezone_id' => 'nullable|integer|exists:timezones,id',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'logo' => 'nullable|string|max:255',
            'fiscal_year_start_month' => 'sometimes|integer|min:1|max:12',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'کد شرکت قبلاً ثبت شده است',
            'code.max' => 'کد شرکت نباید بیشتر از ۵۰ کاراکتر باشد',
            'name.max' => 'نام شرکت نباید بیشتر از ۲۵۵ کاراکتر باشد',
            'fiscal_year_start_month.integer' => 'ماه شروع سال مالی باید عددی بین ۱ تا ۱۲ باشد',
            'fiscal_year_start_month.min' => 'ماه شروع سال مالی باید حداقل ۱ باشد',
            'fiscal_year_start_month.max' => 'ماه شروع سال مالی باید حداکثر ۱۲ باشد',
            'email.email' => 'آدرس ایمیل نامعتبر است',
            'website.url' => 'آدرس وبسایت نامعتبر است',
            'country_id.exists' => 'کشور انتخاب شده معتبر نیست',
            'base_currency_id.exists' => 'ارز پایه انتخاب شده معتبر نیست',
            'language_id.exists' => 'زبان انتخاب شده معتبر نیست',
            'timezone_id.exists' => 'منطقه زمانی انتخاب شده معتبر نیست',
        ];
    }
}