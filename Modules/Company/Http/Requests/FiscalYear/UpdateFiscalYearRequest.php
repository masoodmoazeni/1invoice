<?php

namespace Modules\Company\Http\Requests\FiscalYear;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFiscalYearRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'name' => ['sometimes', 'string', 'max:100'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'status' => ['sometimes', Rule::in(['open', 'closed', 'pending', 'locked'])],
            'is_default' => ['sometimes', 'boolean'],
            'lock_date' => ['nullable', 'date'],
        ];
    }
}
