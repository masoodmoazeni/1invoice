<?php

namespace Modules\System\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class CreateCurrencyRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return [
        'code'=>['required','string','max:5','unique:currencies,code'], 'name'=>['required','string','max:100'],
        'symbol'=>['nullable','string','max:10'], 'decimal_places'=>['sometimes','integer','min:0','max:8'],
        'rounding'=>['sometimes','numeric','min:0'], 'is_active'=>['sometimes','boolean'],
    ]; }
}
