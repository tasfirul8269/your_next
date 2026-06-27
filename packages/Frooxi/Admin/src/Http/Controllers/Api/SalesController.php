<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Sales\Repositories\InvoiceRepository;
use Frooxi\Sales\Repositories\OrderRepository;
use Frooxi\Sales\Repositories\RefundRepository;
use Frooxi\Sales\Repositories\ShipmentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class SalesController extends Controller
{
    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected ShipmentRepository $shipmentRepository,
        protected RefundRepository $refundRepository,
        protected OrderRepository $orderRepository
    ) {}

    public function invoices(Request $request): JsonResponse
    {
        $invoices = $this->invoiceRepository
            ->with(['order', 'items'])
            ->paginate($request->get('limit', 10));

        return response()->json([
            'data' => $invoices->items(),
            'meta' => [
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
            ],
        ]);
    }

    /**
     * Get invoice details.
     */
    public function showInvoice(int $id): JsonResponse
    {
        $invoice = $this->invoiceRepository->with([
            'order',
            'order.billing_address',
            'order.shipping_address',
            'items',
        ])->find($id);

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        return response()->json([
            'data' => $invoice,
        ]);
    }

    /**
     * Create invoice from order.
     */
    public function createInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'invoice.items' => 'required|array',
        ]);

        $order = $this->orderRepository->find($request->input('order_id'));

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if (! $order->canInvoice()) {
            return response()->json([
                'message' => 'Invoice cannot be created for this order.',
            ], 422);
        }

        try {
            Event::dispatch('sales.invoice.save.before', $request->all());

            $invoice = $this->invoiceRepository->create($request->all());

            Event::dispatch('sales.invoice.save.after', $invoice);

            $invoice->load(['order', 'items']);

            return response()->json([
                'data' => $invoice,
                'message' => 'Invoice created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function shipments(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->shipmentRepository->paginate($request->get('limit', 10)),
        ]);
    }

    public function refunds(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->refundRepository->paginate($request->get('limit', 10)),
        ]);
    }
}
