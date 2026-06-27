<?php

namespace Frooxi\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'sort_order',
    ];

    /**
     * Get the active shipping methods.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1)->orderBy('sort_order');
    }
}
