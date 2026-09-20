<?php

namespace Modules\System\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;

class CreateLanguageRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['code'=>['required','string','max:5','regex:/^[a-z]{2,5}$/','unique:languages,code'],'name'=>['required','string','max:100','unique:languages,name'],'native_name'=>['nullable','string','max:100'],'direction'=>['sometimes','in:ltr,rtl'],'is_active'=>['sometimes','boolean']]; }
    protected function prepareForValidation(): void { if($this->has('code')) $this->merge(['code'=>strtolower($this->code)]); }
}
