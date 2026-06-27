<?php

namespace Frooxi\Attribute\Models;

use Frooxi\Attribute\Contracts\AttributeOptionTranslation as AttributeOptionTranslationContract;
use Illuminate\Database\Eloquent\Model;

class AttributeOptionTranslation extends Model implements AttributeOptionTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['label'];
}
