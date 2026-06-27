<?php

namespace Frooxi\Customer\Models;

use Frooxi\Customer\Contracts\CustomerNote as CustomerNoteContract;
use Illuminate\Database\Eloquent\Model;

class CustomerNote extends Model implements CustomerNoteContract
{
    protected $fillable = [
        'note',
        'customer_id',
        'customer_notified',
    ];

    /**
     * Get the order record associated with the order comment.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }
}
