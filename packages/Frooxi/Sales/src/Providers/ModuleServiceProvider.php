<?php

namespace Frooxi\Sales\Providers;

use Frooxi\Core\Providers\CoreModuleServiceProvider;
use Frooxi\Sales\Models\DownloadableLinkPurchased;
use Frooxi\Sales\Models\Invoice;
use Frooxi\Sales\Models\InvoiceItem;
use Frooxi\Sales\Models\Order;
use Frooxi\Sales\Models\OrderAddress;
use Frooxi\Sales\Models\OrderComment;
use Frooxi\Sales\Models\OrderItem;
use Frooxi\Sales\Models\OrderPayment;
use Frooxi\Sales\Models\OrderTransaction;
use Frooxi\Sales\Models\Refund;
use Frooxi\Sales\Models\RefundItem;
use Frooxi\Sales\Models\Shipment;
use Frooxi\Sales\Models\ShipmentItem;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        DownloadableLinkPurchased::class,
        Invoice::class,
        InvoiceItem::class,
        Order::class,
        OrderAddress::class,
        OrderComment::class,
        OrderItem::class,
        OrderPayment::class,
        OrderTransaction::class,
        Refund::class,
        RefundItem::class,
        Shipment::class,
        ShipmentItem::class,
    ];
}
