<?php

namespace Frooxi\Admin\Http\Controllers\Api;

use Frooxi\Admin\Http\Controllers\Controller;
use Frooxi\Customer\Repositories\CustomerRepository;
use Frooxi\Product\Repositories\ProductRepository;
use Frooxi\Sales\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected CustomerRepository $customerRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Resolve date range from period query param.
     */
    protected function resolveDateRange(string $period): array
    {
        $now = now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            '7days' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            '30days' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            default => [null, null],
        };
    }

    /**
     * Get dashboard stats.
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'all');
            [$from, $to] = $this->resolveDateRange($period);

            $totalRevenue = $this->orderRepository->scopeQuery(function ($query) use ($from, $to) {
                $query->whereIn('status', ['completed', 'processing']);
                if ($from && $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                }

                return $query;
            })->sum('base_grand_total') ?? 0;

            $totalOrders = $this->orderRepository->scopeQuery(function ($query) use ($from, $to) {
                if ($from && $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                }

                return $query;
            })->count();

            $totalCustomers = $this->customerRepository->count();
            $activeProducts = $this->productRepository->count();

            $recentOrders = $this->orderRepository->with(['items'])
                ->orderBy('id', 'desc')
                ->paginate(5);

            return response()->json([
                'data' => [
                    'total_revenue' => (float) $totalRevenue,
                    'total_orders' => $totalOrders,
                    'total_customers' => $totalCustomers,
                    'active_products' => $activeProducts,
                    'recent_orders' => $recentOrders,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Get revenue trend data (monthly for current year or filtered by period).
     */
    public function revenueTrend(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'year');
            [$from, $to] = $this->resolveDateRange($period);
            $currentYear = date('Y');

            $revenueByMonth = $this->orderRepository->scopeQuery(function ($query) use ($currentYear, $from, $to) {
                $query->whereIn('status', ['completed', 'processing']);
                if ($from && $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                } else {
                    $query->whereYear('created_at', $currentYear);
                }

                return $query
                    ->selectRaw('MONTH(created_at) as month, SUM(base_grand_total) as total')
                    ->groupBy('month')
                    ->orderBy('month');
            })->get();

            // Fill in missing months with 0
            $monthlyData = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyData[$i] = 0;
            }

            foreach ($revenueByMonth as $item) {
                $monthlyData[$item->month] = (float) $item->total;
            }

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $labels = [];
            $data = [];

            foreach ($monthlyData as $month => $total) {
                $labels[] = $months[$month - 1];
                $data[] = $total;
            }

            return response()->json([
                'data' => [
                    'labels' => $labels,
                    'values' => $data,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get orders overview data (daily for current week or filtered by period).
     */
    public function ordersOverview(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '7days');
            [$from, $to] = $this->resolveDateRange($period);

            if (! $from) {
                $from = now()->startOfWeek();
                $to = now()->endOfWeek();
            }

            $ordersByDay = $this->orderRepository->scopeQuery(function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from, $to])
                    ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as total')
                    ->groupBy('day');
            })->get();

            // Fill in missing days with 0
            $dailyData = [];
            for ($i = 1; $i <= 7; $i++) {
                $dailyData[$i] = 0;
            }

            foreach ($ordersByDay as $item) {
                $dailyData[$item->day] = (int) $item->total;
            }

            $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $labels = [];
            $data = [];

            // Reorder to start from Monday
            $orderedDays = [2, 3, 4, 5, 6, 7, 1];
            foreach ($orderedDays as $day) {
                $labels[] = $days[$day - 1];
                $data[] = $dailyData[$day];
            }

            return response()->json([
                'data' => [
                    'labels' => $labels,
                    'values' => $data,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top selling products.
     */
    public function topProducts(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'year');
            [$from, $to] = $this->resolveDateRange($period);

            $topProducts = $this->orderRepository->scopeQuery(function ($query) use ($from, $to) {
                $query->with(['items.product'])
                    ->whereIn('status', ['completed', 'processing']);

                if ($from && $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                }

                return $query->orderBy('id', 'desc')->limit(50);
            })->get();

            // Aggregate product sales
            $productSales = [];
            foreach ($topProducts as $order) {
                foreach ($order->items as $item) {
                    $productId = $item->product_id;
                    if (! isset($productSales[$productId])) {
                        $productSales[$productId] = [
                            'product_id' => $productId,
                            'name' => $item->name,
                            'quantity_sold' => 0,
                            'revenue' => 0,
                        ];
                    }
                    $productSales[$productId]['quantity_sold'] += $item->qty_ordered;
                    $productSales[$productId]['revenue'] += (float) $item->base_total;
                }
            }

            // Sort by quantity sold and take top 4
            usort($productSales, function ($a, $b) {
                return $b['quantity_sold'] <=> $a['quantity_sold'];
            });

            $topProducts = array_slice($productSales, 0, 4);

            return response()->json([
                'data' => $topProducts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock alert data (low inventory products).
     */
    public function stockAlert(): JsonResponse
    {
        try {
            $lowStockProducts = $this->productRepository->with(['inventories'])
                ->scopeQuery(function ($query) {
                    return $query->whereHas('inventories', function ($q) {
                        $q->where('qty', '<=', 5);
                    });
                })
                ->all();

            $alertData = [];
            $count = 0;
            foreach ($lowStockProducts as $product) {
                if ($count >= 5) {
                    break;
                }

                $totalQty = $product->inventories->sum('qty');
                $alertData[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $totalQty,
                ];
                $count++;
            }

            return response()->json([
                'data' => $alertData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer distribution.
     */
    public function customerDistribution(): JsonResponse
    {
        try {
            $totalCustomers = $this->customerRepository->count();

            // Count guest orders (unique emails not in customers table)
            $guestEmails = $this->orderRepository->scopeQuery(function ($query) {
                return $query->where('is_guest', 1)
                    ->distinct()
                    ->pluck('customer_email');
            })->unique();

            $guestCount = $guestEmails->count();
            $newCustomers = $this->customerRepository->scopeQuery(function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })->count();

            $returningCustomers = max(0, $totalCustomers - $newCustomers);

            return response()->json([
                'data' => [
                    'new' => $newCustomers,
                    'returning' => $returningCustomers,
                    'guest' => $guestCount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
