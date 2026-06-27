{!! view_render_event('frooxi.shop.checkout.onepage.payment_methods.before') !!}

<v-payment-methods
    :methods="paymentMethods"
    @processing="stepForward"
    @processed="stepProcessed"
>
    <x-shop::shimmer.checkout.onepage.payment-method />
</v-payment-methods>

{!! view_render_event('frooxi.shop.checkout.onepage.payment_methods.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-payment-methods-template"
    >
        <div class="mb-7 max-md:last:!mb-0">
            <template v-if="! methods">
                <!-- Payment Method shimmer Effect -->
                <x-shop::shimmer.checkout.onepage.payment-method />
            </template>
    
            <template v-else>
                {!! view_render_event('frooxi.shop.checkout.onepage.payment_method.accordion.before') !!}

                <!-- Themed step card -->
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                    <div style="display:flex;align-items:center;gap:14px;padding:20px 24px;border-bottom:1px solid #f3f4f6;">
                        <div style="width:28px;height:28px;border-radius:50%;background:#e30612;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;color:#fff;">3</span>
                        </div>
                        <h2 style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#111;margin:0;">
                            @lang('shop::app.checkout.onepage.payment.payment-method')
                        </h2>
                    </div>

                    <div style="padding:24px;">
                        <div style="display:flex;flex-wrap:wrap;gap:16px;">
                            <div
                                style="position:relative;cursor:pointer;flex:1;min-width:180px;max-width:220px;"
                                v-for="(payment, index) in methods"
                            >
                                {!! view_render_event('frooxi.shop.checkout.payment-method.before') !!}

                                <input
                                    type="radio"
                                    name="payment[method]"
                                    :value="payment.payment"
                                    :id="payment.method"
                                    class="peer hidden"
                                    @change="store(payment)"
                                >

                                <label
                                    :for="payment.method"
                                    style="display:block;cursor:pointer;border:1.5px solid #e5e7eb;border-radius:10px;padding:16px;transition:border-color .15s;"
                                    class="peer-checked:!border-black"
                                >
                                    {!! view_render_event('frooxi.shop.checkout.onepage.payment-method.image.before') !!}
                                    <img
                                        style="max-height:36px;max-width:56px;margin-bottom:10px;"
                                        :src="payment.image"
                                        width="55"
                                        height="55"
                                        :alt="payment.method_title"
                                        :title="payment.method_title"
                                    />
                                    {!! view_render_event('frooxi.shop.checkout.onepage.payment-method.image.after') !!}

                                    {!! view_render_event('frooxi.shop.checkout.onepage.payment-method.title.before') !!}
                                    <p style="font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;color:#111;margin:0 0 4px;">@{{ payment.method_title }}</p>
                                    {!! view_render_event('frooxi.shop.checkout.onepage.payment-method.title.after') !!}

                                    {!! view_render_event('frooxi.shop.checkout.onepage.payment-method.description.before') !!}
                                    <p style="font-family:'Montserrat',sans-serif;font-size:11px;color:#9ca3af;">@{{ payment.description }}</p>
                                    {!! view_render_event('frooxi.shop.checkout.onepage.payment-method.description.after') !!}
                                </label>

                                <label
                                    class="icon-radio-unselect peer-checked:icon-radio-select"
                                    :for="payment.method"
                                    style="position:absolute;top:14px;right:14px;cursor:pointer;font-size:22px;color:#111;"
                                ></label>

                                {!! view_render_event('frooxi.shop.checkout.payment-method.after') !!}
                            </div>
                        </div>
                    </div>
                </div>

                {!! view_render_event('frooxi.shop.checkout.onepage.payment_method.accordion.after') !!}
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-payment-methods', {
            template: '#v-payment-methods-template',

            props: {
                methods: {
                    type: Object,
                    required: true,
                    default: () => null,
                },
            },

            emits: ['processing', 'processed'],

            methods: {
                store(selectedMethod) {
                    this.$emit('processing', 'review');

                    this.$axios.post("{{ route('shop.checkout.onepage.payment_methods.store') }}", {
                            payment: selectedMethod
                        })
                        .then(response => {
                            this.$emit('processed', response.data.cart);

                            // Used in mobile view. 
                            if (window.innerWidth <= 768) {
                                window.scrollTo({
                                    top: document.body.scrollHeight,
                                    behavior: 'smooth'
                                });
                            }
                        })
                        .catch(error => {
                            this.$emit('processing', 'payment');

                            if (error.response.data.redirect_url) {
                                window.location.href = error.response.data.redirect_url;
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce
