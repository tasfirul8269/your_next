<?php

namespace Frooxi\Customer\Models;

use Frooxi\Customer\Contracts\CompareItem as CompareItemContract;
use Frooxi\Customer\Database\Factories\CompareItemFactory;
use Frooxi\Product\Models\ProductProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompareItem extends Model implements CompareItemContract
{
    use HasFactory;

    /**
     * Guarded
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'compare_items';

    /**
     * The customer that belong to the compare product.
     *
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass(), 'customer_id');
    }

    /**
     * The product that belong to the compare product.
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductProxy::modelClass(), 'product_id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return CompareItemFactory::new();
    }
}
