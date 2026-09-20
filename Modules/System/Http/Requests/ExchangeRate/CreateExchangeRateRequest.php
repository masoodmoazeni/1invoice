<?php

namespace Modules\System\Http\Requests\ExchangeRate;

use Illuminate\Foundation\Http\FormRequest;

class CreateExchangeRateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['from_currency_id'=>['required','integer','exists:currencies,id'],'to_currency_id'=>['required','integer','exists:currencies,id','different:from_currency_id'],'rate'=>['required','numeric','gt:0'],'effective_date'=>['required','date'],'source'=>['nullable','string','max:100']]; }
}
