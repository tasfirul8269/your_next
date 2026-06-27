<!-- Stock Threshold Products Vue Component -->
<v-dashboard-stock-threshold-products>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.stock-threshold-products />
</v-dashboard-stock-threshold-products>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-stock-threshold-products-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.stock-threshold-products />
        </template>

        <!-- Stock Threshold Products -->
        <template v-else>
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                    <div>
                        <p class="text-base font-semibold text-gray-900 mb-0">
                            Low Stock Alert
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5 mb-0">
                            Products that need restocking
                        </p>
                    </div>
                    <span 
                        v-if="report.statistics.length"
                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-red-100 text-red-700"
                    >
                        @{{ report.statistics.length }} items
                    </span>
                </div>

                <!-- Products List -->
                <div>
                    <div
                        v-for="product in report.statistics"
                        class="flex items-center gap-4 px-5 py-3 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors"
                    >
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            <img
                                v-if="product.image"
                                class="rounded-lg w-10 h-10 object-cover"
                                :src="product.image"
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
                                @{{ product.name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 mb-0">
                                SKU: @{{ product.sku }}
                            </p>
                        </div>

                        <!-- Stock Status -->
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold mb-0"
                               :class="product.total_qty > {{ core()->getConfigData('catalog.inventory.stock_options.out_of_stock_threshold') }} ? 'text-gray-900' : 'text-red-600'">
                                @{{ product.total_qty }} units
                            </p>
                            <a 
                                :href="'{{ route('admin.catalog.products.edit', ':replace') }}'.replace(':replace', product.id)"
                                class="text-xs text-gray-400 hover:text-[#D4A84B] transition-colors"
                            >
                                Manage Stock →
                            </a>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!report.statistics || report.statistics.length === 0" class="px-5 py-10 text-center">
                        <div class="text-4xl mb-3">✅</div>
                        <p class="text-sm text-gray-400 mb-1">All stocked up!</p>
                        <p class="text-xs text-gray-300">No products below stock threshold</p>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-stock-threshold-products', {
            template: '#v-dashboard-stock-threshold-products-template',

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
                    filters.type = 'stock-threshold-products';

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
