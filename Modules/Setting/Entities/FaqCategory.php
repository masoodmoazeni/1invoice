<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'slug',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(FaqItem::class, 'faq_category_id');
    }

    public function publishedItems(): HasMany
    {
        return $this->items()
            ->where('status', FaqItem::STATUS_PUBLISHED)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
