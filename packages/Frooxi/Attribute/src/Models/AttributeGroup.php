<?php

namespace Frooxi\Attribute\Models;

use Frooxi\Attribute\Contracts\AttributeGroup as AttributeGroupContract;
use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model implements AttributeGroupContract
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'column',
        'position',
        'is_user_defined',
    ];

    /**
     * Get the attributes that owns the attribute group.
     */
    public function custom_attributes()
    {
        return $this->belongsToMany(AttributeProxy::modelClass(), 'attribute_group_mappings')
            ->withPivot('position')
            ->orderBy('pivot_position', 'asc');
    }
}
