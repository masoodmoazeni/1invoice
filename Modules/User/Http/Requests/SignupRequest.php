<?php

namespace Modules\User\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'firstname' => 'nullable|string|max:255',
            'lastname'  => 'nullable|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'mobile'    => 'required|string|min:10|max:15|unique:users,mobile',
            'password'      => 'required|string|min:6',
            'redirect_url'  => 'nullable|string',
        ];

        if (! empty(config('services.turnstile.secret_key'))) {
            $rules['turnstile_token'] = [new Turnstile()];
        }

        return $rules;
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
