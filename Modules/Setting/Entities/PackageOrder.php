<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ListBusiness\Entities\ListBusiness;
use Modules\User\Entities\User;

class PackageOrder extends Model
{
    use SoftDeletes;

    protected $casts = [
        'items'        => 'array',
        'responsedata' => 'array',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected $table = 'package_orders';

    protected $fillable = [
        'user_id',
        'list_id',
        'package_type',
        'coupon_id',
        'coupon_code',
        'original_amount',
        'discount_amount',
        'total_amount',
        'stripe_session_id',
        'stripe_checkout_url',
        'stripe_customer_id',
        'stripe_subscription_id',
        'starts_at',
        'trial_ends_at',
        'ends_at',
        'status',
        'items',
        'responsedata'
    ];

    /**
     * Relation: Order belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation: Order belongs to a User
     */
    public function list()
    {
        return $this->belongsTo(ListBusiness::class, 'list_id');
    }

    /**
     * Relation: Order belongs to a Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

}
