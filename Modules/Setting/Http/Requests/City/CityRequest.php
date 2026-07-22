<?php

namespace Modules\Setting\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country_id'    => 'nullable|integer',
            'state_id'      => 'nullable|integer',
            'name'          => 'required|string|max:255',
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
