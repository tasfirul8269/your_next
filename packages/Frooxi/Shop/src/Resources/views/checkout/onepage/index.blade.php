<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.checkout.onepage.index.checkout')"/>

    <meta name="keywords" content="@lang('shop::app.checkout.onepage.index.checkout')"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.checkout.onepage.index.checkout')
    </x-slot>

    {!! view_render_event('frooxi.shop.checkout.onepage.header.before') !!}

    {{-- Minimal Checkout Header with step tracker --}}
    <div style="border-bottom:1px solid #e5e7eb;background:#fff;">
        <div class="container px-[60px] max-lg:px-8 max-sm:px-4" style="display:flex;align-items:center;justify-content:space-between;padding-top:20px;padding-bottom:20px;">
            <a href="{{ route('shop.home.index') }}" style="display:flex;align-items:center;gap:12px;text-decoration:none;">
                <img src="{{ asset('themes/shop/logo_black.png') }}" alt="{{ config('app.name') }}" style="height:36px;width:auto;">
            </a>

            {{-- Step tracker (desktop) --}}
            <div style="display:flex;align-items:center;gap:0;" class="max-md:hidden">
                <template v-for="(step, idx) in [{key:'address',label:'Address'},{key:'shipping',label:'Shipping'},{key:'payment',label:'Payment'},{key:'review',label:'Review'}]" :key="step.key">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div :style="'width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:Montserrat,sans-serif;font-size:11px;font-weight:700;transition:all .2s;' + (currentStep === step.key ? 'background:#e30612;color:#fff;' : ['address','shipping','payment','review'].indexOf(currentStep) > idx ? 'background:#a38c5a;color:#fff;' : 'background:#f3f4f6;color:#9ca3af;')">
                            <span v-if="['address','shipping','payment','review'].indexOf(currentStep) > idx">&#10003;</span>
                            <span v-else>@{{ idx + 1 }}</span>
                        </div>
                        <span :style="'font-family:Montserrat,sans-serif;font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;transition:color .2s;' + (currentStep === step.key ? 'color:#111;' : ['address','shipping','payment','review'].indexOf(currentStep) > idx ? 'color:#a38c5a;' : 'color:#d1d5db;')">
                            @{{ step.label }}
                        </span>
                    </div>
                    <div v-if="idx < 3" style="width:40px;height:1px;background:#e5e7eb;margin:0 8px;"></div>
                </template>
            </div>

            @guest('customer')
                @include('shop::checkout.login')
            @endguest
        </div>
    </div>

    <!-- Page Content -->
    <div class="container px-[60px] max-lg:px-8 max-sm:px-4">
        <div style="padding:32px 0 12px;">
            <h1 style="font-family:'Montserrat',sans-serif;font-size:26px;font-weight:500;letter-spacing:.04em;text-transform:uppercase;color:#111;margin:0 0 6px;">@lang('shop::app.checkout.onepage.index.checkout')</h1>
            <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;margin:0;">Complete your delivery, shipping, and payment details to place your order.</p>
        </div>

        {!! view_render_event('frooxi.shop.checkout.onepage.header.after') !!}

        <!-- Checkout Vue Component -->
        <v-checkout>
            <!-- Shimmer Effect -->
            <x-shop::shimmer.checkout.onepage />
        </v-checkout>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-checkout-template"
        >
            <template v-if="! cart">
                <!-- Shimmer Effect -->
                <x-shop::shimmer.checkout.onepage />
            </template>

            <template v-else>
                <div class="grid grid-cols-[minmax(0,1fr)_420px] gap-10 pb-10 max-lg:grid-cols-1 max-md:gap-6 max-md:pb-0">
                    <!-- Included Checkout Summary Blade File For Mobile view -->
                    <div class="hidden max-md:block">
                        @include('shop::checkout.onepage.summary')
                    </div>

                    <div
                        class="overflow-y-auto max-md:grid max-md:gap-4"
                        id="steps-container"
                    >
                        <!-- Included Addresses Blade File -->
                        <template v-if="['address', 'shipping', 'payment', 'review'].includes(currentStep)">
                            @include('shop::checkout.onepage.address')
                        </template>

                        <!-- Included Shipping Methods Blade File -->
                        <template v-if="cart.have_stockable_items && ['shipping', 'payment', 'review'].includes(currentStep)">
                            @include('shop::checkout.onepage.shipping')
                        </template>

                        <!-- Included Payment Methods Blade File -->
                        <template v-if="['payment', 'review'].includes(currentStep)">
                            @include('shop::checkout.onepage.payment')
                        </template>
                    </div>

                    <!-- Included Checkout Summary Blade File For Desktop view -->
                    <div class="sticky top-8 block h-max w-full max-w-full max-lg:w-auto ltr:pl-2 max-lg:ltr:pl-0 rtl:pr-2 max-lg:rtl:pr-0">
                        <div class="block max-md:hidden">
                            @include('shop::checkout.onepage.summary')
                        </div>

                        <div
                            class="mt-4 flex justify-end"
                            v-if="canPlaceOrder"
                        >
                            <template v-if="cart.payment_method == 'paypal_smart_button'">
                                {!! view_render_event('frooxi.shop.checkout.onepage.summary.paypal_smart_button.before') !!}

                                <!-- Paypal Smart Button Vue Component -->
                                <v-paypal-smart-button></v-paypal-smart-button>

                                {!! view_render_event('frooxi.shop.checkout.onepage.summary.paypal_smart_button.after') !!}
                            </template>

                            <template v-else>
                                <x-shop::button
                                    type="button"
                                    style="width:100%;height:52px;background:#e30612;color:#fff;border:none;border-radius:8px;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;cursor:pointer;"
                                    :title="trans('shop::app.checkout.onepage.summary.place-order')"
                                    ::disabled="isPlacingOrder"
                                    ::loading="isPlacingOrder"
                                    @click="placeOrder"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </script>

        <script type="module">
            app.component('v-checkout', {
                template: '#v-checkout-template',

                data() {
                    return {
                        cart: null,

                        displayTax: {
                            prices: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_prices') }}",

                            subtotal: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_subtotal') }}",
                            
                            shipping: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_shipping_amount') }}",
                        },

                        isPlacingOrder: false,

                        currentStep: 'address',

                        shippingMethods: null,

                        paymentMethods: null,

                        canPlaceOrder: false,
                    }
                },

                mounted() {
                    this.getCart();
                },

                methods: {
                    getCart() {
                        this.$axios.get("{{ route('shop.checkout.onepage.summary') }}")
                            .then(response => {
                                this.cart = response.data.data;

                                this.scrollToCurrentStep();
                            })
                            .catch(error => {});
                    },

                    stepForward(step) {
                        this.currentStep = step;

                        if (step == 'review') {
                            this.canPlaceOrder = true;

                            return;
                        }

                        this.canPlaceOrder = false;

                        if (this.currentStep == 'shipping') {
                            this.shippingMethods = null;
                        } else if (this.currentStep == 'payment') {
                            this.paymentMethods = null;
                        }
                    },

                    stepProcessed(data) {
                        if (this.currentStep == 'shipping') {
                            this.shippingMethods = data;
                        } else if (this.currentStep == 'payment') {
                            this.paymentMethods = data;
                        }

                        this.getCart();
                    },

                    scrollToCurrentStep() {
                        let container = document.getElementById('steps-container');

                        if (! container) {
                            return;
                        }

                        container.scrollIntoView({
                            behavior: 'smooth',
                            block: 'end'
                        });
                    },

                    placeOrder() {
                        this.isPlacingOrder = true;

                        this.$axios.post('{{ route('shop.checkout.onepage.orders.store') }}')
                            .then(response => {
                                if (response.data.data.redirect) {
                                    window.location.href = response.data.data.redirect_url;
                                } else {
                                    window.location.href = '{{ route('shop.checkout.onepage.success') }}';
                                }

                                this.isPlacingOrder = false;
                            })
                            .catch(error => {
                                this.isPlacingOrder = false

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    }
                },
            });
        </script>
    @endPushOnce
</x-shop::layouts>
