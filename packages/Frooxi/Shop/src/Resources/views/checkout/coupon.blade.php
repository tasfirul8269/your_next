<v-coupon></v-coupon>

@pushOnce('scripts')
    <script type="text/x-template" id="v-coupon-template">
        <div class="mt-4">
            <!-- Applied Coupon -->
            <div v-if="cart.coupon_code" class="flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-800">@{{ cart.coupon_code }}</span>
                </div>
                <button
                    type="button"
                    @click="removeCoupon"
                    class="text-sm font-medium text-red-600 hover:text-red-800"
                >
                    @lang('shop::app.checkout.cart.remove-coupon')
                </button>
            </div>

            <!-- Coupon Input -->
            <div v-else class="flex gap-2">
                <input
                    type="text"
                    v-model="couponCode"
                    :placeholder="__('shop::app.checkout.onepage.summary.enter-coupon-code')"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500"
                    @keyup.enter="applyCoupon"
                />
                <button
                    type="button"
                    @click="applyCoupon"
                    :disabled="isProcessing"
                    class="rounded-lg bg-[#e30612] px-6 py-2.5 text-sm font-medium text-white transition-all hover:opacity-80 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    @{{ isProcessing ? __('shop::app.checkout.onepage.summary.applying') : __('shop::app.checkout.onepage.summary.apply-coupon') }}
                </button>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-coupon', {
            template: '#v-coupon-template',

            data() {
                return {
                    couponCode: '',
                    isProcessing: false,
                    cart: {},
                };
            },

            mounted() {
                this.cart = window.cart || {};
            },

            methods: {
                async applyCoupon() {
                    if (!this.couponCode.trim()) {
                        this.$emitter.emit('add-flash', { 
                            type: 'error', 
                            message: __('shop::app.checkout.onepage.summary.coupon-code-required') 
                        });
                        return;
                    }

                    this.isProcessing = true;

                    try {
                        const response = await axios.post(
                            '/api/checkout/cart/coupon',
                            { code: this.couponCode }
                        );

                        this.$emitter.emit('add-flash', { 
                            type: 'success', 
                            message: response.data.message 
                        });

                        window.location.reload();
                    } catch (error) {
                        const message = error.response?.data?.message || __('shop::app.checkout.onepage.summary.coupon-apply-error');
                        this.$emitter.emit('add-flash', { type: 'error', message });
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async removeCoupon() {
                    try {
                        const response = await axios.delete(
                            '/api/checkout/cart/coupon'
                        );

                        this.$emitter.emit('add-flash', { 
                            type: 'success', 
                            message: response.data.message 
                        });

                        window.location.reload();
                    } catch (error) {
                        this.$emitter.emit('add-flash', { 
                            type: 'error', 
                            message: __('shop::app.checkout.onepage.summary.coupon-remove-error') 
                        });
                    }
                },
            },
        });
    </script>
@endpushOnce
