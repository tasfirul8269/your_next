<?php

namespace Frooxi\Admin\Http\Controllers\Settings;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Shipping\Repositories\ShippingMethodRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ShippingMethodController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ShippingMethodRepository $shippingMethodRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $shippingMethods = $this->shippingMethodRepository->all();

            $data = $shippingMethods->map(function ($method) {
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'description' => $method->description ?? '',
                    'price' => core()->formatBasePrice($method->price),
                    'sort_order' => $method->sort_order,
                    'status' => $method->status ? __('shipping::app.common.enable') : __('shipping::app.common.disable'),
                    'actions' => '
                        <div class="flex items-center justify-center gap-x-1">
                            <button 
                                onclick="editShippingMethod('.$method->id.')" 
                                class="cursor-pointer rounded-md p-1.5 text-lg transition-all hover:bg-gray-100 dark:hover:bg-gray-800"
                            >
                                <span class="icon-edit text-xl"></span>
                            </button>
                            <button 
                                onclick="deleteShippingMethod('.$method->id.')" 
                                class="cursor-pointer rounded-md p-1.5 text-lg transition-all hover:bg-gray-100 dark:hover:bg-gray-800"
                            >
                                <span class="icon-delete text-xl"></span>
                            </button>
                        </div>
                    ',
                ];
            });

            return response()->json([
                'data' => $data,
            ]);
        }

        return view('shipping::admin.shipping-methods.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('shipping::app.validation-error'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->shippingMethodRepository->create(request()->only([
            'name',
            'description',
            'price',
            'status',
            'sort_order',
        ]));

        return new JsonResponse([
            'message' => __('shipping::app.create-success'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): JsonResponse
    {
        $shippingMethod = $this->shippingMethodRepository->find($id);

        if (! $shippingMethod) {
            return new JsonResponse([
                'message' => __('shipping::app.not-found'),
            ], 404);
        }

        return new JsonResponse([
            'data' => $shippingMethod,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('shipping::app.validation-error'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->shippingMethodRepository->update(request()->only([
            'name',
            'description',
            'price',
            'status',
            'sort_order',
        ]), $id);

        return new JsonResponse([
            'message' => __('shipping::app.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $shippingMethod = $this->shippingMethodRepository->find($id);

        if (! $shippingMethod) {
            return new JsonResponse([
                'message' => __('shipping::app.not-found'),
            ], 404);
        }

        $this->shippingMethodRepository->delete($id);

        return new JsonResponse([
            'message' => __('shipping::app.delete-success'),
        ]);
    }
}
