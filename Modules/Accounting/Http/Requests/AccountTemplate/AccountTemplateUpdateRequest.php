<?php

namespace Modules\Accounting\Http\Requests\AccountTemplate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountTemplateUpdateRequest extends FormRequest
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
        $templateId = $this->route('id');
        $countryId = $this->country_id;

        return [
            'country_id' => 'sometimes|integer|exists:countries,id',
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('account_templates', 'name')
                    ->where('country_id', $countryId)
                    ->ignore($templateId),
            ],
            'version' => 'sometimes|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'country_id.integer' => 'شناسه کشور باید عددی باشد',
            'country_id.exists' => 'کشور انتخاب شده معتبر نیست',
            
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
        // اگر country_id ارسال نشده، از مدل فعلی استفاده کن
        if (!$this->has('country_id')) {
            $template = $this->accountTemplateService->find($this->route('id'));
            if ($template) {
                $this->merge([
                    'country_id' => $template->country_id,
                ]);
            }
        }
    }
}