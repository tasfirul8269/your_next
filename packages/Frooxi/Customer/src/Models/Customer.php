<?php

namespace Frooxi\Customer\Models;

use Frooxi\Checkout\Models\CartProxy;
use Frooxi\Core\Models\ChannelProxy;
use Frooxi\Core\Models\SubscribersListProxy;
use Frooxi\Customer\Contracts\Customer as CustomerContract;
use Frooxi\Customer\Database\Factories\CustomerFactory;
use Frooxi\Product\Models\ProductReviewProxy;
use Frooxi\Sales\Models\InvoiceProxy;
use Frooxi\Sales\Models\OrderProxy;
use Frooxi\Shop\Mail\Customer\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements CustomerContract
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'subscribed_to_news_letter' => 'boolean',
        'otp_expires_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'password',
        'api_token',
        'token',
        'customer_group_id',
        'channel_id',
        'subscribed_to_news_letter',
        'status',
        'is_verified',
        'is_suspended',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Get the name of the unique identifier for authentication.
     * Use phone instead of email for login.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'phone';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get image url for the customer profile.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }

    /**
     * Get the customer full name.
     */
    public function getNameAttribute(): string
    {
        return ucfirst($this->first_name).' '.ucfirst($this->last_name);
    }

    /**
     * Get image url for the customer image.
     *
     * @return string|null
     */
    public function image_url()
    {
        if (! $this->image) {
            return;
        }

        return Storage::url($this->image);
    }

    /**
     * Is email exists or not.
     *
     * @param  string  $email
     */
    public function emailExists($email): bool
    {
        $results = $this->where('email', $email);

        if ($results->count() === 0) {
            return false;
        }

        return true;
    }

    /**
     * Get the customer group that owns the customer.
     *
     * @return BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(CustomerGroupProxy::modelClass(), 'customer_group_id');
    }

    /**
     * Get the customer address that owns the customer.
     *
     * @return HasMany
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddressProxy::modelClass(), 'customer_id');
    }

    /**
     * Get default customer address that owns the customer.
     *
     * @return HasOne
     */
    public function default_address()
    {
        return $this->hasOne(CustomerAddressProxy::modelClass(), 'customer_id')
            ->where('default_address', 1);
    }

    /**
     * Customer's relation with invoice .
     *
     * @return hasManyThrough
     */
    public function invoices()
    {
        return $this->hasManyThrough(InvoiceProxy::modelClass(), OrderProxy::modelClass());
    }

    /**
     * Customer's relation with wishlist items.
     *
     * @return HasMany
     */
    public function wishlist_items()
    {
        return $this->hasMany(WishlistProxy::modelClass(), 'customer_id');
    }

    /**
     * Is wishlist shared by the customer.
     */
    public function isWishlistShared(): bool
    {
        return (bool) $this->wishlist_items()->where('shared', 1)->first();
    }

    /**
     * Get wishlist shared link.
     *
     * @return string|null
     */
    public function getWishlistSharedLink()
    {
        return $this->isWishlistShared()
            ? URL::signedRoute('shop.customer.wishlist.shared', ['id' => $this->id])
            : null;
    }

    /**
     * Get all cart inactive cart instance of a customer.
     *
     * @return HasMany
     */
    public function all_carts()
    {
        return $this->hasMany(CartProxy::modelClass(), 'customer_id');
    }

    /**
     * Get inactive cart instance of a customer.
     *
     * @return HasMany
     */
    public function inactive_carts()
    {
        return $this->hasMany(CartProxy::modelClass(), 'customer_id')
            ->where('is_active', 0);
    }

    /**
     * Get active cart instance of a customer.
     *
     * @return HasMany
     */
    public function active_carts()
    {
        return $this->hasMany(CartProxy::modelClass(), 'customer_id')
            ->where('is_active', 1);
    }

    /**
     * Get all orders of a customer.
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(OrderProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all reviews of a customer.
     *
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(ProductReviewProxy::modelClass(), 'customer_id');
    }

    /**
     * Get all notes of a customer.
     *
     * @return HasMany
     */
    public function notes()
    {
        return $this->hasMany(CustomerNoteProxy::modelClass(), 'customer_id');
    }

    /**
     * Get the customer's subscription.
     *
     * @return HasOne
     */
    public function subscription()
    {
        return $this->hasOne(SubscribersListProxy::modelClass(), 'customer_id');
    }

    /**
     * Get the channel that owns the customer.
     *
     * @return BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(ChannelProxy::modelClass(), 'channel_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return CustomerFactory
     */
    protected static function newFactory()
    {
        return CustomerFactory::new();
    }
}
