<?php

namespace Modules\Setting\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'featured_image' => $this->featured_image,
            'content' => $this->content_preview ?? $this->content ?? '',
            'status' => $this->status,
            'published_at' => $this->published_at,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'og_image' => $this->og_image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image_url' => $this->image_url,
            'og_image_url' => $this->og_image_url,
        ];
    }
}
