<?php

namespace Modules\Setting\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Setting\Entities\FaqCategory;

/** @mixin FaqCategory */
class FaqPublicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'items' => FaqItemPublicResource::collection($this->whenLoaded('publishedItems')),
        ];
    }
}
