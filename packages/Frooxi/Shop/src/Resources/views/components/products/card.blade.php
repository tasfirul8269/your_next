<v-product-card
    {{ $attributes }}
    :product="product"
>
</v-product-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-card-template"
    >
        <!-- Grid Card -->
        <div
            class="group relative flex h-full w-full flex-col"
            v-if="mode != 'list'"
        >
            @php
                $productBaseUrl = url('/');
            @endphp

            <div class="relative overflow-hidden rounded-[12px] bg-zinc-100">
                {!! view_render_event('frooxi.shop.components.products.card.image.before') !!}

                <!-- Product Image -->
                <a
                    :href="`{!! $productBaseUrl !!}/${product.url_key}`"
                    :aria-label="product.name + ' '"
                    class="block"
                >
                    <x-shop::media.images.lazy
                        class="after:content-[' '] relative block overflow-hidden rounded-[12px] bg-zinc-100 transition duration-700 ease-out after:block after:pb-[calc(100%+9px)] group-hover:scale-[1.05]"
                        ::src="product.base_image.medium_image_url"
                        ::srcset="`
                            ${product.base_image.small_image_url} 150w,
                            ${product.base_image.medium_image_url} 300w,
                        `"
                        sizes="(max-width: 768px) 150px, (max-width: 1200px) 300px, 600px"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="300"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('frooxi.shop.components.products.card.image.after') !!}

                <!-- Product Ratings -->
                {!! view_render_event('frooxi.shop.components.products.card.average_ratings.before') !!}

                @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                    <x-shop::products.ratings
                        class="absolute bottom-3 z-10 items-center rounded-full !border-white/80 bg-white/90 !px-2.5 !py-1 text-xs shadow-sm backdrop-blur ltr:left-3 rtl:right-3"
                        ::average="product.ratings.average"
                        ::total="product.ratings.total"
                        ::rating="false"
                        v-if="product.ratings.total"
                    />
                @else
                    <x-shop::products.ratings
                        class="absolute bottom-3 z-10 items-center rounded-full !border-white/80 bg-white/90 !px-2.5 !py-1 text-xs shadow-sm backdrop-blur ltr:left-3 rtl:right-3"
                        ::average="product.ratings.average"
                        ::total="product.reviews.total"
                        ::rating="false"
                        v-if="product.reviews.total"
                    />
                @endif

                {!! view_render_event('frooxi.shop.components.products.card.average_ratings.after') !!}

                <div class="pointer-events-none absolute inset-0">
                    <!-- Badges -->
                    <div class="absolute top-3 z-10 flex flex-col gap-2 ltr:left-3 rtl:right-3">
                        <!-- Product Sale Badge -->
                        <p
                            class="inline-flex rounded-full bg-red-500 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.08em] text-white shadow-sm"
                            v-if="product.on_sale || product.discount_percentage || product.flash_sale_discount"
                        >
                            <span v-if="product.flash_sale_discount">@{{ product.flash_sale_discount }}% OFF</span>
                            <span v-else-if="product.discount_percentage">@{{ parseFloat(product.discount_percentage) }}% OFF</span>
                            <span v-else>@lang('shop::app.components.products.card.sale')</span>
                        </p>
                    </div>

                    <div class="pointer-events-none absolute top-3 z-10 flex flex-col gap-2 ltr:right-3 ltr:items-end rtl:left-3 rtl:items-start">
                        <!-- Product New Badge -->
                        <p
                            class="pointer-events-auto inline-flex rounded-full bg-navyBlue px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.08em] text-white shadow-sm"
                            v-if="product.is_new"
                        >
                            @lang('shop::app.components.products.card.new')
                        </p>

                        <div class="pointer-events-auto flex flex-col gap-2 opacity-100 transition-all duration-300 md:translate-x-2 md:opacity-0 md:group-hover:translate-x-0 md:group-hover:opacity-100">
                            {!! view_render_event('frooxi.shop.components.products.card.wishlist_option.before') !!}

                            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                            <span
                                class="flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200/80 bg-white/95 text-lg text-zinc-700 shadow-sm backdrop-blur"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                                tabindex="0"
                                :class="product.is_wishlist ? 'icon-heart-fill !text-red-500' : 'icon-heart'"
                                @click="addToWishlist()"
                            >
                            </span>
                        @endif

                        {!! view_render_event('frooxi.shop.components.products.card.wishlist_option.after') !!}
                    </div>

                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        {!! view_render_event('frooxi.shop.components.products.card.add_to_cart.before') !!}

                        <!-- Add to Cart Button (configurable products must pick a size on the product page) -->
                        <template v-if="product.is_saleable && product.type !== 'configurable'">
                            <button
                                class="pointer-events-auto absolute bottom-3 z-10 flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-950 text-white shadow-lg transition hover:bg-black md:hidden ltr:right-3 rtl:left-3"
                                :aria-label="'@lang('shop::app.components.products.card.add-to-cart')'"
                                :disabled="isAddingToCart"
                                @click="addToCart()"
                            >
                                <span
                                    class="icon-cart text-xl"
                                    aria-hidden="true"
                                >
                                </span>
                            </button>

                            <button
                                class="pointer-events-auto absolute bottom-3 z-10 hidden w-[calc(100%-24px)] translate-y-3 items-center justify-center rounded-[10px] bg-zinc-950 px-4 py-3 text-sm font-medium text-white opacity-0 shadow-lg transition-all duration-300 ease-out hover:bg-black group-hover:translate-y-0 group-hover:opacity-100 md:flex ltr:left-3 rtl:right-3"
                                :disabled="isAddingToCart"
                                @click="addToCart()"
                            >
                                @lang('shop::app.components.products.card.add-to-cart')
                            </button>
                        </template>

                        <template v-else>
                            <a
                                :href="`{!! $productBaseUrl !!}/${product.url_key}`"
                                @click.prevent="navigateToProduct()"
                                class="pointer-events-auto absolute bottom-3 z-10 flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-950 text-white shadow-lg transition hover:bg-black md:hidden ltr:right-3 rtl:left-3"
                                :aria-label="product.name"
                            >
                                <span
                                    class="icon-eye text-xl"
                                    aria-hidden="true"
                                >
                                </span>
                            </a>

                            <a
                                :href="`{!! $productBaseUrl !!}/${product.url_key}`"
                                @click.prevent="navigateToProduct()"
                                class="pointer-events-auto absolute bottom-3 z-10 hidden w-[calc(100%-24px)] translate-y-3 items-center justify-center rounded-[10px] bg-zinc-950 px-4 py-3 text-sm font-medium text-white opacity-0 shadow-lg transition-all duration-300 ease-out hover:bg-black group-hover:translate-y-0 group-hover:opacity-100 md:flex ltr:left-3 rtl:right-3"
                            >
                                View Product
                            </a>
                        </template>

                        {!! view_render_event('frooxi.shop.components.products.card.add_to_cart.after') !!}
                    @endif
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="flex flex-1 flex-col gap-1.5 px-1 pt-3">
                {!! view_render_event('frooxi.shop.components.products.card.name.before') !!}

                <a
                    :href="`{!! $productBaseUrl !!}/${product.url_key}`"
                    class="line-clamp-2 text-sm font-normal leading-5 text-zinc-900 transition-colors hover:text-zinc-700 md:text-[15px]"
                >
                    @{{ product.name }}
                </a>

                {!! view_render_event('frooxi.shop.components.products.card.name.after') !!}

                <!-- Pricing -->
                {!! view_render_event('frooxi.shop.components.products.card.price.before') !!}

                <div
                    class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[15px] font-semibold leading-none text-zinc-900"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('frooxi.shop.components.products.card.price.after') !!}
            </div>
        </div>

        <!-- List Card -->
        <div
            class="relative flex max-w-max grid-cols-2 gap-4 overflow-hidden rounded max-sm:flex-wrap"
            v-else
        >
            <div class="group relative max-h-[258px] max-w-[250px] overflow-hidden">

                {!! view_render_event('frooxi.shop.components.products.card.image.before') !!}

                <a :href="`{!! $productBaseUrl !!}/${product.url_key}`">
                    <x-shop::media.images.lazy
                        class="after:content-[' '] relative min-w-[250px] bg-zinc-100 transition-all duration-300 after:block after:pb-[calc(100%+9px)] group-hover:scale-105"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="300"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('frooxi.shop.components.products.card.image.after') !!}

                <div class="action-items bg-black">
                    <div class="absolute top-5 flex flex-col items-start gap-2 ltr:left-5 max-sm:ltr:left-2 rtl:right-5">
                        <p
                            class="inline-block rounded-[44px] bg-red-500 px-2.5 text-sm text-white"
                            v-if="product.on_sale || product.discount_percentage || product.flash_sale_discount"
                        >
                            <span v-if="product.flash_sale_discount">@{{ product.flash_sale_discount }}% OFF</span>
                            <span v-else-if="product.discount_percentage">@{{ parseFloat(product.discount_percentage) }}% OFF</span>
                            <span v-else>@lang('shop::app.components.products.card.sale')</span>
                        </p>
                    </div>

                    <div class="pointer-events-none absolute top-5 flex flex-col gap-2 ltr:right-5 ltr:items-end rtl:left-5 rtl:items-start">
                        <p
                            class="pointer-events-auto inline-block rounded-[44px] bg-navyBlue px-2.5 text-sm text-white"
                            v-if="product.is_new"
                        >
                            @lang('shop::app.components.products.card.new')
                        </p>

                        <div class="pointer-events-auto opacity-0 transition-all duration-300 group-hover:bottom-0 group-hover:opacity-100 max-sm:opacity-100">
                            {!! view_render_event('frooxi.shop.components.products.card.wishlist_option.before') !!}

                            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                <span
                                    class="flex h-[30px] w-[30px] cursor-pointer items-center justify-center rounded-md bg-white text-2xl"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                                tabindex="0"
                                :class="product.is_wishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                                @click="addToWishlist()"
                            >
                            </span>
                        @endif

                        {!! view_render_event('frooxi.shop.components.products.card.wishlist_option.after') !!}
                    </div>
                </div>
            </div>

            <div class="grid content-start gap-4">

                {!! view_render_event('frooxi.shop.components.products.card.name.before') !!}

                <p class="text-base">
                    @{{ product.name }}
                </p>

                {!! view_render_event('frooxi.shop.components.products.card.name.after') !!}

                {!! view_render_event('frooxi.shop.components.products.card.price.before') !!}

                <div
                    class="flex gap-2.5 text-lg font-semibold"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('frooxi.shop.components.products.card.price.after') !!}

                <!-- Needs to implement that in future -->
                <div class="flex hidden gap-4">
                    <span class="block h-[30px] w-[30px] rounded-full bg-[#B5DCB4]">
                    </span>

                    <span class="block h-[30px] w-[30px] rounded-full bg-zinc-500">
                    </span>
                </div>

                {!! view_render_event('frooxi.shop.components.products.card.average_ratings.before') !!}

                <p class="text-sm text-zinc-500">
                    <template  v-if="! product.ratings.total">
                        <p class="text-sm text-zinc-500">
                            @lang('shop::app.components.products.card.review-description')
                        </p>
                    </template>

                    <template v-else>
                        @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.ratings.total"
                                ::rating="false"
                            />
                        @else
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.reviews.total"
                                ::rating="false"
                            />
                        @endif
                    </template>
                </p>

                {!! view_render_event('frooxi.shop.components.products.card.average_ratings.after') !!}

                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))

                    {!! view_render_event('frooxi.shop.components.products.card.add_to_cart.before') !!}

                    <template v-if="product.type === 'configurable'">
                        <a
                            :href="`{!! $productBaseUrl !!}/${product.url_key}`"
                            @click.prevent="navigateToProduct()"
                            class="primary-button flex items-center justify-center gap-2 whitespace-nowrap px-8 py-2.5"
                        >
                            <span class="icon-eye text-lg" aria-hidden="true"></span>
                            View Product
                        </a>
                    </template>

                    <template v-else>
                        <x-shop::button
                            class="primary-button whitespace-nowrap px-8 py-2.5"
                            :title="trans('shop::app.components.products.card.add-to-cart')"
                            ::loading="isAddingToCart"
                            ::disabled="! product.is_saleable || isAddingToCart"
                            @click="addToCart()"
                        />
                    </template>

                    {!! view_render_event('frooxi.shop.components.products.card.add_to_cart.after') !!}

                @endif
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product-card', {
            template: '#v-product-card-template',

            props: ['mode', 'product'],

            data() {
                return {
                    isCustomer: '{{ auth()->guard('customer')->check() }}',

                    isAddingToCart: false,
                }
            },

            methods: {
                navigateToProduct() {
                    if (this.product && this.product.url_key) {
                        window.location.href = '/' + this.product.url_key;
                    }
                },

                addToWishlist() {
                    if (this.isCustomer) {
                        this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                                product_id: this.product.id
                            })
                            .then(response => {
                                this.product.is_wishlist = ! this.product.is_wishlist;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                },

                addToCompare(productId) {
                    /**
                     * This will handle for customers.
                     */
                    if (this.isCustomer) {
                        this.$axios.post('/api/compare', {
                                'product_id': productId
                            })
                            .then(response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {
                                if ([400, 422].includes(error.response.status)) {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });

                                    return;
                                }

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                            });

                        return;
                    }

                    /**
                     * This will handle for guests.
                     */
                    let items = this.getStorageValue() ?? [];

                    if (items.length) {
                        if (! items.includes(productId)) {
                            items.push(productId);

                            localStorage.setItem('compare_items', JSON.stringify(items));

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                        } else {
                            this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.components.products.card.already-in-compare')" });
                        }
                    } else {
                        localStorage.setItem('compare_items', JSON.stringify([productId]));

                        this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });

                    }
                },

                getStorageValue(key) {
                    let value = localStorage.getItem('compare_items');

                    if (! value) {
                        return [];
                    }

                    return JSON.parse(value);
                },

                addToCart() {
                    // Configurable products require selecting a size/variant on the product page.
                    if (this.product && this.product.type === 'configurable') {
                        this.navigateToProduct();
                        return;
                    }

                    this.isAddingToCart = true;

                    this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data );

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }

                            this.isAddingToCart = false;
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            if (error.response.data.redirect_uri) {
                                window.location.href = error.response.data.redirect_uri;
                            }

                            this.isAddingToCart = false;
                        });
                },
            },
        });
    </script>
@endpushOnce
