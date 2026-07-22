<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{

    protected $fillable = [
        'title',
        'slug',
        'featured_image',
        'content',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected $appends = ['image_url', 'og_image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->featured_image) {
            $path = public_path("images/blogs/{$this->featured_image}");
            if (file_exists($path)) {
                return asset("images/blogs/{$this->featured_image}");
            }
        }
    }

    public function getOgImageUrlAttribute()
    {
        if ($this->og_image) {
            $path = public_path("images/blogs/{$this->og_image}");
            if (file_exists($path)) {
                return asset("images/blogs/{$this->og_image}");
            }
        }

        return null;
    }
}
