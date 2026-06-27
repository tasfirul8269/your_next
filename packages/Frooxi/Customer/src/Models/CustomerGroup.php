<?php

namespace Frooxi\Customer\Models;

use Frooxi\Customer\Contracts\CustomerGroup as CustomerGroupContract;
use Frooxi\Customer\Database\Factories\CustomerGroupFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model implements CustomerGroupContract
{
    use HasFactory;

    /**
     * Deinfine model table name.
     *
     * @var string
     */
    protected $table = 'customer_groups';

    /**
     * Fillable property for the model.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'is_user_defined',
    ];

    /**
     * Get the customers for this group.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(CustomerProxy::modelClass());
    }

    /**
     * Create a new factory instance for the model
     */
    protected static function newFactory(): Factory
    {
        return CustomerGroupFactory::new();
    }
}
