<?php

namespace Modules\System\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        $id=$this->route('currency') ?? $this->route('id'); $id=is_object($id)?$id->id:$id;
        return ['code'=>['sometimes','string','max:5',Rule::unique('currencies','code')->ignore($id)],'name'=>['sometimes','string','max:100'],
            'symbol'=>['nullable','string','max:10'],'decimal_places'=>['sometimes','integer','min:0','max:8'],
            'rounding'=>['sometimes','numeric','min:0'],'is_active'=>['sometimes','boolean']];
    }
}
