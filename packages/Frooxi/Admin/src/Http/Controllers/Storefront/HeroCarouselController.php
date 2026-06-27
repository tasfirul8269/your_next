<?php

namespace Frooxi\Admin\Http\Controllers\Storefront;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Category\Repositories\CategoryRepository;
use Frooxi\Shop\Repositories\HeroSlideRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HeroCarouselController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        public HeroSlideRepository $heroSlideRepository,
        public CategoryRepository $categoryRepository
    ) {}

    /**
     * Display the hero carousel management page.
     */
    public function index(): View
    {
        $channelId = core()->getRequestedChannel()->id;

        $slides = $this->heroSlideRepository->findWhere([
            'channel_id' => $channelId,
        ], ['*'], 'sort_order')->map(function ($slide) {
            $slide->media_url = cloudinary_url($slide->media_path);

            // Add category name if category exists
            if ($slide->category_id && $slide->category) {
                $slide->category_name = $slide->category->name ?? 'Category #'.$slide->category_id;
            } else {
                $slide->category_name = null;
            }

            return $slide;
        });

        $channels = core()->getAllChannels();

        // Get all active categories as a hierarchical tree
        $categories = $this->getCategoryHierarchy();

        return view('admin::storefront.hero-carousel.index', compact('slides', 'channels', 'channelId', 'categories'));
    }

    /**
     * Get categories in hierarchical tree structure.
     */
    protected function getCategoryHierarchy(): array
    {
        $categories = $this->categoryRepository
            ->getModel()
            ->with('translations')
            ->where('status', 1)
            ->orderBy('position', 'ASC')
            ->get()
            ->toTree();

        return $this->flattenCategoryTree($categories);
    }

    /**
     * Flatten category tree into a structured array with indentation levels.
     */
    protected function flattenCategoryTree($categories, int $level = 0, array &$result = []): array
    {
        foreach ($categories as $category) {
            $result[] = [
                'id' => $category->id,
                'name' => $category->name ?? 'Category #'.$category->id,
                'slug' => $category->slug,
                'level' => $level,
            ];

            if ($category->children && $category->children->count() > 0) {
                $this->flattenCategoryTree($category->children, $level + 1, $result);
            }
        }

        return $result;
    }

    /**
     * Store a new hero slide.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'media_file' => 'required|file|max:51200',
            'type' => 'required|in:image,video',
            'channel_id' => 'required|exists:channels,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $file = request()->file('media_file');
        $type = request('type');
        $extension = strtolower($file->getClientOriginalExtension());
        $isVideo = in_array($extension, ['mp4', 'webm', 'mov', 'avi']);

        $path = cloudinary_upload($file, 'hero-slides', null, null, ! $isVideo);

        $maxSortOrder = $this->heroSlideRepository->getModel()
            ->where('channel_id', request('channel_id'))
            ->max('sort_order');

        $slide = $this->heroSlideRepository->create([
            'type' => $type,
            'title' => request('title'),
            'link' => null, // Link will be generated from category
            'category_id' => request('category_id'),
            'media_path' => $path,
            'sort_order' => ($maxSortOrder ?? 0) + 1,
            'status' => 1,
            'channel_id' => request('channel_id'),
        ]);

        return new JsonResponse([
            'message' => 'Hero slide added successfully.',
            'slide' => $slide,
        ]);
    }

    /**
     * Update an existing hero slide.
     */
    public function update(int $id): JsonResponse
    {
        $slide = $this->heroSlideRepository->find($id);

        if (! $slide) {
            return new JsonResponse(['message' => 'Slide not found.'], 404);
        }

        $data = request()->only(['title', 'status', 'sort_order', 'category_id']);

        // If category_id is provided, clear the manual link
        if (request()->has('category_id')) {
            $data['link'] = null;
        }

        // Handle new media upload
        if (request()->hasFile('media_file')) {
            $this->validate(request(), [
                'media_file' => 'file|max:51200',
            ]);

            $file = request()->file('media_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $isVideo = in_array($extension, ['mp4', 'webm', 'mov', 'avi']);

            // Delete old media
            $oldPath = $slide->media_path;
            Storage::disk(config('filesystems.default'))->delete($oldPath);

            $path = cloudinary_upload($file, 'hero-slides', null, null, ! $isVideo);

            $data['media_path'] = $path;
            $data['type'] = $isVideo ? 'video' : 'image';
        }

        // Cast status
        if (isset($data['status'])) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
        }

        $this->heroSlideRepository->update($data, $id);

        return new JsonResponse([
            'message' => 'Hero slide updated successfully.',
        ]);
    }

    /**
     * Delete a hero slide.
     */
    public function destroy(int $id): JsonResponse
    {
        $slide = $this->heroSlideRepository->find($id);

        if (! $slide) {
            return new JsonResponse(['message' => 'Slide not found.'], 404);
        }

        // Delete media file
        $oldPath = $slide->media_path;
        Storage::disk(config('filesystems.default'))->delete($oldPath);

        $this->heroSlideRepository->delete($id);

        return new JsonResponse([
            'message' => 'Hero slide deleted successfully.',
        ]);
    }

    /**
     * Mass update sort orders.
     */
    public function massUpdate(): JsonResponse
    {
        $orders = request('orders', []);

        foreach ($orders as $item) {
            $this->heroSlideRepository->update([
                'sort_order' => $item['sort_order'],
            ], $item['id']);
        }

        return new JsonResponse([
            'message' => 'Slide order updated successfully.',
        ]);
    }
}
