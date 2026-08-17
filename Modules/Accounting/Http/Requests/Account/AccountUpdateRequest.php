<?php

namespace Modules\Accounting\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
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
        $accountId = $this->route('id');
        $companyId = $this->company_id;

        return [
            'company_id' => 'sometimes|integer|exists:companies,id',
            'account_code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('accounts', 'account_code')
                    ->where('company_id', $companyId)
                    ->ignore($accountId),
            ],
            'account_name' => 'sometimes|string|max:100',
            'parent_id' => 'nullable|integer|exists:accounts,id',
            'account_category' => 'sometimes|string|in:asset,liability,equity,revenue,expense',
            'account_type' => 'sometimes|string|in:detail,header',
            'normal_balance' => 'sometimes|string|in:debit,credit',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'allow_posting' => 'sometimes|boolean',
            'is_system' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_id.integer' => 'شناسه شرکت باید عددی باشد',
            'company_id.exists' => 'شرکت انتخاب شده معتبر نیست',
            
            'account_code.max' => 'کد حساب نباید بیشتر از ۵۰ کاراکتر باشد',
            'account_code.unique' => 'کد حساب قبلاً در این شرکت ثبت شده است',
            
            'account_name.max' => 'نام حساب نباید بیشتر از ۱۰۰ کاراکتر باشد',
            
            'parent_id.integer' => 'شناسه حساب والد باید عددی باشد',
            'parent_id.exists' => 'حساب والد انتخاب شده معتبر نیست',
            
            'account_category.in' => 'دسته‌بندی حساب باید یکی از مقادیر asset, liability, equity, revenue, expense باشد',
            
            'account_type.in' => 'نوع حساب باید یکی از مقادیر detail یا header باشد',
            
            'normal_balance.in' => 'مانده عادی باید یکی از مقادیر debit یا credit باشد',
            
            'currency_id.integer' => 'شناسه ارز باید عددی باشد',
            'currency_id.exists' => 'ارز انتخاب شده معتبر نیست',
            
            'allow_posting.boolean' => 'وضعیت مجاز به ثبت سند باید true یا false باشد',
            'is_system.boolean' => 'وضعیت سیستمی باید true یا false باشد',
            'is_active.boolean' => 'وضعیت فعال باید true یا false باشد',
            'sort_order.integer' => 'ترتیب نمایش باید عددی باشد',
            'sort_order.min' => 'ترتیب نمایش نباید کمتر از ۰ باشد',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // اگر parent_id وجود دارد و null است
        if ($this->has('parent_id') && $this->parent_id === 'null') {
            $this->merge([
                'parent_id' => null,
            ]);
        }

        // اگر account_category تغییر کرده و normal_balance ارسال نشده است
        if ($this->has('account_category') && !$this->has('normal_balance')) {
            $normalBalance = $this->getDefaultNormalBalance($this->account_category);
            $this->merge([
                'normal_balance' => $normalBalance,
            ]);
        }
    }

    /**
     * Get default normal balance based on account category.
     */
    private function getDefaultNormalBalance(string $category): string
    {
        $normalBalances = [
            'asset' => 'debit',
            'liability' => 'credit',
            'equity' => 'credit',
            'revenue' => 'credit',
            'expense' => 'debit',
        ];

        return $normalBalances[$category] ?? 'debit';
    }
}