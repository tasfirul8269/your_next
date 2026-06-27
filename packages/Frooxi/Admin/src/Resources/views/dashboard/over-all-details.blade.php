<!-- Over Details Vue Component -->
<v-dashboard-overall-details>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.over-all-details />
</v-dashboard-overall-details>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-overall-details-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.over-all-details />
        </template>

        <!-- Total Sales Section -->
        <template v-else>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Sales -->
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-[#D4A84B]/10 p-3">
                            <img
                                src="{{ frooxi_asset('images/total-sales.svg')}}"
                                title="@lang('admin::app.dashboard.index.total-sales')"
                                class="h-5 w-5 text-[#D4A84B]"
                            >
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-900 mb-0.5">
                                @{{ report.statistics.total_sales.formatted_total }}
                            </p>
                            <p class="text-sm text-gray-500 mb-0">
                                @lang('admin::app.dashboard.index.total-sales')
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="[report.statistics.total_sales.progress >= 0 ? 'bg-[#D4A84B]/10 text-[#D4A84B]' : 'bg-red-50 text-red-600']"
                        >
                            <span :class="[report.statistics.total_sales.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                            @{{ Math.abs(report.statistics.total_sales.progress.toFixed(2)) }}%
                        </span>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-[#D4A84B]/10 p-3">
                            <img
                                src="{{ frooxi_asset('images/total-orders.svg')}}"
                                title="@lang('admin::app.dashboard.index.total-orders')"
                                class="h-5 w-5 text-[#D4A84B]"
                            >
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-900 mb-0.5">
                                @{{ report.statistics.total_orders.current }}
                            </p>
                            <p class="text-sm text-gray-500 mb-0">
                                @lang('admin::app.dashboard.index.total-orders')
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="[report.statistics.total_orders.progress >= 0 ? 'bg-[#D4A84B]/10 text-[#D4A84B]' : 'bg-red-50 text-red-600']"
                        >
                            <span :class="[report.statistics.total_orders.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                            @{{ Math.abs(report.statistics.total_orders.progress.toFixed(2)) }}%
                        </span>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-[#D4A84B]/10 p-3">
                            <img
                                src="{{ frooxi_asset('images/customers.svg')}}"
                                title="@lang('admin::app.dashboard.index.total-customers')"
                                class="h-5 w-5 text-[#D4A84B]"
                            >
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-900 mb-0.5">
                                @{{ report.statistics.total_customers.current }}
                            </p>
                            <p class="text-sm text-gray-500 mb-0">
                                @lang('admin::app.dashboard.index.total-customers')
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="[report.statistics.total_customers.progress >= 0 ? 'bg-[#D4A84B]/10 text-[#D4A84B]' : 'bg-red-50 text-red-600']"
                        >
                            <span :class="[report.statistics.total_customers.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                            @{{ Math.abs(report.statistics.total_customers.progress.toFixed(2)) }}%
                        </span>
                    </div>
                </div>

                <!-- Average Sales & Unpaid Invoices -->
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4">
                        <!-- Average Sales -->
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-[#D4A84B]/10 p-3">
                                <img
                                    src="{{ frooxi_asset('images/average-orders.svg')}}"
                                    title="@lang('admin::app.dashboard.index.average-sale')"
                                    class="h-5 w-5 text-[#D4A84B]"
                                >
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 mb-0">
                                    @{{ report.statistics.avg_sales.formatted_total }}
                                </p>
                                <p class="text-sm text-gray-500 mb-0">
                                    @lang('admin::app.dashboard.index.average-sale')
                                </p>
                            </div>
                        </div>

                        <!-- Unpaid Invoices -->
                        <div class="flex items-center gap-3 border-t border-gray-50 pt-4">
                            <div class="rounded-xl bg-gray-100 p-3">
                                <img
                                    src="{{ frooxi_asset('images/unpaid-invoices.svg')}}"
                                    title="@lang('admin::app.dashboard.index.total-unpaid-invoices')"
                                    class="h-5 w-5"
                                >
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 mb-0">
                                    @{{ report.statistics.total_unpaid_invoices.formatted_total }}
                                </p>
                                <p class="text-sm text-gray-500 mb-0">
                                    @lang('admin::app.dashboard.index.total-unpaid-invoices')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-overall-details', {
            template: '#v-dashboard-overall-details-template',

            data() {
                return {
                    isLoading: true,

                    report: {},
                }
            },

            mounted() {
                this.getReport();
            },

            methods: {
                getReport() {
                    this.$axios.get("{{ route('admin.dashboard.index') }}")
                        .then(response => {
                            this.report = response.data;

                            this.isLoading = false;
                        })
                        .catch(error => {
                            this.isLoading = false;
                        });
                }
            }
        });
    </script>
@endPushOnce
