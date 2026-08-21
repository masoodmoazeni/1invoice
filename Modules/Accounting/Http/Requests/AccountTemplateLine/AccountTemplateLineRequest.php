<?php

namespace Modules\Accounting\Http\Requests\AccountTemplateLine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Accounting\Models\AccountTemplateLine;

class AccountTemplateLineRequest extends FormRequest
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
            'template_id' => [
                'required',
                'integer',
                'exists:account_templates,id'
            ],
            'account_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[0-9]+(\.[0-9]+)*$/',
                Rule::unique('account_template_lines')
                    ->where('template_id', $this->input('template_id'))
            ],
            'account_name' => [
                'required',
                'string',
                'max:100',
                'min:2'
            ],
            'parent_code' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[0-9]+(\.[0-9]+)*$/'
            ],
            'category' => [
                'required',
                'string',
                Rule::in(['asset', 'liability', 'equity', 'revenue', 'expense'])
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['detail', 'header'])
            ],
            'normal_balance' => [
                'required',
                'string',
                Rule::in(['debit', 'credit'])
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'template_id.required' => 'شناسه قالب الزامی است',
            'template_id.integer' => 'شناسه قالب باید عدد باشد',
            'template_id.exists' => 'قالب انتخاب شده معتبر نیست',

            'account_code.required' => 'کد حساب الزامی است',
            'account_code.string' => 'کد حساب باید رشته باشد',
            'account_code.max' => 'کد حساب نباید بیشتر از ۵۰ کاراکتر باشد',
            'account_code.regex' => 'فرمت کد حساب نامعتبر است (مثال: 1 یا 1.1 یا 1.1.1)',
            'account_code.unique' => 'این کد حساب قبلاً در این قالب ثبت شده است',

            'account_name.required' => 'نام حساب الزامی است',
            'account_name.string' => 'نام حساب باید رشته باشد',
            'account_name.max' => 'نام حساب نباید بیشتر از ۱۰۰ کاراکتر باشد',
            'account_name.min' => 'نام حساب باید حداقل ۲ کاراکتر باشد',

            'parent_code.string' => 'کد حساب والد باید رشته باشد',
            'parent_code.max' => 'کد حساب والد نباید بیشتر از ۵۰ کاراکتر باشد',
            'parent_code.regex' => 'فرمت کد حساب والد نامعتبر است',

            'category.required' => 'دسته‌بندی حساب الزامی است',
            'category.in' => 'دسته‌بندی انتخاب شده معتبر نیست (asset, liability, equity, revenue, expense)',

            'type.required' => 'نوع حساب الزامی است',
            'type.in' => 'نوع حساب باید detail یا header باشد',

            'normal_balance.required' => 'مانده عادی حساب الزامی است',
            'normal_balance.in' => 'مانده عادی باید debit یا credit باشد'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // حذف فاصله‌های اضافی
        if ($this->has('account_code')) {
            $this->merge([
                'account_code' => trim($this->account_code)
            ]);
        }

        if ($this->has('account_name')) {
            $this->merge([
                'account_name' => trim($this->account_name)
            ]);
        }

        if ($this->has('parent_code')) {
            $this->merge([
                'parent_code' => trim($this->parent_code)
            ]);
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'template_id' => 'شناسه قالب',
            'account_code' => 'کد حساب',
            'account_name' => 'نام حساب',
            'parent_code' => 'کد حساب والد',
            'category' => 'دسته‌بندی حساب',
            'type' => 'نوع حساب',
            'normal_balance' => 'مانده عادی'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateParentCode($validator);
            $this->validateHierarchyLevel($validator);
            $this->validateCodeFormat($validator);
        });
    }

    /**
     * اعتبارسنجی کد والد
     */
    protected function validateParentCode($validator): void
    {
        $templateId = $this->input('template_id');
        $parentCode = $this->input('parent_code');
        $accountCode = $this->input('account_code');

        // اگر کد والد وارد شده باشد
        if ($parentCode) {
            // بررسی وجود کد والد در قالب
            $exists = AccountTemplateLine::where('template_id', $templateId)
                ->where('account_code', $parentCode)
                ->exists();

            if (!$exists) {
                $validator->errors()->add('parent_code', 'کد والد وارد شده در این قالب وجود ندارد');
            }

            // بررسی اینکه کد والد با کد حساب یکی نباشد
            if ($parentCode === $accountCode) {
                $validator->errors()->add('parent_code', 'کد حساب والد نمی‌تواند با کد حساب برابر باشد');
            }

            // بررسی اینکه کد والد سطح پایین‌تری داشته باشد
            $parentLevel = substr_count($parentCode, '.') + 1;
            $accountLevel = substr_count($accountCode, '.') + 1;
            
            if ($accountLevel <= $parentLevel) {
                $validator->errors()->add('parent_code', 'سطح کد حساب والد باید از سطح کد حساب فعلی کمتر باشد');
            }

            // بررسی اینکه کد والد با کد حساب شروع شود
            if (!str_starts_with($accountCode, $parentCode . '.')) {
                $validator->errors()->add('parent_code', 'کد حساب باید با کد والد شروع شود');
            }
        } else {
            // اگر کد والد خالی است، کد حساب باید در سطح اول باشد
            $level = substr_count($accountCode, '.') + 1;
            if ($level > 1) {
                $validator->errors()->add('parent_code', 'برای کد حساب‌های با سطح بالاتر، کد والد الزامی است');
            }
        }
    }

    /**
     * اعتبارسنجی سطح سلسله‌مراتبی
     */
    protected function validateHierarchyLevel($validator): void
    {
        $accountCode = $this->input('account_code');
        $maxLevel = 10; // حداکثر سطح مجاز

        $level = substr_count($accountCode, '.') + 1;
        
        if ($level > $maxLevel) {
            $validator->errors()->add('account_code', "سطح کد حساب نمی‌تواند بیشتر از {$maxLevel} باشد");
        }
    }

    /**
     * اعتبارسنجی فرمت کد حساب
     */
    protected function validateCodeFormat($validator): void
    {
        $accountCode = $this->input('account_code');
        
        // بررسی اینکه کد حساب با عدد شروع شود و به عدد ختم شود
        if (!preg_match('/^[0-9]+(\.[0-9]+)*$/', $accountCode)) {
            $validator->errors()->add('account_code', 'فرمت کد حساب نامعتبر است (مثال: 1, 1.1, 1.1.1)');
        }

        // بررسی عدم وجود نقطه در ابتدا یا انتها
        if (str_starts_with($accountCode, '.') || str_ends_with($accountCode, '.')) {
            $validator->errors()->add('account_code', 'کد حساب نمی‌تواند با نقطه شروع یا خاتمه یابد');
        }

        // بررسی عدم وجود نقطه‌های تکراری
        if (str_contains($accountCode, '..')) {
            $validator->errors()->add('account_code', 'کد حساب نمی‌تواند شامل نقطه‌های تکراری باشد');
        }
    }
}