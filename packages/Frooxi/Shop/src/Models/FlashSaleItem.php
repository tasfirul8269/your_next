<?php

namespace Frooxi\Shop\Models;

use Frooxi\Core\Models\ChannelProxy;
use Frooxi\Product\Models\ProductProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flash_sale_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'link',
        'product_id',
        'sort_order',
        'status',
        'channel_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the channel that owns the flash sale item.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(ChannelProxy::modelClass(), 'channel_id');
    }

    /**
     * Get the product that this flash sale item links to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass(), 'product_id');
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? cloudinary_url($this->image_path) : '';
    }
}
