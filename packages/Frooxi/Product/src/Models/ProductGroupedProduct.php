<?php

namespace Frooxi\Product\Models;

use Frooxi\Product\Contracts\ProductGroupedProduct as ProductGroupedProductContract;
use Frooxi\Product\Database\Factories\ProductGroupedProductFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroupedProduct extends Model implements ProductGroupedProductContract
{
    use HasFactory;

    /**
     * Set timestamp false.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Add fillable property to the model.
     *
     * @var array
     */
    protected $fillable = [
        'qty',
        'sort_order',
        'product_id',
        'associated_product_id',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get the product that owns the image.
     */
    public function associated_product()
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ProductGroupedProductFactory::new();
    }
}
