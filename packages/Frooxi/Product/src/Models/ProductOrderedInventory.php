<?php

namespace Frooxi\Product\Models;

use Frooxi\Core\Models\ChannelProxy;
use Frooxi\Product\Contracts\ProductOrderedInventory as ProductOrderedInventoryContract;
use Frooxi\Product\Database\Factories\ProductOrderedInventoryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOrderedInventory extends Model implements ProductOrderedInventoryContract
{
    use HasFactory;

    /**
     * Timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'qty',
        'product_id',
        'channel_id',
    ];

    /**
     * Get the channel owns the inventory.
     *
     * @return BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(ChannelProxy::modelClass());
    }

    /**
     * Get the product that owns the product inventory.
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ProductOrderedInventoryFactory::new();
    }
}
