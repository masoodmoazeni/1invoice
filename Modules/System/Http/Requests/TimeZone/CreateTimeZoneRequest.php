<?php

namespace Modules\System\Http\Requests\TimeZone;

use Illuminate\Foundation\Http\FormRequest;

class CreateTimeZoneRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['country_id'=>['required','integer','exists:countries,id'],'name'=>['required','string','max:100','unique:timezones,name'],'utc_offset'=>['required','regex:/^[+-](0[0-9]|1[0-4]):[0-5][0-9]$/'],'is_default'=>['sometimes','boolean']]; }
}
