<?php

namespace Frooxi\Core\Models;

use Frooxi\Core\Contracts\CountryTranslation as CountryTranslationContract;
use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model implements CountryTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['name'];
}
