<?php

namespace Frooxi\Admin\Http\Controllers\Sales;

use Frooxi\Admin\DataGrids\Sales\OrderInvoiceDataGrid;
use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Admin\Http\Requests\MassUpdateRequest;
use Frooxi\Admin\Http\Requests\MassDestroyRequest;
use Frooxi\Core\Traits\PDFHandler;
use Frooxi\Sales\Repositories\InvoiceRepository;
use Frooxi\Sales\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(OrderInvoiceDataGrid::class)->process();
        }

        return view('admin::sales.invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(int $orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if ($order->payment->method === 'paypal_standard') {
            abort(404);
        }

        return view('admin::sales.invoices.create', compact('order'));
    }

    /**
     * (Store) a newly created resource in storage.
     *
     * @return Response
     */
    public function store(int $orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (! $order->canInvoice()) {
            session()->flash('error', trans('admin::app.sales.invoices.create.creation-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'invoice.items' => 'required|array',
            'invoice.items.*' => 'required|numeric|min:0',
        ]);

        if (! $this->invoiceRepository->haveProductToInvoice(request()->all())) {
            session()->flash('error', trans('admin::app.sales.invoices.create.product-error'));

            return redirect()->back();
        }

        if (! $this->invoiceRepository->isValidQuantity(request()->all())) {
            session()->flash('error', trans('admin::app.sales.invoices.create.invalid-qty'));

            return redirect()->back();
        }

        $invoiceState = $order->payment->method === 'cashondelivery'
            ? InvoiceRepository::DEFAULT_STATE_PENDING
            : InvoiceRepository::DEFAULT_STATE_PAID;

        $this->invoiceRepository->create(array_merge(request()->all(), [
            'order_id' => $orderId,
        ]), $invoiceState);

        session()->flash('success', trans('admin::app.sales.invoices.create.create-success'));

        return redirect()->route('admin.sales.orders.view', $orderId);
    }

    /**
     * Show the view for the specified resource.
     *
     * @return View
     */
    public function view(int $id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        return view('admin::sales.invoices.view', compact('invoice'));
    }

    /**
     * Send duplicate invoice.
     *
     * @return Response
     */
    public function sendDuplicateEmail(Request $request, int $id)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $invoice = $this->invoiceRepository->findOrFail($id);

        Event::dispatch('sales.invoice.send_duplicate_email', [
            'invoice' => $invoice,
            'duplicate_invoice_email' => request()->input('email'),
        ]);

        session()->flash('success', trans('admin::app.sales.invoices.view.invoice-sent'));

        return redirect()->route('admin.sales.invoices.view', $invoice->id);
    }

    /**
     * Print and download the for the specified resource.
     *
     * @return Response
     */
    public function printInvoice(int $id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        $orderCurrencyCode = $invoice->order->order_currency_code;

        return $this->downloadPDF(
            view('shop::customers.account.orders.pdf', compact('invoice', 'orderCurrencyCode'))->render(),
            'invoice-'.$invoice->created_at->format('d-m-Y')
        );
    }

    /**
     * Delete the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        try {
            $invoice->delete();

            return new JsonResponse([
                'message' => trans('admin::app.sales.invoices.index.datagrid.delete-success'),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return new JsonResponse([
            'message' => trans('admin::app.sales.invoices.index.datagrid.delete-failed'),
        ], 500);
    }

    /**
     * Mass delete the invoices.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $invoiceIds = $massDestroyRequest->input('indices');

        try {
            foreach ($invoiceIds as $invoiceId) {
                $invoice = $this->invoiceRepository->find($invoiceId);

                if ($invoice) {
                    $invoice->delete();
                }
            }

            return new JsonResponse([
                'message' => trans('admin::app.sales.invoices.index.datagrid.mass-delete-success'),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return new JsonResponse([
            'message' => trans('admin::app.sales.invoices.index.datagrid.delete-failed'),
        ], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function massUpdateState(MassUpdateRequest $massUpdateRequest)
    {
        $invoiceIds = $massUpdateRequest->input('indices');

        $invoices = $this->invoiceRepository->findWhereIn('id', $invoiceIds);

        foreach ($invoices as $invoice) {
            $invoice->state = $massUpdateRequest->input('value');

            $invoice->save();
        }

        return new JsonResponse([
            'message' => trans('admin::app.sales.invoices.index.datagrid.mass-update-success'),
        ], 200);
    }
}
