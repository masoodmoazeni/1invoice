<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'country_code',
        'description',
        'flag',
        'status',
    ];

    public function states()
    {
        return $this->hasMany(State::class, 'country_id');
    }

}
