<?php

namespace Frooxi\Admin\Http\Controllers;

use Carbon\Carbon;
use Frooxi\Admin\Helpers\Dashboard;
use Frooxi\Sales\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Request param functions
     *
     * @var array
     */
    protected $typeFunctions = [
        'over-all' => 'getOverAllStats',
        'today' => 'getTodayStats',
        'stock-threshold-products' => 'getStockThresholdProducts',
        'total-sales' => 'getSalesStats',
        'top-selling-products' => 'getTopSellingProducts',
        'top-customers' => 'getTopCustomers',
    ];

    /**
     * Create a controller instance.
     *
     * @return void
     */
    public function __construct(protected Dashboard $dashboardHelper) {}

    /**
     * Dashboard page.
     *
     * @return View|JsonResponse
     */
    public function index()
    {
        // Handle date filter from request
        if (request()->has('start') && request()->has('end')) {
            $startDate = Carbon::parse(request('start'));
            $endDate = Carbon::parse(request('end'));
        } else {
            $startDate = $this->dashboardHelper->getStartDate();
            $endDate = $this->dashboardHelper->getEndDate();
        }

        // Get stats for the selected date range
        $overAllStats = $this->dashboardHelper->getOverAllStats();

        // Calculate pending orders
        $pendingOrders = Order::where('status', 'pending')->count();

        // Get today's stats separately
        $todayStats = $this->dashboardHelper->getTodayStats();

        return view('admin::dashboard.index')->with([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statistics' => [
                'today_orders' => $todayStats['total_orders']['current'] ?? 0,
                'today_revenue' => $todayStats['total_sales']['current'] ?? 0,
                'pending_orders' => $pendingOrders,
                'total_customers' => $overAllStats['total_customers']['current'] ?? 0,
                'total_orders' => $overAllStats['total_orders']['current'] ?? 0,
                'total_sales' => $overAllStats['total_sales']['current'] ?? 0,
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function stats()
    {
        $stats = $this->dashboardHelper->{$this->typeFunctions[request()->query('type')]}();

        return response()->json([
            'statistics' => $stats,
            'date_range' => $this->dashboardHelper->getDateRange(),
        ]);
    }
}
