<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Shop\Models\FlashSaleProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FlashSaleController extends Controller
{
    /**
     * Get flash sale products.
     */
    public function index(): JsonResponse
    {
        $products = FlashSaleProduct::orderBy('sort_order', 'ASC')->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Store flash sale product.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'sku' => 'required|unique:flash_sale_products,sku',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'required|integer|min:1|max:99',
            'quantity' => 'nullable|integer|min:0',
            'image_file' => 'nullable|file|max:10240',
        ]);

        $data = $request->only(['sku', 'name', 'description', 'price', 'discount_percentage', 'quantity']);
        $data['channel_id'] = core()->getCurrentChannel()->id;

        // Handle image upload
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $data['image_path'] = cloudinary_upload($file, 'flash-sale-products', null, null, true);
        }

        // Get max sort order
        $maxSortOrder = FlashSaleProduct::where('channel_id', $data['channel_id'])->max('sort_order');
        $data['sort_order'] = ($maxSortOrder ?? 0) + 1;
        $data['status'] = true;

        $product = FlashSaleProduct::create($data);

        return response()->json([
            'data' => $product,
            'message' => 'Flash sale product created successfully.',
        ]);
    }

    /**
     * Update flash sale product.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = FlashSaleProduct::find($id);

        if (! $product) {
            return response()->json(['message' => 'Flash sale product not found.'], 404);
        }

        $request->validate([
            'sku' => 'required|unique:flash_sale_products,sku,' . $id,
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'required|integer|min:1|max:99',
            'quantity' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['sku', 'name', 'description', 'price', 'discount_percentage', 'quantity', 'status', 'sort_order']);

        // Handle image upload
        if ($request->hasFile('image_file')) {
            $request->validate([
                'image_file' => 'file|max:10240',
            ]);

            $file = $request->file('image_file');

            // Delete old image
            if ($product->image_path) {
                Storage::disk(config('filesystems.default'))->delete($product->image_path);
            }

            $data['image_path'] = cloudinary_upload($file, 'flash-sale-products', null, null, true);
        }

        // Cast status
        if (isset($data['status'])) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
        }

        $product->update($data);

        return response()->json([
            'message' => 'Flash sale product updated successfully.',
            'data' => $product
        ]);
    }

    /**
     * Delete flash sale product.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = FlashSaleProduct::find($id);

        if (! $product) {
            return response()->json([
                'message' => 'Flash sale product not found',
            ], 404);
        }

        // Delete image
        if ($product->image_path) {
            Storage::disk(config('filesystems.default'))->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'message' => 'Flash sale product deleted successfully.',
        ]);
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $product = FlashSaleProduct::find($id);

        if (! $product) {
            return response()->json([
                'message' => 'Flash sale product not found',
            ], 404);
        }

        $product->update([
            'status' => ! $product->status,
        ]);

        return response()->json([
            'message' => 'Flash sale product status toggled successfully.',
            'status' => ! $product->status,
        ]);
    }

    /**
     * Mass update sort orders.
     */
    public function massUpdate(): JsonResponse
    {
        $orders = request('orders', []);

        foreach ($orders as $item) {
            FlashSaleProduct::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
            ]);
        }

        return new JsonResponse([
            'message' => 'Flash sale product order updated successfully.',
        ]);
    }
}
