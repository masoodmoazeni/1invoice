<?php

namespace Modules\System\Http\Requests\ExchangeRate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExchangeRateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['from_currency_id'=>['sometimes','integer','exists:currencies,id'],'to_currency_id'=>['sometimes','integer','exists:currencies,id'],'rate'=>['sometimes','numeric','gt:0'],'effective_date'=>['sometimes','date'],'source'=>['nullable','string','max:100']]; }
}
