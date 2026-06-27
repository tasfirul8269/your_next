<?php

namespace Frooxi\Shop\Http\Controllers;

use Frooxi\Product\Repositories\ProductFlatRepository;
use Frooxi\Product\Repositories\SearchRepository;
use Illuminate\Http\JsonResponse;
// REMOVED: Marketing package deleted
// use Frooxi\Marketing\Repositories\SearchTermRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        // REMOVED: Marketing package deleted
        // protected SearchTermRepository $searchTermRepository,
        protected SearchRepository $searchRepository,
        protected ProductFlatRepository $productFlatRepository
    ) {}

    /**
     * Index to handle the view loaded with the search results
     *
     * @return View
     */
    public function index()
    {
        $this->validate(request(), [
            'query' => ['sometimes', 'required', 'string', 'regex:/^[^\\\\]+$/u'],
        ]);

        // REMOVED: Marketing package deleted - search term redirects disabled
        /*
        $searchTerm = $this->searchTermRepository->findOneWhere([
            'term' => request()->query('query'),
            'channel_id' => core()->getCurrentChannel()->id,
            'locale' => app()->getLocale(),
        ]);

        if ($searchTerm?->redirect_url) {
            return redirect()->to($searchTerm->redirect_url);
        }
        */

        $query = request()->query('query');

        $suggestion = null;

        if (
            ! request()->has('suggest')
            || request()->query('suggest') !== '0'
        ) {
            $searchEngine = core()->getConfigData('catalog.products.search.engine') === 'elastic'
                ? core()->getConfigData('catalog.products.search.storefront_mode')
                : 'database';

            $suggestion = $this->searchRepository
                ->setSearchEngine($searchEngine)
                ->getSuggestions($query);
        }

        return view('shop::search.index', [
            'query' => $query,
            'suggestion' => $suggestion,
            'params' => [
                'sort' => request()->query('sort'),
                'limit' => request()->query('limit'),
                'mode' => request()->query('mode'),
            ],
        ]);
    }

    /**
     * Upload image and analyze it for product search keywords.
     */
    public function upload(): JsonResponse
    {
        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $imageUrl = $this->searchRepository->uploadSearchImage(request()->all());

        return response()->json([
            'image_url' => $imageUrl,
            'keywords' => '',
            'engine' => 'tensorflow',
        ]);
    }

    /**
     * Get search suggestions for drawer.
     */
    public function suggestions(): JsonResponse
    {
        $query = request()->query('query');

        if (! $query || strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $channel = core()->getCurrentChannelCode();
        $locale = app()->getLocale();

        // Use a raw query to get products with their first image path in a single query
        $products = DB::table('product_flat')
            ->leftJoin('product_images', function ($join) {
                $join->on('product_flat.product_id', '=', 'product_images.product_id')
                    ->whereRaw('product_images.id = (SELECT MIN(pi.id) FROM product_images pi WHERE pi.product_id = product_flat.product_id)');
            })
            ->select([
                'product_flat.product_id',
                'product_flat.name',
                'product_flat.url_key',
                'product_flat.price',
                'product_flat.special_price',
                'product_images.path as image_path',
            ])
            ->where('product_flat.channel', $channel)
            ->where('product_flat.locale', $locale)
            ->where('product_flat.status', 1)
            ->where('product_flat.visible_individually', 1)
            ->where('product_flat.name', 'like', "%{$query}%")
            ->limit(8)
            ->get();

        $isCloudinary = config('filesystems.default') === 'cloudinary';

        $data = $products->map(function ($product) use ($isCloudinary) {
            $price = $product->special_price && $product->special_price < $product->price
                ? $product->special_price
                : $product->price;

            $oldPrice = $product->special_price && $product->special_price < $product->price
                ? $product->price
                : null;

            // Generate image URL without Storage::url() API calls
            $imageUrl = null;
            if ($product->image_path) {
                if ($isCloudinary) {
                    $imageUrl = cloudinary_url($product->image_path);
                } else {
                    $imageUrl = Storage::url($product->image_path);
                }
            }

            return [
                'id' => $product->product_id,
                'name' => $product->name,
                'url' => route('shop.product_or_category.index', $product->url_key),
                'image' => $imageUrl,
                'price' => $price ? core()->currency($price) : null,
                'old_price' => $oldPrice ? core()->currency($oldPrice) : null,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
