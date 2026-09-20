<?php

namespace Modules\Company\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $branch = $this->route('branch') ?? $this->route('id');
        $id = is_object($branch) ? $branch->id : $branch;
        $companyId = $this->input('company_id', optional($this->route('branch'))->company_id);

        return [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('branches', 'code')->ignore($id)->where(fn ($q) => $q->where('company_id', $companyId))],
            'name' => ['sometimes', 'string', 'max:100'],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'manager_id' => ['nullable', 'integer'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
