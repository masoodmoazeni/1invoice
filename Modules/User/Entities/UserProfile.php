<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'slug',
        'meta_title',
        'meta_description',
        'headline',
        'summary',
        'biography',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/default-avatar.jpg');
        }

        if (is_string($this->image) && filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset("images/teams/{$this->image}");
    }
}
