<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FaqSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'schema',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'schema' => 'array',
    ];
}
