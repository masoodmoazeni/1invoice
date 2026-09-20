<?php

namespace Modules\Company\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray($request): array { return $this->resource->toArray() + ['company' => new CompanyResource($this->whenLoaded('company')), 'parent' => new self($this->whenLoaded('parent'))]; }
}
