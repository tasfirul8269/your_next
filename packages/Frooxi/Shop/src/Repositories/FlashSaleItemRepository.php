<?php

namespace Frooxi\Shop\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Shop\Models\FlashSaleItem;
use Illuminate\Support\Collection;

class FlashSaleItemRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return FlashSaleItem::class;
    }

    /**
     * Get active flash sale items for a channel, ordered by sort_order.
     */
    public function getActiveItems(?int $channelId = null): Collection
    {
        $channelId = $channelId ?: core()->getCurrentChannel()->id;

        return $this->model
            ->where('status', 1)
            ->where('channel_id', $channelId)
            ->orderBy('sort_order')
            ->get();
    }
}
