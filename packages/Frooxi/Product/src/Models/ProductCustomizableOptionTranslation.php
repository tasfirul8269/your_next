<?php

namespace Frooxi\Product\Models;

use Frooxi\Product\Contracts\ProductCustomizableOptionTranslation as ProductCustomizableOptionTranslationContract;
use Illuminate\Database\Eloquent\Model;

class ProductCustomizableOptionTranslation extends Model implements ProductCustomizableOptionTranslationContract
{
    /**
     * Set timestamp false.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Set fillable property to the model.
     *
     * @var array
     */
    protected $fillable = ['label'];
}
