<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Customer\Repositories\CustomerGroupRepository;
use Frooxi\Customer\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerGroupRepository $customerGroupRepository
    ) {}

    /**
     * Get customers.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->customerRepository->paginate($request->get('limit', 10)),
        ]);
    }

    /**
     * Get customer details.
     */
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerRepository->with(['orders', 'addresses'])->find($id);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        return response()->json([
            'data' => $customer,
        ]);
    }

    /**
     * Create a new customer.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'status' => 'boolean',
        ]);

        try {
            $data = $request->only([
                'first_name',
                'last_name',
                'email',
                'gender',
                'date_of_birth',
                'phone',
                'status',
            ]);

            $data['password'] = Hash::make($request->input('password'));
            $data['status'] = $request->input('status', 1);
            $data['is_verified'] = 1;

            // Assign to general customer group if not specified
            $group = $this->customerGroupRepository->findOneByField('code', 'general');
            $data['customer_group_id'] = $group ? $group->id : 2;

            $customer = $this->customerRepository->create($data);

            return response()->json([
                'data' => $customer,
                'message' => 'Customer created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update customer.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        $request->validate([
            'email' => 'email|unique:customers,email,'.$id,
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'status' => 'boolean',
        ]);

        try {
            $data = $request->only([
                'first_name',
                'last_name',
                'email',
                'gender',
                'date_of_birth',
                'phone',
                'status',
            ]);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }

            $customer = $this->customerRepository->update($data, $id);

            return response()->json([
                'data' => $customer,
                'message' => 'Customer updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete customer.
     */
    public function destroy(int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        try {
            $this->customerRepository->delete($id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
