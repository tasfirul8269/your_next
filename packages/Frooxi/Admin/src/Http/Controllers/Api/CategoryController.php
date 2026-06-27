<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Category\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepository $categoryRepository
    ) {}

    /**
     * Get categories as tree structure.
     *
     * Returns the children of the root category (e.g. Men, Women, Kids)
     * with their subcategories nested recursively. The root category
     * itself (parent_id = null) is excluded from the response.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Cache the category tree for 1 hour to improve performance
            $categories = Cache::remember('shop_categories_tree', 3600, function () {
                $root = $this->categoryRepository
                    ->getModel()
                    ->whereNull('parent_id')
                    ->first();

                if (! $root) {
                    return [];
                }

                return $this->categoryRepository
                    ->getModel()
                    ->where('parent_id', $root->id)
                    ->with(['children' => function ($query) {
                        $query->with(['children' => function ($q2) {
                            $q2->with('children')->orderBy('position');
                        }])->orderBy('position');
                    }])
                    ->orderBy('position')
                    ->get()
                    ->map(fn ($cat) => $this->formatCategory($cat))
                    ->values()
                    ->toArray();
            });

            return response()->json([
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recursively format a category and its children into an array.
     */
    private function formatCategory($cat): array
    {
        $data = [
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
            'parent_id' => $cat->parent_id,
            'position' => $cat->position,
            'status' => (bool) $cat->status,
            'logo_url' => $cat->logo_url ?? null,
            'banner_url' => $cat->banner_url ?? null,
            'products_count' => $cat->products()->count(),
            'children' => [],
        ];

        if ($cat->relationLoaded('children') && $cat->children->isNotEmpty()) {
            $data['children'] = $cat->children
                ->map(fn ($child) => $this->formatCategory($child))
                ->values()
                ->toArray();
        }

        return $data;
    }

    /**
     * Get category details.
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryRepository->with(['translations', 'children', 'parent'])->find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        return response()->json([
            'data' => $category,
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:category_translations,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'boolean',
        ]);

        try {
            Event::dispatch('catalog.category.create.before');

            $data = $request->only([
                'name',
                'slug',
                'description',
                'parent_id',
                'status',
                'position',
                'display_mode',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'logo_path',
                'banner_path',
            ]);

            $data['locale'] = $request->get('locale', 'en');

            if (! empty($data['description'])) {
                $data['description'] = clean_content($data['description']);
            }

            $category = $this->categoryRepository->create($data);

            Event::dispatch('catalog.category.create.after', $category);

            $category->load(['translations', 'children']);

            return response()->json([
                'data' => $category,
                'message' => 'Category created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $request->validate([
            'slug' => 'string|unique:category_translations,slug,'.$id.',category_id',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'boolean',
        ]);

        try {
            Event::dispatch('catalog.category.update.before', $id);

            $data = $request->only([
                'name',
                'slug',
                'description',
                'parent_id',
                'status',
                'position',
                'display_mode',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'logo_path',
                'banner_path',
            ]);

            $data['locale'] = $request->get('locale', 'en');

            if (! empty($data['description'])) {
                $data['description'] = clean_content($data['description']);
            }

            $category = $this->categoryRepository->update($data, $id);

            Event::dispatch('catalog.category.update.after', $category);

            $category->load(['translations', 'children']);

            return response()->json([
                'data' => $category,
                'message' => 'Category updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a category.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        if ($category->children && $category->children->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category that has child categories.',
            ], 422);
        }

        try {
            Event::dispatch('catalog.category.delete.before', $id);

            $this->categoryRepository->delete($id);

            Event::dispatch('catalog.category.delete.after', $id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.position' => 'required|integer',
        ]);

        try {
            foreach ($request->input('categories') as $item) {
                $data = ['position' => $item['position']];

                if (isset($item['parent_id'])) {
                    $data['parent_id'] = $item['parent_id'];
                }

                $this->categoryRepository->update($data, $item['id']);
            }

            return response()->json([
                'message' => 'Categories reordered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
