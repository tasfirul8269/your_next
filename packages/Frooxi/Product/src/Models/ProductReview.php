<?php

namespace Frooxi\Product\Models;

use Frooxi\Customer\Models\CustomerProxy;
use Frooxi\Product\Contracts\ProductReview as ProductReviewContract;
use Frooxi\Product\Database\Factories\ProductReviewFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model implements ProductReviewContract
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'title',
        'rating',
        'status',
        'product_id',
        'customer_id',
        'name',
    ];

    /**
     * Get the product attribute family that owns the product.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * The images that belong to the review.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductReviewAttachmentProxy::modelClass(), 'review_id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ProductReviewFactory::new();
    }
}
