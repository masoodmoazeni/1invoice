<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeaderMenuItem extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public const LINK_TYPE_CUSTOM = 'custom';
    public const LINK_TYPE_PAGE_BUILDER = 'page_builder';

    protected $fillable = [
        'parent_id',
        'label',
        'href',
        'description',
        'cols',
        'is_dropdown',
        'link_type',
        'page_id',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'cols' => 'integer',
        'is_dropdown' => 'boolean',
        'page_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function publishedChildren(): HasMany
    {
        return $this->children()
            ->where('status', self::STATUS_PUBLISHED);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function resolveHref(): ?string
    {
        if ($this->link_type === self::LINK_TYPE_PAGE_BUILDER && $this->page) {
            $slug = $this->page->published_slug ?: $this->page->slug;

            return $slug ? '/' . ltrim($slug, '/') : null;
        }

        return $this->href;
    }
}
