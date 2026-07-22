<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagebuilder_pages';

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_TEMP      = 'temp';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED  = 'archived';

    protected $fillable = [
        'title',
        'slug',
        'type',
        'image',
        'content',
        'root',
        'published_content',
        'published_root',
        'published_title',
        'published_slug',
        'status',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'content'           => 'array',
        'root'              => 'array',
        'published_content' => 'array',
        'published_root'    => 'array',
        'published_at'      => 'datetime',
        'is_published'      => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'is_live_published',
        'has_unpublished_changes',
    ];

    protected $hidden = [
        'is_published',
    ];

    public function scopePublished($query)
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_content')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            $path = public_path("images/pagebuilder/{$this->image}");
            if (file_exists($path)) {
                return asset("images/pagebuilder/{$this->image}");
            }
        }

        return asset('images/default-avatar.jpg');
    }

    public function getIsLivePublishedAttribute(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_content !== null;
    }

    public function getHasUnpublishedChangesAttribute(): bool
    {
        if (!$this->is_live_published) {
            return false;
        }

        return $this->encodeForComparison($this->content) !== $this->encodeForComparison($this->published_content)
            || $this->encodeForComparison($this->root) !== $this->encodeForComparison($this->published_root)
            || $this->title !== $this->published_title
            || $this->slug !== $this->published_slug;
    }

    public function getLiveSlug(): ?string
    {
        return $this->published_slug ?? $this->slug;
    }

    public function toPublishedPayload(): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->published_title ?? $this->title,
            'slug'         => $this->getLiveSlug(),
            'type'         => $this->type,
            'image'        => $this->image,
            'image_url'    => $this->image_url,
            'content'      => $this->published_content ?? $this->content,
            'root'         => $this->published_root ?? $this->root,
            'status'       => self::STATUS_PUBLISHED,
            'published_at' => $this->published_at,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }

    private function encodeForComparison(mixed $value): string
    {
        return json_encode($value ?? []);
    }
}
