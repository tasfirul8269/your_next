<?php

namespace Frooxi\Product\Models;

use Frooxi\Product\Contracts\ProductDownloadableSampleTranslation as ProductDownloadableSampleTranslationContract;
use Illuminate\Database\Eloquent\Model;

class ProductDownloadableSampleTranslation extends Model implements ProductDownloadableSampleTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['title'];
}
