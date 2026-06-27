<?php

namespace Frooxi\Sales\Models;

use Frooxi\Sales\Contracts\OrderComment as OrderCommentContract;
use Illuminate\Database\Eloquent\Model;

class OrderComment extends Model implements OrderCommentContract
{
    protected $fillable = [
        'comment',
        'customer_notified',
        'order_id',
    ];

    /**
     * Get the order record associated with the order comment.
     */
    public function order()
    {
        return $this->belongsTo(OrderProxy::modelClass());
    }
}
