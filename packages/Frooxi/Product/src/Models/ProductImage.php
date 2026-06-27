<?php

namespace Frooxi\Product\Models;

use Frooxi\Product\Contracts\ProductImage as ProductImageContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model implements ProductImageContract
{
    /**
     * Timestamp.
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
        'type',
        'path',
        'product_id',
        'position',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * Get the product that owns the image.
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get image url for the product image.
     *
     * @return string
     */
    public function url()
    {
        if (config('filesystems.default') === 'cloudinary') {
            return cloudinary_url($this->path);
        }

        return Storage::url($this->path);
    }

    /**
     * Get image url for the product image.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->url();
    }

    /**
     * Is custom attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function isCustomAttribute($attribute)
    {
        return $this->attribute_family->custom_attributes->pluck('code')->contains($attribute);
    }
}
