<?php

namespace Frooxi\Core\Models;

use Frooxi\Core\Contracts\Locale as LocaleContract;
use Frooxi\Core\Database\Factories\LocaleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Locale extends Model implements LocaleContract
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'direction',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['logo_url'];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return LocaleFactory::new();
    }

    /**
     * Get the logo full path of the locale.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_url();
    }

    /**
     * Get the logo full path of the locale.
     *
     * @return string|void
     */
    public function logo_url()
    {
        if (empty($this->logo_path)) {
            return;
        }

        return Storage::url($this->logo_path);
    }
}
