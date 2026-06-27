<!-- Todays Details Vue Component -->
<v-dashboard-todays-details>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.todays-details />
</v-dashboard-todays-details>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-todays-details-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.todays-details />
        </template>

        <!-- Today's Sales Section -->
        <template v-else>
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                    <p class="text-base font-semibold text-gray-900 mb-0">
                        Today's Performance
                    </p>
                    <div class="flex gap-6">
                        <!-- Today's Sales -->
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 mb-0">
                                @{{ report.statistics.total_sales.formatted_total }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 mb-0">
                                Sales
                            </p>
                        </div>
                        <!-- Today's Orders -->
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 mb-0">
                                @{{ report.statistics.total_orders.current }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 mb-0">
                                Orders
                            </p>
                        </div>
                        <!-- Today's Customers -->
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 mb-0">
                                @{{ report.statistics.total_customers.current }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 mb-0">
                                Customers
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders List -->
                <div>
                    <div 
                        v-for="order in report.statistics.orders"
                        class="px-5 py-3 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors"
                    >
                        <div class="flex items-start justify-between">
                            <!-- Order Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <h4 class="text-sm font-medium text-gray-900 mb-0">
                                        Order #@{{ order.increment_id }}
                                    </h4>
                                    <span 
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="{
                                            'bg-[#D4A84B]/10 text-[#D4A84B]': order.status === 'completed',
                                            'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                            'bg-blue-100 text-blue-800': order.status === 'processing',
                                            'bg-red-100 text-red-800': order.status === 'canceled'
                                        }"
                                    >
                                        @{{ order.status_label }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>@{{ order.created_at }}</span>
                                    <span>@{{ order.customer_name }}</span>
                                    <span>@{{ order.payment_method }}</span>
                                </div>
                            </div>

                            <!-- Order Total -->
                            <div class="text-right">
                                <p class="text-base font-bold text-gray-900 mb-0">
                                    @{{ order.formatted_base_grand_total }}
                                </p>
                                <a 
                                    :href="'{{ route('admin.sales.orders.view', ':replace') }}'.replace(':replace', order.id)"
                                    class="text-xs text-gray-400 hover:text-[#D4A84B] transition-colors"
                                >
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!report.statistics.orders || report.statistics.orders.length === 0" class="px-5 py-10 text-center">
                        <p class="text-sm text-gray-400">No orders today</p>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-todays-details', {
            template: '#v-dashboard-todays-details-template',

            data() {
                return {
                    report: [],
                    isLoading: true,
                }
            },

            mounted() {
                this.getStats({});
                this.$emitter.on('reporting-filter-updated', this.getStats);
            },

            methods: {
                getStats(filters) {
                    this.isLoading = true;
                    var filters = Object.assign({}, filters);
                    filters.type = 'today';

                    this.$axios.get("{{ route('admin.dashboard.stats') }}", { params: filters })
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
