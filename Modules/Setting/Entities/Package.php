<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'packages';

    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'type_package',
        'type_stripe',
        'purchaseType',
        'minQuantity',
        'price',
        'monthly_price',
        'currency',
        'stripe_product_id',
        'stripe_price_id',
        'metadata',
        'main_package',
        'status'
    ];
}
