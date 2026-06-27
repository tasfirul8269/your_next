<x-shop::layouts
    :has-header="true"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Flash Sale
    </x-slot>

    @php
        $discounts = $flashSaleProducts
            ->pluck('flash_sale_discount')
            ->unique()
            ->filter(fn($d) => $d > 0)
            ->sortDesc()
            ->values();
    @endphp

    <v-flash-sale-page></v-flash-sale-page>

    @pushOnce('scripts')
        <style>
            .desktop-cta { display: flex !important; }
            .mobile-cta { display: none !important; }
            @media (max-width: 768px) {
                .desktop-cta { display: none !important; }
                .mobile-cta { display: flex !important; }
            }
        </style>
        <script type="text/x-template" id="v-flash-sale-page-template">
            <div style="min-height: 100vh; background: #fff; padding: 40px 16px 100px;">
                <!-- Page Header -->
                <div style="text-align: center; margin-bottom: 40px;">
                    <h1 style="margin: 0 0 24px 0; font-family: Montserrat, sans-serif; font-size: clamp(26px, 4vw, 42px); font-weight: 500; line-height: 1.05; letter-spacing: .08em; text-transform: uppercase; color: #111;">
                        FLASH SALE
                    </h1>

                    <!-- Filter Tags -->
                    <div v-if="discounts.length > 0" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-bottom: 40px;">
                        <button
                            v-for="discount in discounts"
                            :key="discount"
                            v-if="currentFilter !== discount"
                            @click="currentFilter = discount"
                            style="padding: 8px 24px; background: #D63044; color: white; border: none; border-radius: 0; font-size: 14px; font-weight: 600; cursor: pointer;"
                        >
                            @{{ discount }}% Off
                        </button>
                    </div>


                </div>

                <!-- Products Grid -->
                <div
                    v-if="filteredProducts.length > 0"
                    class="container mt-5 max-lg:px-8 max-sm:!px-4"
                >
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 max-md:gap-7 max-sm:gap-4">
                        <div 
                            v-for="product in filteredProducts" 
                            :key="product.id"
                            class="cursor-pointer"
                            style="font-family: Montserrat, sans-serif;"
                            @click="goToProduct('/' + product.url_key)"
                            @mouseenter="hoveredProduct = product.id"
                            @mouseleave="hoveredProduct = null"
                        >
                            <!-- Image Container -->
                            <div style="position: relative; border-radius: 8px; overflow: hidden; background: #f9f9f9;">
                                <!-- Discount Badge -->
                                <div style="position: absolute; top: 10px; left: 10px; z-index: 4; background: #ef4444; color: #fff; font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 9999px;">
                                    @{{ discountsMap[product.id] }}% OFF
                                </div>

                                <!-- Images -->
                                <div style="position: relative; width: 100%; aspect-ratio: 2/3; overflow: hidden;">
                                    <img 
                                        :src="product.base_image.medium_image_url" 
                                        loading="lazy" 
                                        style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: opacity 1s ease, transform 1s ease;"
                                        :style="hoveredProduct === product.id ? 'opacity: 0; transform: scale(1.05);' : 'opacity: 1; transform: scale(1);'"
                                    >
                                    <img 
                                        :src="(product.images && product.images[1] && product.images[1].medium_image_url) ? product.images[1].medium_image_url : product.base_image.medium_image_url" 
                                        loading="lazy" 
                                        style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: opacity 1s ease, transform 1s ease;"
                                        :style="hoveredProduct === product.id ? 'opacity: 1; transform: scale(1.05);' : 'opacity: 0; transform: scale(1);'"
                                    >
                                </div>

                                <!-- Desktop CTA -->
                                <div style="position: absolute; bottom: 12px; left: 0; right: 0; display: flex; justify-content: center; pointer-events: auto;" class="desktop-cta">
                                    <button 
                                        style="display: inline-flex; align-items: center; justify-content: center; height: 44px; padding: 0 28px; background: #111; color: #fff; border: none; outline: none; border-radius: 5px; cursor: pointer; transform: translateY(0); transition: transform .3s ease; overflow: hidden; position: relative; min-width: 140px;"
                                        :style="hoveredProduct === product.id ? 'transform: translateY(-6px);' : ''"
                                        @click.stop="(product.is_saleable && product.type !== 'configurable') ?addToCart(product.id) : goToProduct('/' + product.url_key)"
                                        @mouseenter="hoveredButton = product.id"
                                        @mouseleave="hoveredButton = null"
                                    >
                                        <span 
                                            style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; font-family: Montserrat, sans-serif; letter-spacing: .2px; transition: transform .28s ease, opacity .28s ease; z-index: 1;"
                                            :style="hoveredButton === product.id ? 'transform: translateY(-100%); opacity: 0;' : 'transform: translateY(0); opacity: 1;'"
                                        >@{{ (product.is_saleable && product.type !== 'configurable') ?'Add to cart' : 'View product' }}</span>
                                        <span 
                                            style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: #111; transition: transform .28s ease, opacity .28s ease; z-index: 2;"
                                            :style="hoveredButton === product.id ? 'transform: translateY(0); opacity: 1;' : 'transform: translateY(100%); opacity: 1;'"
                                        >
                                            <svg v-if="product.is_saleable && product.type !== 'configurable'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </span>
                                    </button>
                                </div>

                                <!-- Mobile CTA -->
                                <button 
                                    style="position: absolute; bottom: 10px; right: 10px; z-index: 4; width: 44px; height: 44px; background: #111; color: #fff; border: none; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; pointer-events: auto;"
                                    class="mobile-cta"
                                    @click.stop="(product.is_saleable && product.type !== 'configurable') ?addToCart(product.id) : goToProduct('/' + product.url_key)"
                                >
                                    <svg v-if="product.is_saleable && product.type !== 'configurable'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                    <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>

                            <!-- Info -->
                            <div style="padding: 10px 2px 0;">
                                <p style="font-size: 13px; font-weight: 400; color: #111827; margin: 0 0 4px; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">@{{ product.name }}</p>
                                <p style="font-size: 13px; color: #6b7280; margin: 0;">@{{ product.min_price }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else style="text-align: center; padding: 48px; color: #6b7280; font-size: 18px;">
                    No products found for this filter.
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-flash-sale-page', {
                template: '#v-flash-sale-page-template',

                data() {
                    return {
                        products: [],
                        discounts: @json($discounts),
                        discountsMap: @json($flashSaleProducts->pluck('flash_sale_discount', 'id')),
                        currentFilter: null,
                        sortBy: 'featured',
                        isLoading: true,
                        hoveredProduct: null,
                        hoveredButton: null,
                    };
                },

                computed: {
                    filteredProducts() {
                        if (!this.currentFilter) {
                            return this.products;
                        }
                        return this.products.filter(p => this.discountsMap[p.id] == this.currentFilter);
                    }
                },

                mounted() {
                    this.getProducts();
                },

                methods: {
                    getProducts() {
                        this.$axios.get("{{ route('shop.api.products.index') }}", {
                            params: {
                                is_flash_sale_page: 1,
                                status: 1,
                                limit: 100
                            }
                        })
                        .then(response => {
                            this.isLoading = false;
                            this.products = response.data.data;
                        })
                        .catch(error => {
                            this.isLoading = false;
                            console.error('Failed to load flash sale products:', error);
                        });
                    },

                    sortProducts() {
                        if (this.sortBy === 'price_low') {
                            this.products.sort((a, b) => parseFloat(a.min_price.replace(/[^0-9.]/g, '')) - parseFloat(b.min_price.replace(/[^0-9.]/g, '')));
                        } else if (this.sortBy === 'price_high') {
                            this.products.sort((a, b) => parseFloat(b.min_price.replace(/[^0-9.]/g, '')) - parseFloat(a.min_price.replace(/[^0-9.]/g, '')));
                        } else if (this.sortBy === 'discount') {
                            this.products.sort((a, b) => (this.discountsMap[b.id] || 0) - (this.discountsMap[a.id] || 0));
                        }
                    },

                    addToCart(productId) {
                        this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': productId,
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data);
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                    },
                    goToProduct(url) {
                        window.location.href = url;
                    }
                }
            });
        </script>
    @endPushOnce
</x-shop::layouts>
