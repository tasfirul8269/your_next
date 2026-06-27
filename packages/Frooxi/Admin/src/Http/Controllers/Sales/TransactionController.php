<?php

namespace Frooxi\Admin\Http\Controllers\Sales;

use Frooxi\Admin\DataGrids\Sales\OrderTransactionDataGrid;
use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Admin\Http\Resources\TransactionResource;
use Frooxi\Payment\Facades\Payment;
use Frooxi\Sales\Models\Invoice;
use Frooxi\Sales\Models\Order;
use Frooxi\Sales\Repositories\InvoiceRepository;
use Frooxi\Sales\Repositories\OrderRepository;
use Frooxi\Sales\Repositories\OrderTransactionRepository;
use Frooxi\Sales\Repositories\ShipmentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected ShipmentRepository $shipmentRepository,
        protected OrderTransactionRepository $orderTransactionRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(OrderTransactionDataGrid::class)->process();
        }

        $paymentMethods = Payment::getSupportedPaymentMethods();

        return view('admin::sales.transactions.index', compact('paymentMethods'));
    }

    /**
     * Save the transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate(request(), [
            'invoice_id' => 'required',
            'payment_method' => 'required',
            'amount' => 'required|numeric',
        ]);

        $invoice = $this->invoiceRepository->where('id', $request->invoice_id)->first();

        if (! $invoice) {
            return new JsonResponse([
                'message' => trans('admin::app.sales.transactions.index.create.invoice-missing'),
            ], 400);
        }

        $transactionAmtBefore = $this->orderTransactionRepository->where('invoice_id', $invoice->id)->sum('amount');

        $transactionAmtFinal = $request->amount + $transactionAmtBefore;

        if ($invoice->state == 'paid') {
            return new JsonResponse([
                'message' => trans('admin::app.sales.transactions.index.create.already-paid'),
            ], 400);
        }

        if ($transactionAmtFinal > $invoice->base_grand_total) {
            return new JsonResponse([
                'message' => trans('admin::app.sales.transactions.index.create.transaction-amount-exceeds'),
            ], 400);
        }

        if ($request->amount <= 0) {
            return new JsonResponse([
                'message' => trans('admin::app.sales.transactions.index.create.transaction-amount-zero'),
            ], 400);
        }

        $order = $this->orderRepository->find($invoice->order_id);

        $this->orderTransactionRepository->create([
            'transaction_id' => bin2hex(random_bytes(20)),
            'type' => $request->payment_method,
            'payment_method' => $request->payment_method,
            'invoice_id' => $invoice->id,
            'order_id' => $invoice->order_id,
            'amount' => $request->amount,
            'status' => 'paid',
            'data' => json_encode([
                'paidAmount' => $request->amount,
            ]),
        ]);

        $transactionTotal = $this->orderTransactionRepository->where('invoice_id', $invoice->id)->sum('amount');

        if ($transactionTotal >= $invoice->base_grand_total) {
            $shipments = $this->shipmentRepository->where('order_id', $invoice->order_id)->first();

            $status = isset($shipments)
                ? Order::STATUS_COMPLETED
                : Order::STATUS_PROCESSING;

            $this->orderRepository->updateOrderStatus($order, $status);

            $this->invoiceRepository->updateState($invoice, Invoice::STATUS_PAID);
        }

        return new JsonResponse([
            'message' => trans('admin::app.sales.transactions.index.create.transaction-saved'),
        ]);
    }

    /**
     * Show the view for the specified resource.
     */
    public function view(int $id): TransactionResource
    {
        $transaction = $this->orderTransactionRepository->findOrFail($id);

        return new TransactionResource($transaction);
    }
}
