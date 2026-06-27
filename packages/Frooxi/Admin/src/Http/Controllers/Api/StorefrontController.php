<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Shop\Models\HeroSlide;
use Frooxi\Shop\Repositories\HeroSlideRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorefrontController extends Controller
{
    public function __construct(
        protected HeroSlideRepository $heroSlideRepository
    ) {}

    /**
     * Get hero slides.
     */
    public function getHeroSlides(): JsonResponse
    {
        $slides = $this->heroSlideRepository
            ->getModel()
            ->with('category')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->map(function ($slide) {
                $slide->media_url = $slide->media_path
                    ? (function_exists('cloudinary_url') ? cloudinary_url($slide->media_path) : asset('storage/'.$slide->media_path))
                    : null;
                $slide->category_name = $slide->category?->name ?? null;

                return $slide;
            });

        return response()->json([
            'data' => $slides,
        ]);
    }

    /**
     * Save hero slide.
     */
    public function saveHeroSlide(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required',
        ]);

        $slide = HeroSlide::updateOrCreate(
            ['id' => $request->get('id')],
            $request->only(['title', 'subtitle', 'image', 'link', 'sort_order'])
        );

        return response()->json([
            'data' => $slide,
            'message' => 'Hero slide saved successfully.',
        ]);
    }

    /**
     * Update hero slide.
     */
    public function updateHeroSlide(Request $request, int $id): JsonResponse
    {
        $slide = $this->heroSlideRepository->find($id);

        if (! $slide) {
            return response()->json(['message' => 'Slide not found.'], 404);
        }

        $data = $request->only(['title', 'status', 'sort_order', 'category_id']);

        if ($request->has('category_id')) {
            $data['link'] = null;
        }

        // Handle new media upload
        if ($request->hasFile('media_file')) {
            $request->validate([
                'media_file' => 'file|max:51200',
            ]);

            $file = $request->file('media_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $isVideo = in_array($extension, ['mp4', 'webm', 'mov', 'avi']);

            // Delete old media
            if ($slide->media_path) {
                Storage::disk(config('filesystems.default'))->delete($slide->media_path);
            }

            $path = cloudinary_upload($file, 'hero-slides', null, null, ! $isVideo);
            $data['media_path'] = $path;
            $data['type'] = $isVideo ? 'video' : 'image';
        }

        if (isset($data['status'])) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
        }

        $this->heroSlideRepository->update($data, $id);

        return response()->json([
            'message' => 'Hero slide updated successfully.',
        ]);
    }

    /**
     * Delete hero slide.
     */
    public function deleteHeroSlide(int $id): JsonResponse
    {
        $slide = $this->heroSlideRepository->find($id);

        if (! $slide) {
            return response()->json([
                'message' => 'Slide not found',
            ], 404);
        }

        if ($slide->media_path) {
            Storage::disk(config('filesystems.default'))->delete($slide->media_path);
        }

        $this->heroSlideRepository->delete($id);

        return response()->json([
            'message' => 'Hero slide deleted successfully.',
        ]);
    }

    /**
     * Toggle slide status.
     */
    public function toggleSlideStatus(int $id): JsonResponse
    {
        $slide = $this->heroSlideRepository->find($id);

        if (! $slide) {
            return response()->json([
                'message' => 'Slide not found',
            ], 404);
        }

        $this->heroSlideRepository->update(['status' => ! $slide->status], $id);

        $slide->refresh();

        return response()->json([
            'data' => $slide,
            'message' => 'Slide status updated successfully.',
        ]);
    }

    /**
     * Reorder hero slides.
     */
    public function reorderHeroSlides(Request $request): JsonResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:hero_slides,id',
            'orders.*.sort_order' => 'required|integer',
        ]);

        try {
            foreach ($request->input('orders') as $item) {
                $this->heroSlideRepository->update([
                    'sort_order' => $item['sort_order'],
                ], $item['id']);
            }

            return response()->json([
                'message' => 'Slide order updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
