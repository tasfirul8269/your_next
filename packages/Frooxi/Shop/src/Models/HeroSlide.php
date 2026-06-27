<?php

namespace Frooxi\Shop\Models;

use Frooxi\Category\Models\CategoryProxy;
use Frooxi\Core\Models\ChannelProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hero_slides';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'link',
        'category_id',
        'media_path',
        'sort_order',
        'status',
        'channel_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['media_url'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the channel that owns the hero slide.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(ChannelProxy::modelClass(), 'channel_id');
    }

    /**
     * Get the category that this slide links to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryProxy::modelClass(), 'category_id');
    }

    /**
     * Get the full media URL.
     */
    public function getMediaUrlAttribute(): string
    {
        return cloudinary_url($this->media_path);
    }
}
