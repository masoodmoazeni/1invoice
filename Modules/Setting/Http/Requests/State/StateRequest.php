<?php

namespace Modules\Setting\Http\Requests\State;

use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
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
            'state_code'    => 'nullable|string|max:10',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'disclaimer'    => 'nullable|string',
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
