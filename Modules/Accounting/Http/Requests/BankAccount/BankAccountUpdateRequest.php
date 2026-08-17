<?php

namespace Modules\Accounting\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $bankAccountId = $this->route('id');

        return [
            'company_id' => 'sometimes|required|integer|exists:companies,id',
            'bank_name' => 'sometimes|required|string|max:100',
            'branch_name' => 'nullable|string|max:100',
            'account_number' => 'sometimes|required|string|max:50|unique:bank_accounts,account_number,' . $bankAccountId,
            'iban' => 'nullable|string|max:34|unique:bank_accounts,iban,' . $bankAccountId,
            'swift' => 'nullable|string|max:11',
            'currency_id' => 'sometimes|required|integer|exists:currencies,id',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'company_id.required' => 'شناسه شرکت الزامی است',
            'company_id.exists' => 'شرکت انتخاب شده معتبر نیست',
            'bank_name.required' => 'نام بانک الزامی است',
            'bank_name.max' => 'نام بانک نباید بیشتر از 100 کاراکتر باشد',
            'branch_name.max' => 'نام شعبه نباید بیشتر از 100 کاراکتر باشد',
            'account_number.required' => 'شماره حساب الزامی است',
            'account_number.max' => 'شماره حساب نباید بیشتر از 50 کاراکتر باشد',
            'account_number.unique' => 'این شماره حساب قبلاً ثبت شده است',
            'iban.max' => 'شماره شبا نباید بیشتر از 34 کاراکتر باشد',
            'iban.unique' => 'این شماره شبا قبلاً ثبت شده است',
            'swift.max' => 'کد سوئیفت نباید بیشتر از 11 کاراکتر باشد',
            'currency_id.required' => 'شناسه ارز الزامی است',
            'currency_id.exists' => 'ارز انتخاب شده معتبر نیست',
            'is_default.boolean' => 'وضعیت پیش‌فرض باید مقدار بولین باشد',
            'is_active.boolean' => 'وضعیت فعال باید مقدار بولین باشد',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // حذف فیلدهایی که مقدار null دارند تا در به‌روزرسانی تغییر نکنند
        $data = $this->all();
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }
        $this->replace($data);
    }
}