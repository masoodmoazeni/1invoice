<?php

namespace Modules\Company\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FiscalYearResource extends JsonResource
{
    public function toArray($request): array { return $this->resource->toArray() + ['company' => new CompanyResource($this->whenLoaded('company')), 'status_text' => $this->status_text, 'full_name' => $this->full_name]; }
}
