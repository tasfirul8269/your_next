<?php

namespace Frooxi\Core\Models;

use Frooxi\Core\Contracts\CountryStateTranslation as CountryStateTranslationContract;
use Illuminate\Database\Eloquent\Model;

class CountryStateTranslation extends Model implements CountryStateTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['default_name'];
}
