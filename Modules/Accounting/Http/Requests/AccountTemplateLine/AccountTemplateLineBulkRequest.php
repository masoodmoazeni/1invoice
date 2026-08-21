<?php

namespace Modules\Accounting\Http\Requests\AccountTemplateLine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountTemplateLineBulkRequest extends FormRequest
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
            'lines' => [
                'required',
                'array',
                'min:1',
                'max:100' // حداکثر ۱۰۰ ردیف در هر درخواست
            ],
            'lines.*.account_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[0-9]+(\.[0-9]+)*$/',
                Rule::unique('account_template_lines')
                    ->where('template_id', $this->input('template_id'))
            ],
            'lines.*.account_name' => [
                'required',
                'string',
                'max:100',
                'min:2'
            ],
            'lines.*.parent_code' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[0-9]+(\.[0-9]+)*$/'
            ],
            'lines.*.category' => [
                'required',
                'string',
                Rule::in(['asset', 'liability', 'equity', 'revenue', 'expense'])
            ],
            'lines.*.type' => [
                'required',
                'string',
                Rule::in(['detail', 'header'])
            ],
            'lines.*.normal_balance' => [
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
            'template_id.exists' => 'قالب انتخاب شده معتبر نیست',
            
            'lines.required' => 'لیست ردیف‌ها الزامی است',
            'lines.array' => 'لیست ردیف‌ها باید به صورت آرایه باشد',
            'lines.min' => 'حداقل یک ردیف باید وارد شود',
            'lines.max' => 'حداکثر ۱۰۰ ردیف مجاز است',
            
            'lines.*.account_code.required' => 'کد حساب برای ردیف شماره {position} الزامی است',
            'lines.*.account_code.unique' => 'کد حساب برای ردیف شماره {position} تکراری است',
            'lines.*.account_code.regex' => 'فرمت کد حساب برای ردیف شماره {position} نامعتبر است',
            
            'lines.*.account_name.required' => 'نام حساب برای ردیف شماره {position} الزامی است',
            'lines.*.account_name.max' => 'نام حساب برای ردیف شماره {position} نباید بیشتر از ۱۰۰ کاراکتر باشد',
            'lines.*.account_name.min' => 'نام حساب برای ردیف شماره {position} باید حداقل ۲ کاراکتر باشد',
            
            'lines.*.category.required' => 'دسته‌بندی حساب برای ردیف شماره {position} الزامی است',
            'lines.*.category.in' => 'دسته‌بندی حساب برای ردیف شماره {position} معتبر نیست',
            
            'lines.*.type.required' => 'نوع حساب برای ردیف شماره {position} الزامی است',
            'lines.*.type.in' => 'نوع حساب برای ردیف شماره {position} معتبر نیست',
            
            'lines.*.normal_balance.required' => 'مانده عادی برای ردیف شماره {position} الزامی است',
            'lines.*.normal_balance.in' => 'مانده عادی برای ردیف شماره {position} معتبر نیست'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('lines')) {
            $lines = $this->lines;
            foreach ($lines as $key => $line) {
                // حذف فاصله‌های اضافی
                if (isset($line['account_code'])) {
                    $lines[$key]['account_code'] = trim($line['account_code']);
                }
                if (isset($line['account_name'])) {
                    $lines[$key]['account_name'] = trim($line['account_name']);
                }
                if (isset($line['parent_code'])) {
                    $lines[$key]['parent_code'] = trim($line['parent_code']);
                }
            }
            $this->merge(['lines' => $lines]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $templateId = $this->input('template_id');
            $lines = $this->input('lines');

            if ($lines) {
                foreach ($lines as $index => $line) {
                    $this->validateParentCodeForBulk($validator, $templateId, $line, $index);
                    $this->validateHierarchyLevelForBulk($validator, $line, $index);
                    $this->validateUniqueCodes($validator, $lines, $index);
                }
            }
        });
    }

    /**
     * اعتبارسنجی کد والد برای درخواست گروهی
     */
    protected function validateParentCodeForBulk($validator, int $templateId, array $line, int $index): void
    {
        $parentCode = $line['parent_code'] ?? null;
        $accountCode = $line['account_code'] ?? null;

        if ($parentCode) {
            // بررسی وجود کد والد در قالب
            $exists = \Modules\Accounting\Models\AccountTemplateLine::where('template_id', $templateId)
                ->where('account_code', $parentCode)
                ->exists();

            if (!$exists) {
                $validator->errors()->add(
                    "lines.{$index}.parent_code",
                    "کد والد وارد شده برای ردیف شماره " . ($index + 1) . " در این قالب وجود ندارد"
                );
            }

            // بررسی اینکه کد والد با کد حساب یکی نباشد
            if ($parentCode === $accountCode) {
                $validator->errors()->add(
                    "lines.{$index}.parent_code",
                    "کد حساب والد نمی‌تواند با کد حساب برای ردیف شماره " . ($index + 1) . " برابر باشد"
                );
            }

            // بررسی سطح
            if ($accountCode) {
                $parentLevel = substr_count($parentCode, '.') + 1;
                $accountLevel = substr_count($accountCode, '.') + 1;
                
                if ($accountLevel <= $parentLevel) {
                    $validator->errors()->add(
                        "lines.{$index}.parent_code",
                        "سطح کد حساب والد باید از سطح کد حساب فعلی برای ردیف شماره " . ($index + 1) . " کمتر باشد"
                    );
                }

                if (!str_starts_with($accountCode, $parentCode . '.')) {
                    $validator->errors()->add(
                        "lines.{$index}.parent_code",
                        "کد حساب برای ردیف شماره " . ($index + 1) . " باید با کد والد شروع شود"
                    );
                }
            }
        }
    }

    /**
     * اعتبارسنجی سطح سلسله‌مراتبی برای درخواست گروهی
     */
    protected function validateHierarchyLevelForBulk($validator, array $line, int $index): void
    {
        $accountCode = $line['account_code'] ?? null;
        $maxLevel = 10;

        if ($accountCode) {
            $level = substr_count($accountCode, '.') + 1;
            
            if ($level > $maxLevel) {
                $validator->errors()->add(
                    "lines.{$index}.account_code",
                    "سطح کد حساب برای ردیف شماره " . ($index + 1) . " نمی‌تواند بیشتر از {$maxLevel} باشد"
                );
            }
        }
    }

    /**
     * اعتبارسنجی کدهای تکراری در درخواست گروهی
     */
    protected function validateUniqueCodes($validator, array $lines, int $currentIndex): void
    {
        $currentCode = $lines[$currentIndex]['account_code'] ?? null;
        
        if ($currentCode) {
            for ($i = 0; $i < $currentIndex; $i++) {
                if (isset($lines[$i]['account_code']) && $lines[$i]['account_code'] === $currentCode) {
                    $validator->errors()->add(
                        "lines.{$currentIndex}.account_code",
                        "کد حساب برای ردیف شماره " . ($currentIndex + 1) . " با ردیف شماره " . ($i + 1) . " تکراری است"
                    );
                    break;
                }
            }
        }
    }
}