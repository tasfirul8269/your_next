<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.dashboard.index.title')
    </x-slot>

    <!-- Dashboard - Modern AdminV2 Style -->
    <div class="p-6 space-y-6" style="font-family:'Montserrat',sans-serif;">

        <!-- Welcome Section + Date Filter -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2" v-pre>
                    Welcome, {{ auth()->guard('admin')->user()->name }}
                </h1>
                <p class="text-base text-gray-600">
                    Your store dashboard — monitor sales, orders, and inventory at a glance.
                </p>
            </div>

            <!-- Date Filter -->
            <div class="flex-shrink-0">
                <v-dashboard-filters>
                    <div class="flex gap-2">
                        <div class="shimmer h-[36px] w-[132px] rounded-md"></div>
                        <div class="shimmer h-[36px] w-[140px] rounded-md"></div>
                    </div>
                </v-dashboard-filters>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {!! view_render_event('frooxi.admin.dashboard.overall_details.before') !!}
            @include('admin::dashboard.over-all-details')
            {!! view_render_event('frooxi.admin.dashboard.overall_details.after') !!}
        </div>

        <!-- Today's Status Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Today's Orders -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Today's Orders</h3>
                    <span class="text-2xl icon-shopping-bag text-gray-400"></span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $statistics['today_orders'] ?? 0 }}</p>
                <p class="text-sm text-gray-500">Orders placed today</p>
            </div>

            <!-- Today's Revenue -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Today's Revenue</h3>
                    <span class="text-2xl icon-currency text-gray-400"></span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ core()->formatPrice($statistics['today_revenue'] ?? 0) }}</p>
                <p class="text-sm text-gray-500">Revenue earned today</p>
            </div>

            <!-- Pending Orders -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Pending Orders</h3>
                    <span class="text-2xl icon-clock text-gray-400"></span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $statistics['pending_orders'] ?? 0 }}</p>
                <p class="text-sm text-gray-500">Awaiting processing</p>
            </div>
        </div>

        <!-- Bottom Section: Stock Alerts + Best Selling Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stock Alerts -->
            <div>
                {!! view_render_event('frooxi.admin.dashboard.stock_threshold.before') !!}
                @include('admin::dashboard.stock-threshold-products')
                {!! view_render_event('frooxi.admin.dashboard.stock_threshold.after') !!}
            </div>

            <!-- Best Selling Products -->
            <div>
                {!! view_render_event('frooxi.admin.dashboard.top_selling.before') !!}
                @include('admin::dashboard.top-selling-products')
                {!! view_render_event('frooxi.admin.dashboard.top_selling.after') !!}
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="mt-6">
            {!! view_render_event('frooxi.admin.dashboard.todays_details.before') !!}
            @include('admin::dashboard.todays-details')
            {!! view_render_event('frooxi.admin.dashboard.todays_details.after') !!}
        </div>

    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-dashboard-filters-template"
        >
            <div class="flex flex-wrap gap-2 items-center">
                <x-admin::flat-picker.date class="!w-[140px]" ::allow-input="false">
                    <input
                        class="flex h-[36px] w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        v-model="filters.start"
                        placeholder="@lang('admin::app.dashboard.index.start-date')"
                    />
                </x-admin::flat-picker.date>

                <x-admin::flat-picker.date class="!w-[140px]" ::allow-input="false">
                    <input
                        class="flex h-[36px] w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        v-model="filters.end"
                        placeholder="@lang('admin::app.dashboard.index.end-date')"
                    />
                </x-admin::flat-picker.date>

                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-1.5 text-sm font-medium text-white transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @click="applyFilter"
                >
                    <span class="icon-search text-base"></span>
                    @lang('admin::app.dashboard.index.apply-filter')
                </button>
            </div>
        </script>

        <script type="module">
            app.component('v-dashboard-filters', {
                template: '#v-dashboard-filters-template',

                data() {
                    return {
                        filters: {
                            start: "{{ $startDate->format('Y-m-d') }}",
                            end: "{{ $endDate->format('Y-m-d') }}",
                        }
                    }
                },

                methods: {
                    applyFilter() {
                        // Reload dashboard with new date range
                        const params = new URLSearchParams({
                            start: this.filters.start,
                            end: this.filters.end
                        });
                        window.location.href = '{{ route('admin.dashboard.index') }}?' + params.toString();
                    }
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
