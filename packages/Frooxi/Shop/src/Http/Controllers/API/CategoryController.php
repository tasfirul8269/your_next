<?php

namespace Frooxi\Shop\Http\Controllers\API;

use Frooxi\Attribute\Enums\AttributeTypeEnum;
use Frooxi\Attribute\Repositories\AttributeRepository;
use Frooxi\Category\Repositories\CategoryRepository;
use Frooxi\Product\Repositories\ProductRepository;
use Frooxi\Shop\Http\Resources\AttributeOptionResource;
use Frooxi\Shop\Http\Resources\AttributeResource;
use Frooxi\Shop\Http\Resources\CategoryResource;
use Frooxi\Shop\Http\Resources\CategoryTreeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryController extends APIController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Get all categories.
     */
    public function index(): JsonResource
    {
        /**
         * These are the default parameters. By default, only the enabled category
         * will be shown in the current locale.
         */
        $defaultParams = [
            'status' => 1,
            'locale' => app()->getLocale(),
        ];

        $categories = $this->categoryRepository->getAll(array_merge($defaultParams, request()->all()));

        return CategoryResource::collection($categories);
    }

    /**
     * Get all categories in tree format.
     */
    public function tree(): JsonResponse
    {
        $version = cache()->get('category_tree_version', 1);

        $cacheKey = 'category_tree_'.core()->getCurrentChannel()->id.'_'.app()->getLocale().'_'.$version;

        $data = cache()->remember($cacheKey, 3600, function () {
            return CategoryTreeResource::collection(
                $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id)
            )->toArray(request());
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Get filterable attributes for category.
     */
    public function getAttributes(): JsonResource
    {
        if (! request('category_id')) {
            $filterableAttributes = $this->attributeRepository->getFilterableAttributes();

            return AttributeResource::collection($filterableAttributes);
        }

        $category = $this->categoryRepository->findOrFail(request('category_id'));

        if (empty($filterableAttributes = $category->filterableAttributes)) {
            $filterableAttributes = $this->attributeRepository->getFilterableAttributes();
        }

        return AttributeResource::collection($filterableAttributes);
    }

    /**
     * Get attribute options with pagination and search.
     */
    public function getAttributeOptions(int $attributeId): mixed
    {
        $attribute = $this->attributeRepository->findOrFail($attributeId);

        if ($attribute->type === AttributeTypeEnum::BOOLEAN->value) {
            return new JsonResponse([
                'data' => AttributeTypeEnum::getBooleanOptions(),
            ]);
        }

        $query = $attribute->options()
            ->with([
                'translation' => fn ($query) => $query->where('locale', core()->getCurrentLocale()->code),
            ]);

        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('translation', fn ($query) => $query->where('label', 'like', "%{$search}%"))
                    ->orWhere('admin_name', 'like', "%{$search}%");
            });
        }

        $query->orderBy('sort_order');

        return AttributeOptionResource::collection($query->paginate(request()->integer('per_page', 200)));
    }

    /**
     * Get product minimum and maximum price.
     */
    public function getProductPriceRange($categoryId = null): JsonResource
    {
        if (core()->getConfigData('catalog.products.search.engine') == 'elastic') {
            $searchEngine = core()->getConfigData('catalog.products.search.storefront_mode');
        }

        $productRepository = $this->productRepository
            ->setSearchEngine($searchEngine ?? 'database');

        $minPrice = $productRepository->getMinPrice(['category_id' => $categoryId]);
        $maxPrice = $productRepository->getMaxPrice(['category_id' => $categoryId]);

        return new JsonResource([
            'min_price' => core()->convertPrice($minPrice),
            'max_price' => core()->convertPrice($maxPrice),
        ]);
    }
}
