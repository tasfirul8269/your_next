<!-- Top Selling Products Vue Component -->
<v-dashboard-top-selling-products>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.top-selling-products />
</v-dashboard-top-selling-products>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-top-selling-products-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.top-selling-products />
        </template>

        <!-- Top Selling Products -->
        <template v-else>
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                    <p class="text-base font-semibold text-gray-900 mb-0">
                        Top Selling Products
                    </p>
                    <p class="text-xs text-gray-500 mb-0">
                        @{{ report.date_range }}
                    </p>
                </div>

                <!-- Products List -->
                <div>
                    <a
                        :href="'{{ route('admin.catalog.products.edit', ':id') }}'.replace(':id', item.id)"
                        class="flex items-center gap-4 px-5 py-3 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors"
                        v-for="item in report.statistics"
                    >
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            <img
                                v-if="item.images?.length"
                                class="rounded-lg w-10 h-10 object-cover"
                                :src="item.images[0]?.url"
                            />
                            <div
                                v-else
                                class="rounded-lg w-10 h-10 border-2 border-dashed border-gray-200 flex items-center justify-center"
                            >
                                <span class="text-base text-gray-300">📦</span>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 mb-0 truncate">
                                @{{ item.name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 mb-0">
                                @{{ item.formatted_price }}
                            </p>
                        </div>

                        <!-- Revenue -->
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-gray-900 mb-0">
                                @{{ item.formatted_revenue }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 mb-0">
                                Revenue
                            </p>
                        </div>
                    </a>

                    <!-- Empty State -->
                    <div v-if="!report.statistics || report.statistics.length === 0" class="px-5 py-10 text-center">
                        <div class="text-4xl mb-3">📊</div>
                        <p class="text-sm text-gray-400 mb-1">No sales data yet</p>
                        <p class="text-xs text-gray-300">Top products will appear here once you start selling</p>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-top-selling-products', {
            template: '#v-dashboard-top-selling-products-template',

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
                    filters.type = 'top-selling-products';

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
