<?php

namespace Modules\Company\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray($request): array { return $this->resource->toArray() + ['company' => new CompanyResource($this->whenLoaded('company')), 'country' => $this->whenLoaded('country')]; }
}
