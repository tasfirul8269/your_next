<?php

namespace Frooxi\Admin\Http\Controllers\Sales;

use Frooxi\Admin\DataGrids\Sales\OrderDataGrid;
use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Admin\Http\Requests\MassDestroyRequest;
use Frooxi\Admin\Http\Resources\AddressResource;
use Frooxi\Admin\Http\Resources\CartResource;
use Frooxi\Checkout\Facades\Cart;
use Frooxi\Checkout\Repositories\CartRepository;
use Frooxi\Customer\Repositories\CustomerGroupRepository;
use Frooxi\Sales\Repositories\OrderCommentRepository;
use Frooxi\Sales\Repositories\OrderItemRepository;
use Frooxi\Sales\Repositories\OrderRepository;
use Frooxi\Sales\Transformers\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderCommentRepository $orderCommentRepository,
        protected CartRepository $cartRepository,
        protected CustomerGroupRepository $customerGroupRepository,
        protected OrderItemRepository $orderItemRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(OrderDataGrid::class)->process();
        }

        $channels = core()->getAllChannels();

        $groups = $this->customerGroupRepository->findWhere([['code', '<>', 'guest']]);

        return view('admin::sales.orders.index', compact('channels', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(int $cartId)
    {
        $cart = $this->cartRepository->find($cartId);

        if (! $cart) {
            return redirect()->route('admin.sales.orders.index');
        }

        $addresses = AddressResource::collection($cart->customer->addresses);

        $cart = new CartResource($cart);

        return view('admin::sales.orders.create', compact('cart', 'addresses'));
    }

    /**
     * Store order
     */
    public function store(int $cartId)
    {
        $cart = $this->cartRepository->findOrFail($cartId);

        Cart::setCart($cart);

        if (Cart::hasError()) {
            return response()->json([
                'message' => trans('admin::app.sales.orders.create.error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        Cart::collectTotals();

        try {
            $this->validateOrder();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $cart = Cart::getCart();

        if (! in_array($cart->payment->method, ['cashondelivery'])) {
            return response()->json([
                'message' => trans('admin::app.sales.orders.create.payment-not-supported'),
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        Cart::removeCart($cart);

        session()->flash('order', trans('admin::app.sales.orders.create.order-placed-success'));

        return new JsonResource([
            'redirect' => true,
            'redirect_url' => route('admin.sales.orders.view', $order->id),
        ]);
    }

    /**
     * Show the view for the specified resource.
     *
     * @return View
     */
    public function view(int $id)
    {
        $order = $this->orderRepository->findOrFail($id);

        return view('admin::sales.orders.view', compact('order'));
    }

    /**
     * Reorder action for the specified resource.
     *
     * @return Response
     */
    public function reorder(int $id)
    {
        $order = $this->orderRepository->findOrFail($id);

        $cart = Cart::createCart([
            'customer' => $order->customer,
            'is_active' => false,
        ]);

        Cart::setCart($cart);

        foreach ($order->items as $item) {
            try {
                Cart::addProduct($item->product, $item->additional);
            } catch (\Exception $e) {
                // do nothing
            }
        }

        return redirect()->route('admin.sales.orders.create', $cart->id);
    }

    /**
     * Cancel action for the specified resource.
     *
     * @return Response
     */
    public function cancel(int $id)
    {
        $order = $this->orderRepository->findOrFail($id);

        // Return inventory before deleting
        $this->returnInventoryForOrder($order);

        // Delete the order
        $order->delete();

        session()->flash('success', trans('admin::app.sales.orders.view.cancel-success'));

        return redirect()->route('admin.sales.orders.index');
    }

    /**
     * Return inventory for cancelled order.
     * Handles both simple and configurable products.
     */
    protected function returnInventoryForOrder($order): void
    {
        \Log::info('Returning inventory for cancelled order #'.$order->id);

        try {
            $channelInventorySourceIds = $order->channel?->inventory_sources?->where('status', 1)?->pluck('id') ?? collect();
        } catch (\Exception $e) {
            \Log::warning('Could not load channel for order #'.$order->id.': '.$e->getMessage());
            $channelInventorySourceIds = collect();
        }

        // Use all_items (includes child variant items); items() filters out parent_id IS NOT NULL
        $allItems = $order->all_items;

        foreach ($allItems as $item) {
            // Skip configurable parent items — inventory lives on the variant (child)
            if ($item->type === 'configurable') {
                continue;
            }

            $product = $item->product;

            if (! $product || ! $product->manage_stock) {
                continue;
            }

            // Child items of configurable may have qty_ordered=0; use parent's qty
            $qty = (int) $item->qty_ordered;
            if ($qty <= 0 && $item->parent_id) {
                $parentItem = $allItems->firstWhere('id', $item->parent_id);
                $qty = (int) ($parentItem?->qty_ordered ?? 0);
            }

            if ($qty <= 0) {
                continue;
            }

            // Remove reserved quantity from ordered_inventories
            $orderedInventory = $product->ordered_inventories()
                ->where('channel_id', $order->channel_id)
                ->first();

            if ($orderedInventory) {
                $newOrderedQty = max(0, $orderedInventory->qty - $qty);
                $orderedInventory->update(['qty' => $newOrderedQty]);
                \Log::info('Returned ordered_inventory: new qty='.$newOrderedQty);
            }

            // Return to actual product_inventories
            foreach ($channelInventorySourceIds as $inventorySourceId) {
                $inventory = $product->inventories()
                    ->where('inventory_source_id', $inventorySourceId)
                    ->first();

                if ($inventory) {
                    $newQty = $inventory->qty + $qty;
                    $inventory->update(['qty' => $newQty]);
                    \Log::info('Returned '.$qty.' to inventory source #'.$inventorySourceId.', new qty='.$newQty);
                    break;
                }
            }
        }

        \Log::info('Inventory return complete for cancelled order #'.$order->id);
    }

    /**
     * Delete the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $order = $this->orderRepository->findOrFail($id);

        try {
            $this->returnInventoryForOrder($order);
        } catch (\Exception $e) {
            \Log::warning('Inventory return failed for order #'.$id.' during delete: '.$e->getMessage());
        }

        try {
            $order->delete();

            return new JsonResponse([
                'message' => trans('admin::app.sales.orders.index.datagrid.delete-success'),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return new JsonResponse([
            'message' => trans('admin::app.sales.orders.index.datagrid.delete-failed'),
        ], 500);
    }

    /**
     * Mass delete the orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $orderIds = $massDestroyRequest->input('indices');

        try {
            foreach ($orderIds as $orderId) {
                $order = $this->orderRepository->find($orderId);

                if ($order) {
                    try {
                        $this->returnInventoryForOrder($order);
                    } catch (\Exception $e) {
                        \Log::warning('Inventory return failed for order #'.$orderId.' during mass delete: '.$e->getMessage());
                    }

                    $order->delete();
                }
            }

            return new JsonResponse([
                'message' => trans('admin::app.sales.orders.index.datagrid.mass-delete-success'),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return new JsonResponse([
            'message' => trans('admin::app.sales.orders.index.datagrid.delete-failed'),
        ], 500);
    }

    /**
     * Add comment to the order
     *
     * @return Response
     */
    public function comment(int $id)
    {
        $validatedData = $this->validate(request(), [
            'comment' => 'required',
            'customer_notified' => 'sometimes|sometimes',
        ]);

        $validatedData['order_id'] = $id;

        Event::dispatch('sales.order.comment.create.before');

        $comment = $this->orderCommentRepository->create($validatedData);

        Event::dispatch('sales.order.comment.create.after', $comment);

        session()->flash('success', trans('admin::app.sales.orders.view.comment-success'));

        return redirect()->route('admin.sales.orders.view', $id);
    }

    /**
     * Update order status.
     *
     * @return JsonResponse
     */
    public function updateStatus(int $id)
    {
        $order = $this->orderRepository->findOrFail($id);

        // These statuses are terminal or payment-controlled — not manually editable
        if (in_array($order->status, ['canceled', 'pending_payment', 'completed', 'closed'])) {
            return response()->json([
                'message' => trans('admin::app.sales.orders.update-status-not-allowed'),
            ], 422);
        }

        $validated = request()->validate([
            'status' => 'required|in:pending,processing,shipped',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // No-op if status hasn't changed
        if ($oldStatus === $newStatus) {
            return response()->json(['message' => trans('admin::app.sales.orders.update-status-success')]);
        }

        \Log::info('Order #'.$id.' status changing from '.$oldStatus.' to '.$newStatus);

        $stockReducedStatuses = ['processing', 'shipped'];
        $wasStockReduced = in_array($oldStatus, $stockReducedStatuses);
        $willReduceStock = in_array($newStatus, $stockReducedStatuses);

        if (! $wasStockReduced && $willReduceStock) {
            // pending → processing/shipped: deduct stock
            \Log::info('Triggering inventory reduction for order #'.$id);
            $this->reduceInventoryForOrder($order);
        } elseif ($wasStockReduced && ! $willReduceStock) {
            // processing/shipped → pending: return stock
            \Log::info('Triggering inventory return for order #'.$id);
            $this->addInventoryForOrder($order);
        }

        $order->status = $newStatus;
        $order->save();

        Event::dispatch('sales.order.update-status.after', $order);

        return response()->json([
            'message' => trans('admin::app.sales.orders.update-status-success'),
        ]);
    }

    /**
     * Reduce inventory for order items.
     * Handles both simple and configurable products.
     */
    protected function reduceInventoryForOrder($order): void
    {
        \Log::info('Starting inventory reduction for order #'.$order->id);

        try {
            $channelInventorySourceIds = $order->channel?->inventory_sources?->where('status', 1)?->pluck('id') ?? collect();
        } catch (\Exception $e) {
            \Log::warning('Could not load channel for order #'.$order->id.': '.$e->getMessage());
            $channelInventorySourceIds = collect();
        }
        \Log::info('Active inventory sources: '.$channelInventorySourceIds->implode(', '));

        // Use all_items (includes child variant items); items() filters out parent_id IS NOT NULL
        $allItems = $order->all_items;

        foreach ($allItems as $item) {
            \Log::info('Processing order item #'.$item->id.' type='.$item->type);

            // Skip configurable parent items — inventory lives on the variant (child)
            if ($item->type === 'configurable') {
                \Log::info('Skipping configurable parent item #'.$item->id);

                continue;
            }

            $product = $item->product;

            if (! $product) {
                \Log::info('Skipping item #'.$item->id.' - no product found');

                continue;
            }

            // Check manage_stock (stored in product_flat, accessible via model attribute)
            if (! $product->manage_stock) {
                \Log::info('Skipping item #'.$item->id.' - product does not manage stock');

                continue;
            }

            // For child items (variant of configurable), qty_ordered may be 0;
            // fall back to the parent order item's qty_ordered
            $qty = (int) $item->qty_ordered;
            if ($qty <= 0 && $item->parent_id) {
                $parentItem = $allItems->firstWhere('id', $item->parent_id);
                $qty = (int) ($parentItem?->qty_ordered ?? 0);
                \Log::info('Child item qty was 0, using parent qty: '.$qty);
            }

            if ($qty <= 0) {
                \Log::info('Skipping item #'.$item->id.' - qty is 0 or less');

                continue;
            }

            \Log::info('Reducing inventory by '.$qty.' for product #'.$product->id.' (item #'.$item->id.')');

            // Reduce from ordered_inventories (reserved quantity)
            $orderedInventory = $product->ordered_inventories()
                ->where('channel_id', $order->channel_id)
                ->first();

            if ($orderedInventory) {
                $newOrderedQty = max(0, $orderedInventory->qty - $qty);
                \Log::info('ordered_inventory: '.$orderedInventory->qty.' → '.$newOrderedQty);
                $orderedInventory->update(['qty' => $newOrderedQty]);
            }

            // Reduce from actual product_inventories
            $remaining = $qty;
            foreach ($channelInventorySourceIds as $inventorySourceId) {
                $inventory = $product->inventories()
                    ->where('inventory_source_id', $inventorySourceId)
                    ->first();

                if ($inventory && $inventory->qty > 0) {
                    $reduceQty = min($remaining, $inventory->qty);
                    $newQty = $inventory->qty - $reduceQty;
                    \Log::info('inventory source #'.$inventorySourceId.': '.$inventory->qty.' → '.$newQty);
                    $inventory->update(['qty' => $newQty]);
                    $remaining -= $reduceQty;

                    if ($remaining <= 0) {
                        break;
                    }
                }
            }

            if ($remaining > 0) {
                \Log::warning('Could not fully reduce inventory for product #'.$product->id.'. Remaining: '.$remaining);
            }
        }

        \Log::info('Inventory reduction complete for order #'.$order->id);
    }

    /**
     * Add (restore) inventory when order status is reverted (e.g. processing/shipped → pending).
     * Handles both simple and configurable products.
     */
    protected function addInventoryForOrder($order): void
    {
        \Log::info('Restoring inventory for order #'.$order->id);

        try {
            $channelInventorySourceIds = $order->channel?->inventory_sources?->where('status', 1)?->pluck('id') ?? collect();
        } catch (\Exception $e) {
            \Log::warning('Could not load channel for order #'.$order->id.': '.$e->getMessage());
            $channelInventorySourceIds = collect();
        }
        $allItems = $order->all_items;

        foreach ($allItems as $item) {
            if ($item->type === 'configurable') {
                continue;
            }

            $product = $item->product;

            if (! $product || ! $product->manage_stock) {
                continue;
            }

            $qty = (int) $item->qty_ordered;
            if ($qty <= 0 && $item->parent_id) {
                $parentItem = $allItems->firstWhere('id', $item->parent_id);
                $qty = (int) ($parentItem?->qty_ordered ?? 0);
                \Log::info('Child item qty was 0, using parent qty: '.$qty);
            }

            if ($qty <= 0) {
                continue;
            }

            \Log::info('Restoring '.$qty.' units for product #'.$product->id.' (item #'.$item->id.')');

            // Restore to actual product_inventories (first active source)
            foreach ($channelInventorySourceIds as $inventorySourceId) {
                $inventory = $product->inventories()
                    ->where('inventory_source_id', $inventorySourceId)
                    ->first();

                if ($inventory) {
                    $newQty = $inventory->qty + $qty;
                    $inventory->update(['qty' => $newQty]);
                    \Log::info('Restored inventory source #'.$inventorySourceId.': '.$inventory->qty.' → '.$newQty);
                    break;
                }
            }
        }

        \Log::info('Inventory restore complete for order #'.$order->id);
    }

    /**
     * Result of search product.
     *
     * @return JsonResponse
     */
    public function search()
    {
        $orders = $this->orderRepository->scopeQuery(function ($query) {
            return $query->where('customer_email', 'like', '%'.urldecode(request()->input('query')).'%')
                ->orWhere('status', 'like', '%'.urldecode(request()->input('query')).'%')
                ->orWhere(DB::raw('CONCAT(customer_first_name, " ", customer_last_name)'), 'like', '%'.urldecode(request()->input('query')).'%')
                ->orWhere('increment_id', request()->input('query'))
                ->orderBy('created_at', 'desc');
        })->paginate(10);

        foreach ($orders as $key => $order) {
            $orders[$key]['formatted_created_at'] = core()->formatDate($order->created_at, 'd M Y');

            $orders[$key]['status_label'] = $order->status_label;

            $orders[$key]['customer_full_name'] = $order->customer_full_name;
        }

        return response()->json($orders);
    }

    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        if (! Cart::haveMinimumOrderAmount()) {
            throw new \Exception(trans('admin::app.sales.orders.create.minimum-order-error', [
                'amount' => core()->formatPrice(core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0),
            ]));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->shipping_address
        ) {
            throw new \Exception(trans('admin::app.sales.orders.create.check-shipping-address'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('admin::app.sales.orders.create.check-billing-address'));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->selected_shipping_rate
        ) {
            throw new \Exception(trans('admin::app.sales.orders.create.specify-shipping-method'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('admin::app.sales.orders.create.specify-payment-method'));
        }
    }
}
