<?php

namespace Modules\Setting\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Setting\Entities\FaqCategory;

/** @mixin FaqCategory */
class FaqCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'items_count' => $this->when(isset($this->items_count), $this->items_count),
            'items' => FaqItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
