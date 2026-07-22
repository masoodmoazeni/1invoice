<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ListBusiness\Entities\ListBusiness;

class SaleAdvantage extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'type',
        'title',
        'description',
        'status'
    ];

    public function saleLists()
    {
        return $this->hasMany(ListBusiness::class, 'sale_type_id', 'id');
    }

    public function occupancyLists()
    {
        return $this->hasMany(ListBusiness::class, 'occupancy_type_id', 'id');
    }

    public function BusinessLists()
    {
        return $this->hasMany(ListBusiness::class, 'business_type_id', 'id');
    }
}
