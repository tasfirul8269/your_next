<?php

namespace Frooxi\Shop\Http\Controllers\Customer\Account;

use Frooxi\Checkout\Facades\Cart;
use Frooxi\Core\Traits\PDFHandler;
use Frooxi\Sales\Repositories\InvoiceRepository;
use Frooxi\Sales\Repositories\OrderRepository;
use Frooxi\Shop\DataGrids\OrderDataGrid;
use Frooxi\Shop\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository
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

        return view('shop::customers.account.orders.index');
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function view($id)
    {
        $order = $this->orderRepository->findOneWhere([
            'customer_id' => auth()->guard('customer')->id(),
            'id' => $id,
        ]);

        abort_if(! $order, 404);

        return view('shop::customers.account.orders.view', compact('order'));
    }

    /**
     * Reorder action for the specified resource.
     *
     * @return Response
     */
    public function reorder(int $id)
    {
        $order = $this->orderRepository->findOneWhere([
            'customer_id' => auth()->guard('customer')->id(),
            'id' => $id,
        ]);

        abort_if(! $order, 404);

        foreach ($order->items as $item) {
            try {
                Cart::addProduct($item->product, $item->additional);
            } catch (\Exception $e) {
            }
        }

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Print and download the for the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function printInvoice($id)
    {
        $invoice = $this->invoiceRepository->where('id', $id)
            ->whereHas('order', function ($query) {
                $query->where('customer_id', auth()->guard('customer')->id());
            })
            ->firstOrFail();

        $orderCurrencyCode = $invoice->order->order_currency_code;

        return $this->downloadPDF(
            view('shop::customers.account.orders.pdf', compact('invoice', 'orderCurrencyCode'))->render(),
            'invoice-'.$invoice->created_at->format('d-m-Y')
        );
    }

    /**
     * Cancel action for the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function cancel($id)
    {
        $customer = auth()->guard('customer')->user();

        /* find by order id in customer's order */
        $order = $customer->orders()->find($id);

        /* if order id not found then process should be aborted with 404 page */
        if (! $order) {
            abort(404);
        }

        $result = $this->orderRepository->cancel($order);

        if ($result) {
            session()->flash('success', trans('shop::app.customers.account.orders.view.cancel-success', ['name' => trans('shop::app.customers.account.orders.order')]));
        } else {
            session()->flash('error', trans('shop::app.customers.account.orders.view.cancel-error', ['name' => trans('shop::app.customers.account.orders.order')]));
        }

        return redirect()->back();
    }
}
