<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Sales\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Allowed order status transitions.
     */
    protected array $allowedTransitions = [
        'pending' => ['processing', 'canceled'],
        'pending_payment' => ['pending', 'canceled'],
        'processing' => ['completed', 'canceled'],
        'completed' => [],
        'canceled' => [],
        'closed' => [],
        'fraud' => ['canceled'],
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Get orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderRepository->with(['items'])->paginate($request->get('limit', 10));

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Get order details.
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderRepository->with([
            'items',
            'shipping_address',
            'billing_address',
            'invoices',
            'shipments',
            'refunds',
            'transactions',
            'payment',
            'comments',
        ])->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        try {
            // Build a status_history array from comments + order lifecycle
            $statusHistory = [];

            // Add order creation entry
            $statusHistory[] = [
                'status' => 'pending',
                'comment' => 'Order placed.',
                'created_at' => $order->created_at,
            ];

            // Add comments as history entries
            if ($order->relationLoaded('comments') && $order->comments && $order->comments->count() > 0) {
                foreach ($order->comments as $comment) {
                    $statusHistory[] = [
                        'status' => $comment->status ?? $order->status,
                        'comment' => $comment->comment ?? '',
                        'created_at' => $comment->created_at,
                    ];
                }
            } elseif ($order->status !== 'pending') {
                // If no comments but status changed, add a generic entry
                $statusHistory[] = [
                    'status' => $order->status,
                    'comment' => 'Status updated to '.$order->status.'.',
                    'created_at' => $order->updated_at,
                ];
            }

            $orderData = $order->toArray();
            $orderData['status_history'] = $statusHistory;

            return response()->json([
                'data' => $orderData,
            ]);
        } catch (\Exception $e) {
            // If anything fails with relations, return order without status_history
            $orderData = $order->toArray();
            $orderData['status_history'] = [['status' => $order->status, 'comment' => 'Order placed.', 'created_at' => $order->created_at]];

            return response()->json(['data' => $orderData]);
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $request->validate([
            'status' => 'required|string',
        ]);

        $newStatus = $request->input('status');
        $currentStatus = $order->status;

        $allowed = $this->allowedTransitions[$currentStatus] ?? [];

        if (! in_array($newStatus, $allowed)) {
            return response()->json([
                'message' => "Cannot transition order from '{$currentStatus}' to '{$newStatus}'.",
            ], 422);
        }

        $this->orderRepository->update(['status' => $newStatus], $id);

        $order->refresh();
        $order->load(['items', 'shipping_address', 'billing_address', 'invoices', 'shipments', 'refunds', 'transactions', 'payment']);

        return response()->json([
            'data' => $order,
            'message' => 'Order status updated successfully.',
        ]);
    }

    /**
     * Cancel an order.
     */
    public function cancel(int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if (! in_array($order->status, ['pending', 'pending_payment', 'processing', 'fraud'])) {
            return response()->json([
                'message' => 'Order cannot be canceled in its current status.',
            ], 422);
        }

        $result = $this->orderRepository->cancel($id);

        if ($result) {
            $order->refresh();

            return response()->json([
                'data' => $order,
                'message' => 'Order canceled successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Unable to cancel order.',
        ], 422);
    }

    /**
     * Delete an order.
     */
    public function destroy(int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        try {
            $this->orderRepository->delete($id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
