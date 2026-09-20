<?php

namespace Modules\Company\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'country_id' => $this->country_id,
            'base_currency_id' => $this->base_currency_id,
            'language_id' => $this->language_id,
            'timezone_id' => $this->timezone_id,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'state' => $this->state,
            'logo' => $this->logo,
            'logo_url' => $this->logo_url,
            'fiscal_year_start_month' => $this->fiscal_year_start_month,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
