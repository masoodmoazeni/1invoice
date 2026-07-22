<?php

namespace Modules\Setting\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Setting\Entities\HeaderMenuItem;

class HeaderMenuPublicResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var HeaderMenuItem $item */
        $item = $this->resource;

        if ($item->is_dropdown) {
            return [
                'label' => $item->label,
                'cols' => $item->cols,
                'items' => HeaderMenuPublicResource::collection(
                    $item->publishedChildren ?? $item->children
                )->resolve(),
            ];
        }

        return [
            'label' => $item->label,
            'href' => $item->resolveHref(),
            'description' => $item->description,
        ];
    }
}
