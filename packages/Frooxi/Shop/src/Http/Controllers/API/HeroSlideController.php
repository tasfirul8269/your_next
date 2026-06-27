<?php

namespace Frooxi\Shop\Http\Controllers\API;

use Frooxi\Shop\Http\Controllers\Controller;
use Frooxi\Shop\Repositories\HeroSlideRepository;
use Illuminate\Http\JsonResponse;

class HeroSlideController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected HeroSlideRepository $heroSlideRepository) {}

    /**
     * Get all active hero slides.
     */
    public function index(): JsonResponse
    {
        $slides = $this->heroSlideRepository->getActiveSlides()->load('category');

        return new JsonResponse([
            'data' => $slides->map(function ($slide) {
                // Generate link from category if category_id is set
                $link = null;
                if ($slide->category_id && $slide->category) {
                    $link = url('/').'/'.($slide->category->slug ?? '');
                } elseif ($slide->link) {
                    $link = $slide->link;
                }

                return [
                    'id' => $slide->id,
                    'type' => $slide->type,
                    'title' => $slide->title,
                    'link' => $link,
                    'category_id' => $slide->category_id,
                    'media_url' => cloudinary_url($slide->media_path),
                    'sort_order' => $slide->sort_order,
                ];
            }),
        ]);
    }
}
