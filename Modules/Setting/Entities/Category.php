<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ListBusiness\Entities\ListBusiness;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'status'
    ];

    protected $appends = ['slug'];

    public function getSlugAttribute()
    {
        $slug = mb_strtolower($this->title);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^\p{L}\p{N}-]+/u', '', $slug);
        return $slug;
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

}
