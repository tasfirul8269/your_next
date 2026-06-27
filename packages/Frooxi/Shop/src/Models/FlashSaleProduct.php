<?php

namespace Frooxi\Shop\Models;

use Frooxi\Core\Models\ChannelProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flash_sale_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'name',
        'description',
        'price',
        'discount_percentage',
        'quantity',
        'image_path',
        'status',
        'channel_id',
        'sort_order',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', 'discounted_price'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'quantity' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the channel that owns the flash sale product.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(ChannelProxy::modelClass(), 'channel_id');
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? cloudinary_url($this->image_path) : '';
    }

    /**
     * Get the discounted price based on discount percentage.
     */
    public function getDiscountedPriceAttribute(): float
    {
        return round($this->price * (1 - $this->discount_percentage / 100), 2);
    }
}
