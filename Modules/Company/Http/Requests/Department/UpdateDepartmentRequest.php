<?php

namespace Modules\Company\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $department = $this->route('department') ?? $this->route('id');
        $id = is_object($department) ? $department->id : $department;
        $companyId = $this->input('company_id', optional($this->route('department'))->company_id);

        return [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('departments', 'code')->ignore($id)->where(fn ($q) => $q->where('company_id', $companyId))],
            'name' => ['sometimes', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:departments,id', Rule::notIn([$id])],
            'manager_id' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
