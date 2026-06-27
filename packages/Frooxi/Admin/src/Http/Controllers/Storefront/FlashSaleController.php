<?php

namespace Frooxi\Admin\Http\Controllers\Storefront;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Product\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ProductRepository $productRepository
    ) {}

    /**
     * Display the flash sale management page.
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Frooxi\Admin\DataGrids\Storefront\FlashSaleProductDataGrid::class)->toJson();
        }

        return view('admin::storefront.flash-sale.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::storefront.flash-sale.index', ['create_flash_sale' => 1]);
    }

    /**
     * Store a newly created flash sale product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => ['required', 'unique:products,sku', new \Frooxi\Core\Rules\Slug],
            'name' => 'required',
            'price' => 'required|numeric',
            'flash_sale_discount' => 'required|numeric|min:1|max:99',
        ]);

        try {
            Event::dispatch('catalog.product.create.before');

            $request->merge([
                'type' => 'simple',
                'attribute_family_id' => 1,
            ]);

            $product = $this->productRepository->create($request->only([
                'type',
                'attribute_family_id',
                'sku',
            ]));

            Event::dispatch('catalog.product.create.after', $product);

            // Now update the product with the rest of the attributes
            Event::dispatch('catalog.product.update.before', $product->id);

            $data = $request->all();

            if (isset($data['flash_sale_discount']) && (int) $data['flash_sale_discount'] > 0) {
                $price = $data['price'] ?? 0;
                $data['special_price'] = $price * (1 - $data['flash_sale_discount'] / 100);
                $data['visible_individually'] = 1;
            } else {
                $data['visible_individually'] = 1;
                $data['special_price'] = null;
                $data['flash_sale_discount'] = 0;
            }

            $data['channel'] = core()->getRequestedChannelCode();
            $data['locale'] = core()->getRequestedLocaleCode();

            $product = $this->productRepository->update($data, $product->id);

            Event::dispatch('catalog.product.update.after', $product);

            session()->flash('success', trans('admin::app.catalog.products.create-success'));

            return redirect()->route('admin.storefront.flash_sale.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $product = $this->productRepository->findOrFail($id);

        return view('admin::catalog.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Frooxi\Admin\Http\Requests\ProductForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(\Frooxi\Admin\Http\Requests\ProductForm $request, $id)
    {
        return app(\Frooxi\Admin\Http\Controllers\Catalog\ProductController::class)->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Event::dispatch('catalog.product.delete.before', $id);

            $this->productRepository->delete($id);

            Event::dispatch('catalog.product.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.catalog.products.delete-success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('admin::app.catalog.products.delete-failed'),
            ], 400);
        }
    }
}
