<?php

namespace Frooxi\Admin\Http\Controllers\Catalog\Product;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Product\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;

class SimpleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ProductRepository $productRepository,
    ) {}

    /**
     * Returns the customizable options of the product.
     */
    public function customizableOptions(int $id): JsonResponse
    {
        $product = $this->productRepository->findOrFail($id);

        return new JsonResponse([
            'data' => $product->customizable_options()->with([
                'product',
                'customizable_option_prices',
            ])->get(),

            'meta' => [
                'initial_price' => $product->getTypeInstance()->getMinimalPrice(),
            ],
        ]);
    }
}
