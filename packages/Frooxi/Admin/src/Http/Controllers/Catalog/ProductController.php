<?php

namespace Frooxi\Admin\Http\Controllers\Catalog;

use Frooxi\Admin\DataGrids\Catalog\ProductDataGrid;
use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Admin\Http\Requests\InventoryRequest;
use Frooxi\Admin\Http\Requests\MassDestroyRequest;
use Frooxi\Admin\Http\Requests\MassUpdateRequest;
use Frooxi\Admin\Http\Requests\ProductForm;
use Frooxi\Admin\Http\Resources\AttributeResource;
use Frooxi\Admin\Http\Resources\ProductResource;
use Frooxi\Attribute\Repositories\AttributeFamilyRepository;
use Frooxi\Core\Rules\Slug;
use Frooxi\Customer\Repositories\CustomerRepository;
use Frooxi\Product\Helpers\Product;
use Frooxi\Product\Helpers\ProductType;
use Frooxi\Product\Repositories\ProductAttributeValueRepository;
use Frooxi\Product\Repositories\ProductDownloadableLinkRepository;
use Frooxi\Product\Repositories\ProductDownloadableSampleRepository;
use Frooxi\Product\Repositories\ProductInventoryRepository;
use Frooxi\Product\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Using const variable for status.
     */
    const ACTIVE_STATUS = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeFamilyRepository $attributeFamilyRepository,
        protected ProductAttributeValueRepository $productAttributeValueRepository,
        protected ProductDownloadableLinkRepository $productDownloadableLinkRepository,
        protected ProductDownloadableSampleRepository $productDownloadableSampleRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected ProductRepository $productRepository,
        protected CustomerRepository $customerRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(ProductDataGrid::class)->process();
        }

        $families = $this->attributeFamilyRepository->all();

        return view('admin::catalog.products.index', compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $families = $this->attributeFamilyRepository->all();

        $configurableFamily = null;

        if ($familyId = request()->get('family')) {
            $configurableFamily = $this->attributeFamilyRepository->find($familyId);
        }

        return view('admin::catalog.products.create', compact('families', 'configurableFamily'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
        $this->mergeRequestWithDefaultFamily();

        $this->validate(request(), [
            'type' => 'required',
            'sku' => ['required', 'unique:products,sku', new Slug],
            'super_attributes' => 'array|min:1',
            'super_attributes.*' => 'array|min:1',
        ]);

        if (
            ProductType::hasVariants(request()->input('type'))
            && ! request()->has('super_attributes')
        ) {
            $configurableFamily = $this->attributeFamilyRepository
                ->find(request()->input('attribute_family_id'));

            // Filter out the 'color' attribute from configurable attributes
            $configurableAttributes = $configurableFamily->configurable_attributes
                ->filter(function ($attribute) {
                    return $attribute->code !== 'color';
                });

            return new JsonResponse([
                'data' => [
                    'attributes' => AttributeResource::collection($configurableAttributes),
                ],
            ]);
        }

        Event::dispatch('catalog.product.create.before');

        $product = $this->productRepository->create(request()->only([
            'type',
            'attribute_family_id',
            'sku',
            'super_attributes',
            'family',
        ]));

        Event::dispatch('catalog.product.create.after', $product);

        session()->flash('success', trans('admin::app.catalog.products.create-success'));

        $redirectUrl = route('admin.catalog.products.edit', $product->id);

        // Redirect to flash sale edit if context matches
        if (request()->get('flash_sale') || session('flash_sale_product')) {
            $redirectUrl = route('admin.storefront.flash_sale.edit', $product->id).'?flash_sale=1';
            session()->forget('flash_sale_product');
        }

        return new JsonResponse([
            'data' => [
                'redirect_url' => $redirectUrl,
            ],
        ]);
    }

    /**
     * Merge the default attribute family id into the request.
     */
    private function mergeRequestWithDefaultFamily(): void
    {
        request()->merge(['attribute_family_id' => 1]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return View
     */
    public function edit(int $id)
    {
        $product = $this->productRepository->findOrFail($id);

        return view('admin::catalog.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(ProductForm $request, int $id)
    {
        try {
            Event::dispatch('catalog.product.update.before', $id);

            $data = $request->all();

            // Calculate special_price if flash_sale_discount is present
            if (isset($data['flash_sale_discount']) && (int) $data['flash_sale_discount'] > 0) {
                $product = $this->productRepository->find($id);
                $price = $data['price'] ?? $product->price;
                $data['special_price'] = $price * (1 - $data['flash_sale_discount'] / 100);
                $data['visible_individually'] = 0;
            } elseif (isset($data['discount_percentage']) && (float) $data['discount_percentage'] > 0) {
                // Map standard discount_percentage to special_price for simple products
                $product = $this->productRepository->find($id);
                $price = $data['price'] ?? $product->price;
                $data['special_price'] = round($price * (1 - (float) $data['discount_percentage'] / 100), 4);

                if (! isset($data['visible_individually'])) {
                    $data['visible_individually'] = 1;
                }
            } else {
                // If discount is removed or 0, restore visibility and clear special price
                if (! isset($data['visible_individually'])) {
                    $data['visible_individually'] = 1;
                }
                $data['special_price'] = null;
                $data['flash_sale_discount'] = 0;
            }

            $product = $this->productRepository->update($data, $id);

            Event::dispatch('catalog.product.update.after', $product);

            session()->flash('success', trans('admin::app.catalog.products.update-success'));

            if ($request->get('flash_sale')) {
                return redirect()->route('admin.storefront.flash_sale.index');
            }

            return redirect()->route('admin.catalog.products.index');
        } catch (\Exception $e) {
            \Log::error('Product update failed: '.$e->getMessage());
            \Log::error($e->getTraceAsString());

            session()->flash('error', 'Failed to save product: '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Update inventories.
     *
     * @return Response
     */
    public function updateInventories(InventoryRequest $inventoryRequest, int $id)
    {
        $product = $this->productRepository->findOrFail($id);

        Event::dispatch('catalog.product.update.before', $id);

        $this->productInventoryRepository->saveInventories(request()->all(), $product);

        Event::dispatch('catalog.product.update.after', $product);

        return response()->json([
            'message' => trans('admin::app.catalog.products.saved-inventory-message'),
            'updatedTotal' => $this->productInventoryRepository->where('product_id', $product->id)->sum('qty'),
        ]);
    }

    /**
     * Uploads downloadable file.
     *
     * @return Response
     */
    public function uploadLink(int $id)
    {
        return response()->json(
            $this->productDownloadableLinkRepository->upload(request()->all(), $id)
        );
    }

    /**
     * Copy a given Product.
     *
     * @return Response
     */
    public function copy(int $id)
    {
        try {
            Event::dispatch('catalog.product.create.before');

            $product = $this->productRepository->copy($id);

            Event::dispatch('catalog.product.create.after', $product);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->to(route('admin.catalog.products.index'));
        }

        return response()->json([
            'message' => trans('admin::app.catalog.products.product-copied'),
        ]);
    }

    /**
     * Uploads downloadable sample file.
     *
     * @return Response
     */
    public function uploadSample(int $id)
    {
        return response()->json(
            $this->productDownloadableSampleRepository->upload(request()->all(), $id)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('catalog.product.delete.before', $id);

            $this->productRepository->delete($id);

            Event::dispatch('catalog.product.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.catalog.products.delete-success'),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return new JsonResponse([
            'message' => trans('admin::app.catalog.products.delete-failed'),
        ], 500);
    }

    /**
     * Mass delete the products.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $productIds = $massDestroyRequest->input('indices');

        try {
            foreach ($productIds as $productId) {
                $product = $this->productRepository->find($productId);

                if (isset($product)) {
                    Event::dispatch('catalog.product.delete.before', $productId);

                    $this->productRepository->delete($productId);

                    Event::dispatch('catalog.product.delete.after', $productId);
                }
            }

            return new JsonResponse([
                'message' => trans('admin::app.catalog.products.index.datagrid.mass-delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mass update the products.
     */
    public function massUpdate(MassUpdateRequest $massUpdateRequest): JsonResponse
    {
        $productIds = $massUpdateRequest->input('indices');

        foreach ($productIds as $productId) {
            Event::dispatch('catalog.product.update.before', $productId);

            $product = $this->productRepository->update([
                'status' => $massUpdateRequest->input('value'),
            ], $productId, ['status']);

            Event::dispatch('catalog.product.update.after', $product);
        }

        return new JsonResponse([
            'message' => trans('admin::app.catalog.products.index.datagrid.mass-update-success'),
        ], 200);
    }

    /**
     * To be manually invoked when data is seeded into products.
     *
     * @return Response
     */
    public function sync()
    {
        Event::dispatch('products.datagrid.sync', true);

        return redirect()->route('admin.catalog.products.index');
    }

    /**
     * Result of search product.
     *
     * @return JsonResponse
     */
    public function search()
    {
        $query = trim(request('query'));

        if (empty($query)) {
            return response()->json([
                'data' => [],
            ]);
        }

        $searchEngine = 'database';

        if (
            core()->getConfigData('catalog.products.search.engine') == 'elastic'
            && core()->getConfigData('catalog.products.search.admin_mode') == 'elastic'
        ) {
            $searchEngine = 'elastic';

            $indexNames = core()->getAllChannels()->map(function ($channel) {
                return Product::formatElasticSearchIndexName($channel->code, app()->getLocale());
            })->toArray();
        }

        $channelId = $this->customerRepository->find(request('customer_id'))->channel_id ?? null;

        $params = [
            'index' => $indexNames ?? null,
            'name' => request('query'),
            'sort' => 'created_at',
            'order' => 'desc',
            'channel_id' => $channelId,
        ];

        if (request()->has('type')) {
            $params['type'] = request('type');
        }

        if (request()->has('exclude_customizable_products')) {
            $params['exclude_customizable_products'] = request('exclude_customizable_products');
        }

        $products = $this->productRepository
            ->setSearchEngine($searchEngine)
            ->getAll($params);

        return ProductResource::collection($products);
    }

    /**
     * Download image or file.
     *
     * @param  int  $productId
     * @param  int  $attributeId
     * @return Response
     */
    public function download($productId, $attributeId)
    {
        $productAttribute = $this->productAttributeValueRepository->findOneWhere([
            'product_id' => $productId,
            'attribute_id' => $attributeId,
        ]);

        return Storage::download($productAttribute['text_value']);
    }
}
