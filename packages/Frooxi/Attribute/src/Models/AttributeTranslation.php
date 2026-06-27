<?php

namespace Frooxi\Attribute\Models;

use Frooxi\Attribute\Contracts\AttributeTranslation as AttributeTranslationContract;
use Illuminate\Database\Eloquent\Model;

class AttributeTranslation extends Model implements AttributeTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['name'];
}
