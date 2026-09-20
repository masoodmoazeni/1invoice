<?php

namespace Modules\System\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { $id=$this->route('language') ?? $this->route('id'); $id=is_object($id)?$id->id:$id; return ['code'=>['sometimes','string','max:5','regex:/^[a-z]{2,5}$/',Rule::unique('languages','code')->ignore($id)],'name'=>['sometimes','string','max:100',Rule::unique('languages','name')->ignore($id)],'native_name'=>['nullable','string','max:100'],'direction'=>['sometimes','in:ltr,rtl'],'is_active'=>['sometimes','boolean']]; }
    protected function prepareForValidation(): void { if($this->has('code')) $this->merge(['code'=>strtolower($this->code)]); }
}
