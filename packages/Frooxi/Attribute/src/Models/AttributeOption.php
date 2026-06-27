<?php

namespace Frooxi\Attribute\Models;

use Frooxi\Attribute\Contracts\AttributeOption as AttributeOptionContract;
use Frooxi\Attribute\Database\Factories\AttributeOptionFactory;
use Frooxi\Core\Eloquent\TranslatableModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeOption extends TranslatableModel implements AttributeOptionContract
{
    use HasFactory;

    public $timestamps = false;

    public $translatedAttributes = ['label'];

    protected $fillable = [
        'admin_name',
        'swatch_value',
        'sort_order',
        'attribute_id',
    ];

    /**
     * Append to the model attributes
     *
     * @var array
     */
    protected $appends = [
        'swatch_value_url',
    ];

    /**
     * Get the attribute that owns the attribute option.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(AttributeProxy::modelClass());
    }

    /**
     * Get image url for the swatch value url.
     */
    public function swatch_value_url()
    {
        if (
            $this->swatch_value
            && $this->attribute->swatch_type == 'image'
        ) {
            return url('cache/small/'.$this->swatch_value);
        }

        return null;
    }

    /**
     * Get image url for the product image.
     */
    public function getSwatchValueUrlAttribute()
    {
        return $this->swatch_value_url();
    }

    /**
     * Create a new factory instance for the model
     */
    protected static function newFactory(): Factory
    {
        return AttributeOptionFactory::new();
    }
}
