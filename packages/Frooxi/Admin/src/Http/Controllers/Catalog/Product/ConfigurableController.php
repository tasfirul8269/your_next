<?php

namespace Frooxi\Admin\Http\Controllers\Catalog\Product;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Product\Helpers\ConfigurableOption;
use Frooxi\Product\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;

class ConfigurableController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected ConfigurableOption $configurableOptionHelper
    ) {}

    /**
     * Returns the compare items of the customer.
     */
    public function options(int $id): JsonResponse
    {
        $product = $this->productRepository->findOrFail($id);

        return new JsonResponse([
            'data' => $this->configurableOptionHelper->getConfigurationConfig($product),
        ]);
    }
}
