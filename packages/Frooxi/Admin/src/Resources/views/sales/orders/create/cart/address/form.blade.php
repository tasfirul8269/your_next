@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-checkout-address-form-template"
    >
        <div class="mt-2">
            <x-admin::form.control-group class="hidden">
                <x-admin::form.control-group.control
                    type="text"
                    ::name="controlName + '.id'"
                    ::value="address.id"
                />
            </x-admin::form.control-group>

            <!-- First Name -->
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required !mt-0">
                    @lang('admin::app.sales.orders.create.cart.address.first-name')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    ::name="controlName + '.first_name'"
                    ::value="address.first_name"
                    rules="required"
                    :label="trans('admin::app.sales.orders.create.cart.address.first-name')"
                    :placeholder="trans('admin::app.sales.orders.create.cart.address.first-name')"
                />

                <x-admin::form.control-group.error ::name="controlName + '.first_name'" />
            </x-admin::form.control-group>

            {!! view_render_event('frooxi.admin.sales.order.create.cart.address.form.first_name.after') !!}

            <!-- Last Name -->
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required !mt-0">
                    @lang('admin::app.sales.orders.create.cart.address.last-name')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    ::name="controlName + '.last_name'"
                    ::value="address.last_name"
                    rules="required"
                    :label="trans('admin::app.sales.orders.create.cart.address.last-name')"
                    :placeholder="trans('admin::app.sales.orders.create.cart.address.last-name')"
                />

                <x-admin::form.control-group.error ::name="controlName + '.last_name'" />
            </x-admin::form.control-group>

            {!! view_render_event('frooxi.admin.sales.order.create.cart.address.form.last_name.after') !!}

            <!-- Email -->
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required !mt-0">
                    @lang('admin::app.sales.orders.create.cart.address.email')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="email"
                    ::name="controlName + '.email'"
                    ::value="address.email"
                    rules="required|email"
                    :label="trans('admin::app.sales.orders.create.cart.address.email')"
                    placeholder="email@example.com"
                />

                <x-admin::form.control-group.error ::name="controlName + '.email'" />
            </x-admin::form.control-group>

            {!! view_render_event('frooxi.admin.sales.order.create.cart.address.form.email.after') !!}

            <!-- Street Address -->
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required !mt-0">
                    @lang('admin::app.sales.orders.create.cart.address.street-address')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    ::name="controlName + '.address.[0]'"
                    ::value="address.address[0]"
                    rules="required|address"
                    :label="trans('admin::app.sales.orders.create.cart.address.street-address')"
                    :placeholder="trans('admin::app.sales.orders.create.cart.address.street-address')"
                />

                <x-admin::form.control-group.error
                    class="mb-2"
                    ::name="controlName + '.address.[0]'"
                />

                @if (core()->getConfigData('customer.address.information.street_lines') > 1)
                    @for ($i = 1; $i < core()->getConfigData('customer.address.information.street_lines'); $i++)
                        <x-admin::form.control-group.control
                            type="text"
                            ::name="controlName + '.address.[{{ $i }}]'"
                            class="mt-2"
                            rules="address"
                            :label="trans('admin::app.sales.orders.create.cart.address.street-address')"
                            :placeholder="trans('admin::app.sales.orders.create.cart.address.street-address')"
                        />

                        <x-admin::form.control-group.error
                            class="mb-2"
                            ::name="controlName + '.address.[{{ $i }}]'"
                        />
                    @endfor
                @endif
            </x-admin::form.control-group>

            {!! view_render_event('frooxi.admin.sales.order.create.cart.address.form.address.after') !!}

            <!-- City -->
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required !mt-0">
                    @lang('admin::app.sales.orders.create.cart.address.city')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    ::name="controlName + '.city'"
                    ::value="address.city"
                    rules="required"
                    :label="trans('admin::app.sales.orders.create.cart.address.city')"
                    :placeholder="trans('admin::app.sales.orders.create.cart.address.city')"
                />

                <x-admin::form.control-group.error ::name="controlName + '.city'" />
            </x-admin::form.control-group>

            {!! view_render_event('frooxi.admin.sales.order.create.cart.address.form.city.after') !!}

            <!-- Mobile Number -->
            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required !mt-0">
                    @lang('admin::app.sales.orders.create.cart.address.mobile-number')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    ::name="controlName + '.phone'"
                    ::value="address.phone"
                    rules="required|phone"
                    :label="trans('admin::app.sales.orders.create.cart.address.mobile-number')"
                    :placeholder="trans('admin::app.sales.orders.create.cart.address.mobile-number')"
                />

                <x-admin::form.control-group.error ::name="controlName + '.phone'" />
            </x-admin::form.control-group>

            {!! view_render_event('frooxi.admin.sales.order.create.cart.address.form.phone.after') !!}
        </div>
    </script>

    <script type="module">
        app.component('v-checkout-address-form', {
            template: '#v-checkout-address-form-template',

            props: {
                controlName: {
                    type: String,
                    required: true,
                },

                address: {
                    type: Object,

                    default: () => ({
                        id: 0,
                        first_name: '',
                        last_name: '',
                        email: '',
                        address: [],
                        city: '',
                        phone: '',
                    }),
                },
            },

            data() {
                return {
                    selectedCountry: this.address.country,

                    countries: [],

                    states: null,
                }
            },

            mounted() {
                // Removed country and state loading
            },

            methods: {
                // Methods removed as fields are not needed
            }
        });
    </script>
@endPushOnce
