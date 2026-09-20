<?php

namespace Modules\Company\Http\Requests\FiscalYear;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFiscalYearRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['sometimes', Rule::in(['open', 'closed', 'pending', 'locked'])],
            'is_default' => ['sometimes', 'boolean'],
            'lock_date' => ['nullable', 'date'],
        ];
    }
}
