<?php

namespace Frooxi\Product\Models;

use Frooxi\Product\Contracts\ProductDownloadableLinkTranslation as ProductDownloadableLinkTranslationContract;
use Frooxi\Product\Database\Factories\ProductDownloadableLinkTranslationFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDownloadableLinkTranslation extends Model implements ProductDownloadableLinkTranslationContract
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['title'];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ProductDownloadableLinkTranslationFactory::new();
    }
}
