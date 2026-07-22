<?php

namespace Modules\Setting\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Setting\Entities\HeaderMenuItem;

class HeaderMenuAdminResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var HeaderMenuItem $item */
        $item = $this->resource;

        $data = [
            'id' => $item->id,
            'parent_id' => $item->parent_id,
            'label' => $item->label,
            'href' => $item->href,
            'description' => $item->description,
            'cols' => $item->cols,
            'is_dropdown' => $item->is_dropdown,
            'link_type' => $item->link_type,
            'page_id' => $item->page_id,
            'page_title' => $item->page?->title,
            'page_slug' => $item->page?->published_slug ?: $item->page?->slug,
            'sort_order' => $item->sort_order,
            'status' => $item->status,
            'resolved_href' => $item->resolveHref(),
        ];

        if ($item->is_dropdown) {
            $data['items'] = HeaderMenuAdminResource::collection($item->children)->resolve();
        }

        return $data;
    }
}
