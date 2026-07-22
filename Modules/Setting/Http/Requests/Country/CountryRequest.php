<?php

namespace Modules\Setting\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|string|max:255',
            'country_code'  => 'nullable|string|max:10',
            'description'   => 'nullable|string',
            'flag'          => 'nullable|string',
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
