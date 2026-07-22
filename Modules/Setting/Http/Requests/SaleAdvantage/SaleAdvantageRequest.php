<?php

namespace Modules\Setting\Http\Requests\SaleAdvantage;

use Illuminate\Foundation\Http\FormRequest;

class SaleAdvantageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'         => 'string|max:255',
            'description'   => 'nullable|string',
            'status'        => 'nullable|integer',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
