<?php

namespace Frooxi\Shop\Http\Controllers;

use Frooxi\Category\Repositories\CategoryRepository;
use Frooxi\Shop\Http\Requests\ContactRequest;
use Frooxi\Shop\Http\Resources\CategoryTreeResource;
use Frooxi\Shop\Mail\ContactUs;
use Frooxi\Shop\Repositories\FlashSaleItemRepository;
use Frooxi\Shop\Models\FlashSaleProduct;
use Frooxi\Shop\Repositories\HeroSlideRepository;
use Frooxi\Theme\Repositories\ThemeCustomizationRepository;
use Frooxi\Product\Repositories\ProductRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Using const variable for status
     */
    const STATUS = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ThemeCustomizationRepository $themeCustomizationRepository,
        protected CategoryRepository $categoryRepository,
        protected HeroSlideRepository $heroSlideRepository,
        protected FlashSaleItemRepository $flashSaleItemRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Loads the home page for the storefront.
     *
     * @return View
     */
    public function index()
    {
        $customizations = $this->themeCustomizationRepository->orderBy('sort_order')->findWhere([
            'status' => self::STATUS,
            'channel_id' => core()->getCurrentChannel()->id,
            'theme_code' => core()->getCurrentChannel()->theme,
        ]);

        $heroSlides = $this->heroSlideRepository->getActiveSlides()->load('category')->map(function ($slide) {
            // Generate link from category if category_id is set
            if ($slide->category_id && $slide->category) {
                $slide->link = url('/').'/'.($slide->category->slug ?? '');
            }

            return $slide;
        });

        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

        $categories = CategoryTreeResource::collection($categories);

        return view('shop::home.index', compact('customizations', 'categories', 'heroSlides'));
    }

    /**
     * Loads the home page for the storefront if something wrong.
     *
     * @return \Exception
     */
    public function notFound()
    {
        abort(404);
    }

    /**
     * Loads the contact us page.
     *
     * @return View
     */
    public function contactUs()
    {
        return view('shop::home.contact-us');
    }

    /**
     * Loads the all categories page.
     */
    public function allCategories(): View
    {
        // getVisibleCategoryTree($rootId) returns direct children of the root
        // category (i.e. the top-level parent categories: Men, Women, Kids…)
        $rawCategories = $this->categoryRepository
            ->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

        // Build plain objects so the Blade view can reliably read products_count
        // (JsonResource->property proxies to the underlying model and bypasses toArray).
        $categories = $rawCategories->map(function ($cat) {
            $subtreeIds = $cat->descendants()->pluck('id')->push($cat->id);

            return (object) [
                'id' => $cat->id,
                'name' => $cat->name,
                'url' => $cat->url,
                'logo_url' => $cat->logo_url,
                'banner_url' => $cat->banner_url,
                'products_count' => DB::table('product_categories')
                    ->whereIn('category_id', $subtreeIds)
                    ->distinct('product_id')
                    ->count('product_id'),
            ];
        });

        return view('shop::home.all-categories', compact('categories'));
    }

    /**
     * Loads the flash sale page.
     */
    public function flashSale(): View
    {
        // Get products with flash_sale_discount > 0 using the repository
        $flashSaleProducts = $this->productRepository->getAll([
            'is_flash_sale_page' => 1,
            'status'             => 1,
        ]);

        return view('shop::home.flash-sale', compact('flashSaleProducts'));
    }

    /**
     * Summary of store.
     *
     * @return RedirectResponse
     */
    public function sendContactUsMail(ContactRequest $contactRequest)
    {
        try {
            Mail::queue(new ContactUs($contactRequest->only([
                'name',
                'email',
                'contact',
                'message',
            ])));

            session()->flash('success', trans('shop::app.home.thanks-for-contact'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            report($e);
        }

        return back();
    }
}
