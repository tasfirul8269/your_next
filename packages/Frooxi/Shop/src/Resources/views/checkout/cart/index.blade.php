<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.checkout.cart.index.cart')"/>

    <meta name="keywords" content="@lang('shop::app.checkout.cart.index.cart')"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.checkout.cart.index.cart')
    </x-slot>

    {!! view_render_event('frooxi.shop.checkout.cart.header.before') !!}

    <div class="flex-auto">
        {{-- ── Minimal Checkout Header ── --}}
        <div style="border-bottom:1px solid #e5e7eb;background:#fff;">
            <div class="container px-[60px] max-lg:px-8 max-md:px-4" style="display:flex;align-items:center;justify-content:space-between;padding-top:20px;padding-bottom:20px;">
                <a href="{{ route('shop.home.index') }}" style="display:flex;align-items:center;gap:12px;text-decoration:none;">
                    <img src="{{ asset('themes/shop/logo_black.png') }}" alt="{{ config('app.name') }}" style="height:36px;width:auto;">
                </a>

                <div style="display:flex;align-items:center;gap:24px;">
                    {{-- Step breadcrumb --}}
                    <span style="font-family:'Montserrat',sans-serif;font-size:11px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:#9ca3af;">
                        <span style="color:#111;">@lang('shop::app.checkout.cart.index.cart')</span>
                        &nbsp;›&nbsp;
                        @lang('shop::app.checkout.onepage.index.checkout')
                    </span>

                    @guest('customer')
                        @include('shop::checkout.login')
                    @endguest
                </div>
            </div>
        </div>

        <div class="container px-[60px] max-lg:px-8 max-md:px-4">
            <div style="padding:36px 0 12px;">
                <h1 style="font-family:'Montserrat',sans-serif;font-size:28px;font-weight:500;letter-spacing:.04em;text-transform:uppercase;color:#111;margin:0 0 6px;">@lang('shop::app.checkout.cart.index.cart')</h1>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;margin:0;">Review your items and move to checkout when you're ready.</p>
            </div>

            {!! view_render_event('frooxi.shop.checkout.cart.header.after') !!}

            {!! view_render_event('frooxi.shop.checkout.cart.breadcrumbs.before') !!}

            <!-- Breadcrumbs -->
            @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
                <x-shop::breadcrumbs name="cart" />
            @endif

            {!! view_render_event('frooxi.shop.checkout.cart.breadcrumbs.after') !!}

            @php
                $errors = \Frooxi\Checkout\Facades\Cart::getErrors();
            @endphp

            @if (! empty($errors) && $errors['error_code'] === 'MINIMUM_ORDER_AMOUNT')
                <div style="margin-top:16px;background:#fef3cd;border:1px solid #fde68a;border-radius:8px;padding:12px 20px;font-size:13px;color:#78350f;font-family:'Montserrat',sans-serif;">
                    {{ $errors['message'] }}: {{ $errors['amount'] }}
                </div>
            @endif

            <v-cart ref="vCart">
                <!-- Cart Shimmer Effect -->
                <x-shop::shimmer.checkout.cart :count="3" />
            </v-cart>
        </div>
    </div>

    @if (core()->getConfigData('sales.checkout.shopping_cart.cross_sell'))
        {!! view_render_event('frooxi.shop.checkout.cart.cross_sell_carousel.before') !!}

        <!-- Cross-sell Product Carousal -->
        <x-shop::products.carousel
            :title="trans('shop::app.checkout.cart.index.cross-sell.title')"
            :src="route('shop.api.checkout.cart.cross-sell.index')"
        >
        </x-shop::products.carousel>

        {!! view_render_event('frooxi.shop.checkout.cart.cross_sell_carousel.after') !!}
    @endif

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-cart-template"
        >
            <div>
                <!-- Cart Shimmer Effect -->
                <template v-if="isLoading">
                    <x-shop::shimmer.checkout.cart :count="3" />
                </template>

                <!-- Cart Information -->
                <template v-else>
                    <div
                        class="mt-6 flex flex-wrap gap-10 pb-10 max-1060:flex-col max-md:mt-0 max-md:gap-8 max-md:pb-0"
                        v-if="cart?.items?.length"
                    >
                        <div class="flex flex-1 flex-col gap-5 max-md:gap-4">

                            {!! view_render_event('frooxi.shop.checkout.cart.cart_mass_actions.before') !!}

                            {{-- Mass action toolbar --}}
                            <div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px 20px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <input
                                        type="checkbox"
                                        id="select-all"
                                        class="peer hidden"
                                        v-model="allSelected"
                                        @change="selectAll"
                                    >
                                    <label
                                        class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue peer-checked:text-navyBlue"
                                        for="select-all"
                                        tabindex="0"
                                        aria-label="@lang('shop::app.checkout.cart.index.select-all')"
                                    ></label>
                                    <span style="font-family:'Montserrat',sans-serif;font-size:13px;color:#374151;" role="heading" aria-level="2">
                                        @{{ "@lang('shop::app.checkout.cart.index.items-selected')".replace(':count', selectedItemsCount) }}
                                    </span>
                                </div>

                                <div v-if="selectedItemsCount" style="display:flex;align-items:center;gap:16px;">
                                    <span
                                        style="cursor:pointer;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#ef4444;"
                                        role="button"
                                        tabindex="0"
                                        @click="removeSelectedItems"
                                    >
                                        @lang('shop::app.checkout.cart.index.remove')
                                    </span>

                                    @if (auth()->guard()->check())
                                        <span style="width:1px;height:14px;background:#e5e7eb;"></span>
                                        <span
                                            style="cursor:pointer;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#111;"
                                            role="button"
                                            tabindex="0"
                                            @click="moveToWishlistSelectedItems"
                                        >
                                            @lang('shop::app.checkout.cart.index.move-to-wishlist')
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {!! view_render_event('frooxi.shop.checkout.cart.cart_mass_actions.after') !!}
                            {!! view_render_event('frooxi.shop.checkout.cart.item.listing.before') !!}

                            {{-- Cart Item Rows --}}
                            <div v-for="item in cart?.items" :key="item.id">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;gap:16px;">

                                    {{-- Left: checkbox + image + details --}}
                                    <div style="display:flex;align-items:flex-start;gap:16px;flex:1;">

                                        {{-- Checkbox --}}
                                        <div style="padding-top:4px;">
                                            <input
                                                type="checkbox"
                                                :id="'item_' + item.id"
                                                class="peer hidden"
                                                v-model="item.selected"
                                                @change="updateAllSelected"
                                            >
                                            <label
                                                class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue peer-checked:text-navyBlue"
                                                :for="'item_' + item.id"
                                                tabindex="0"
                                                aria-label="@lang('shop::app.checkout.cart.index.select-cart-item')"
                                            ></label>
                                        </div>

                                        {!! view_render_event('frooxi.shop.checkout.cart.item_image.before') !!}

                                        {{-- Product image --}}
                                        <a :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', item.product_url_key)" style="flex-shrink:0;">
                                            <x-shop::media.images.lazy
                                                style="width:96px;height:96px;object-fit:cover;border-radius:8px;"
                                                ::src="item.base_image.small_image_url"
                                                ::alt="item.name"
                                                width="96"
                                                height="96"
                                                ::key="item.id"
                                                ::index="item.id"
                                            />
                                        </a>

                                        {!! view_render_event('frooxi.shop.checkout.cart.item_image.after') !!}

                                        {{-- Item details --}}
                                        <div style="flex:1;min-width:0;">
                                            {!! view_render_event('frooxi.shop.checkout.cart.item_name.before') !!}

                                            <a :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', item.product_url_key)" style="text-decoration:none;">
                                                <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:#111;margin:0 0 6px;letter-spacing:.02em;">
                                                    @{{ item.name }}
                                                </p>
                                            </a>

                                            {!! view_render_event('frooxi.shop.checkout.cart.item_name.after') !!}
                                            {!! view_render_event('frooxi.shop.checkout.cart.item_details.before') !!}

                                            {{-- Options --}}
                                            <div v-if="item.options.length" style="margin-bottom:10px;">
                                                <p
                                                    style="display:flex;align-items:center;gap:6px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#6b7280;"
                                                    @click="item.option_show = ! item.option_show"
                                                >
                                                    @lang('shop::app.checkout.cart.index.see-details')
                                                    <span class="text-lg" :class="{'icon-arrow-up': item.option_show, 'icon-arrow-down': ! item.option_show}"></span>
                                                </p>

                                                <div v-show="item.option_show" style="margin-top:6px;">
                                                    <template v-for="attribute in item.options">
                                                        <div style="display:flex;gap:8px;margin-bottom:3px;">
                                                            <p style="font-size:12px;color:#9ca3af;font-family:'Montserrat',sans-serif;">@{{ attribute.attribute_name }}:</p>
                                                            <p style="font-size:12px;color:#374151;font-family:'Montserrat',sans-serif;">
                                                                <template v-if="attribute?.attribute_type === 'file'">
                                                                    <a :href="attribute.file_url" style="color:#111;font-weight:600;" target="_blank" :download="attribute.file_name">@{{ attribute.file_name }}</a>
                                                                </template>
                                                                <template v-else>@{{ attribute.option_label }}</template>
                                                            </p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            {!! view_render_event('frooxi.shop.checkout.cart.item_details.after') !!}
                                            {!! view_render_event('frooxi.shop.checkout.cart.quantity_changer.before') !!}

                                            {{-- Qty changer + remove --}}
                                            <div style="display:flex;align-items:center;gap:16px;margin-top:12px;">
                                                <x-shop::quantity-changer
                                                    v-if="item.can_change_qty"
                                                    class="flex max-w-max items-center gap-x-2.5 rounded-[5px] border border-zinc-300 px-3.5 py-1.5"
                                                    name="quantity"
                                                    ::value="item?.quantity"
                                                    @change="setItemQuantity(item.id, $event)"
                                                />

                                                <span
                                                    style="cursor:pointer;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:#ef4444;"
                                                    role="button"
                                                    tabindex="0"
                                                    @click="removeItem(item.id)"
                                                >
                                                    @lang('shop::app.checkout.cart.index.remove')
                                                </span>
                                            </div>

                                            {!! view_render_event('frooxi.shop.checkout.cart.quantity_changer.after') !!}
                                        </div>
                                    </div>

                                    {{-- Right: price --}}
                                    <div style="text-align:right;flex-shrink:0;">
                                        {!! view_render_event('frooxi.shop.checkout.cart.total.before') !!}

                                        <p style="font-family:'Montserrat',sans-serif;font-size:16px;font-weight:700;color:#111;">@{{ item.formatted_total }}</p>

                                        {!! view_render_event('frooxi.shop.checkout.cart.total.after') !!}
                                        {!! view_render_event('frooxi.shop.checkout.cart.remove_button.before') !!}
                                        {!! view_render_event('frooxi.shop.checkout.cart.remove_button.after') !!}
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('frooxi.shop.checkout.cart.item.listing.after') !!}
                            {!! view_render_event('frooxi.shop.checkout.cart.controls.before') !!}

                            {{-- Action bar --}}
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                                {!! view_render_event('frooxi.shop.checkout.cart.continue_shopping.before') !!}

                                <a
                                    href="{{ route('shop.home.index') }}"
                                    style="font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:#6b7280;text-decoration:none;display:flex;align-items:center;gap:6px;"
                                >
                                    <span class="icon-arrow-left text-lg"></span>
                                    @lang('shop::app.checkout.cart.index.continue-shopping')
                                </a>

                                {!! view_render_event('frooxi.shop.checkout.cart.continue_shopping.after') !!}
                                {!! view_render_event('frooxi.shop.checkout.cart.update_cart.before') !!}

                                <x-shop::button
                                    style="height:44px;background:#e30612;color:#fff;border:none;border-radius:8px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;padding:0 28px;cursor:pointer;"
                                    :title="trans('shop::app.checkout.cart.index.update-cart')"
                                    ::loading="isStoring"
                                    ::disabled="isStoring"
                                    @click="update()"
                                />

                                {!! view_render_event('frooxi.shop.checkout.cart.update_cart.after') !!}
                            </div>

                            {!! view_render_event('frooxi.shop.checkout.cart.controls.after') !!}
                        </div>

                        {!! view_render_event('frooxi.shop.checkout.cart.summary.before') !!}

                        <!-- Cart Summary Blade File -->
                        @include('shop::checkout.cart.summary')

                        {!! view_render_event('frooxi.shop.checkout.cart.summary.after') !!}
                    </div>

                    {{-- Empty Cart --}}
                    <div
                        style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 24px;text-align:center;"
                        v-else
                    >
                        <img
                            src="{{ frooxi_asset('images/thank-you.png') }}"
                            alt="@lang('shop::app.checkout.cart.index.empty-product')"
                            style="height:120px;width:auto;margin-bottom:24px;opacity:.7;"
                            loading="lazy"
                            decoding="async"
                        />
                        <p style="font-family:'Montserrat',sans-serif;font-size:18px;font-weight:500;color:#111;letter-spacing:.04em;text-transform:uppercase;margin:0 0 8px;" role="heading">
                            @lang('shop::app.checkout.cart.index.empty-product')
                        </p>
                        <a href="{{ route('shop.home.index') }}" style="margin-top:20px;display:inline-block;height:44px;line-height:44px;padding:0 32px;background:#e30612;color:#fff;border-radius:8px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;text-decoration:none;">
                            Continue Shopping
                        </a>
                    </div>
                </template>
            </div>
        </script>

        <script type="module">
            app.component("v-cart", {
                template: '#v-cart-template',

                data() {
                    return  {
                        cart: [],

                        allSelected: false,

                        applied: {
                            quantity: {},
                        },

                        isLoading: true,

                        isStoring: false,
                    }
                },

                mounted() {
                    this.getCart();
                },

                computed: {
                    selectedItemsCount() {
                        return this.cart.items.filter(item => item.selected).length;
                    },
                },

                methods: {
                    getCart() {
                        this.$axios.get('{{ route('shop.api.checkout.cart.index') }}')
                            .then(response => {
                                this.cart = response.data.data;

                                this.isLoading = false;

                                if (response.data.message) {
                                    this.$emitter.emit('add-flash', { type: 'info', message: response.data.message });
                                }
                            })
                            .catch(error => {});
                    },

                    setCart(cart) {
                        this.cart = cart;
                    },

                    selectAll() {
                        for (let item of this.cart.items) {
                            item.selected = this.allSelected;
                        }
                    },

                    updateAllSelected() {
                        this.allSelected = this.cart.items.every(item => item.selected);
                    },

                    update() {
                        this.isStoring = true;

                        this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty: this.applied.quantity })
                            .then(response => {
                                if (response.data.message) {
                                    this.cart = response.data.data;

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isStoring = false;

                            })
                            .catch(error => {
                                this.isStoring = false;
                            });
                    },

                    setItemQuantity(itemId, quantity) {
                        this.applied.quantity[itemId] = quantity;
                    },

                    removeItem(itemId) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.$axios.post('{{ route('shop.api.checkout.cart.destroy') }}', {
                                        '_method': 'DELETE',
                                        'cart_item_id': itemId,
                                    })
                                    .then(response => {
                                        this.cart = response.data.data;

                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    })
                                    .catch(error => {});
                            }
                        });
                    },

                    removeSelectedItems() {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                const selectedItemsIds = this.cart.items.flatMap(item => item.selected ? item.id : []);

                                this.$axios.post('{{ route('shop.api.checkout.cart.destroy_selected') }}', {
                                        '_method': 'DELETE',
                                        'ids': selectedItemsIds,
                                    })
                                    .then(response => {
                                        this.cart = response.data.data;

                                        this.$emitter.emit('update-mini-cart', response.data.data );

                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    })
                                    .catch(error => {});
                            }
                        });
                    },

                    moveToWishlistSelectedItems() {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                const selectedItemsIds = this.cart.items.flatMap(item => item.selected ? item.id : []);

                                const selectedItemsQty = this.cart.items.filter(item => item.selected).map(item => this.applied.quantity[item.id] ?? item.quantity);

                                this.$axios.post('{{ route('shop.api.checkout.cart.move_to_wishlist') }}', {
                                        'ids': selectedItemsIds,
                                        'qty': selectedItemsQty
                                    })
                                    .then(response => {
                                        this.cart = response.data.data;

                                        this.$emitter.emit('update-mini-cart', response.data.data );

                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    })
                                    .catch(error => {});
                            }
                        });
                    },
                }
            });
        </script>
    @endpushOnce
</x-shop::layouts>
