<?php

namespace Modules\System\Http\Requests\TimeZone;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTimeZoneRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { $id=$this->route('time_zone') ?? $this->route('time-zone') ?? $this->route('id'); $id=is_object($id)?$id->id:$id; return ['country_id'=>['sometimes','integer','exists:countries,id'],'name'=>['sometimes','string','max:100',Rule::unique('timezones','name')->ignore($id)],'utc_offset'=>['sometimes','regex:/^[+-](0[0-9]|1[0-4]):[0-5][0-9]$/'],'is_default'=>['sometimes','boolean']]; }
}
