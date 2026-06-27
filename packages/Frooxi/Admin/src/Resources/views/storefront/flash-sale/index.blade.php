<x-admin::layouts>
    <x-slot:title>
        Flash Sale Products
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
            Flash Sale Products
        </p>

        <div class="flex items-center gap-x-2.5">
            <v-create-product-form>
                <button
                    type="button"
                    class="primary-button"
                >
                    Create Flash Sale Product
                </button>
            </v-create-product-form>
        </div>
    </div>

    <!-- Datagrid -->
    <x-admin::datagrid :src="route('admin.storefront.flash_sale.index')" />

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-create-product-form-template"
        >
            <div>
                <!-- Product Create Button -->
                @if (bouncer()->hasPermission('catalog.products.create'))
                    <button
                        type="button"
                        class="primary-button"
                        @click="$refs.productCreateModal.toggle()"
                    >
                        Create Flash Sale Product
                    </button>
                @endif

                <x-admin::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form @submit="handleSubmit($event, create)">
                        <!-- Customer Create Modal -->
                        <x-admin::modal ref="productCreateModal">
                            <!-- Modal Header -->
                            <x-slot:header>
                                <p
                                    class="text-lg font-bold text-gray-800 dark:text-white"
                                    v-if="! attributes.length"
                                >
                                    Create Flash Sale Product
                                </p>

                                <p
                                    class="text-lg font-bold text-gray-800 dark:text-white"
                                    v-else
                                >
                                    @lang('admin::app.catalog.products.index.create.configurable-attributes')
                                </p>
                            </x-slot>

                            <!-- Modal Content -->
                            <x-slot:content>
                                <div v-show="! attributes.length">
                                    {!! view_render_event('frooxi.admin.catalog.products.create_form.general.controls.before') !!}

                                    <!-- Product Type (Auto-assigned to Simple) -->
                                    <input type="hidden" name="type" value="simple">

                                    <!-- Attribute Family Id (hidden, auto-assigned to default) -->
                                    <input type="hidden" name="attribute_family_id" value="1">

                                    <!-- SKU -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('admin::app.catalog.products.index.create.sku')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="sku"
                                            ::rules="{ required: true, regex: /^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/ }"
                                            :label="trans('admin::app.catalog.products.index.create.sku')"
                                        />

                                        <x-admin::form.control-group.error control-name="sku" />
                                    </x-admin::form.control-group>

                                    {!! view_render_event('frooxi.admin.catalog.products.create_form.general.controls.after') !!}
                                </div>

                                <div v-show="attributes.length">
                                    {!! view_render_event('frooxi.admin.catalog.products.create_form.attributes.controls.before') !!}

                                    <div
                                        class="mb-4"
                                        v-for="attribute in attributes"
                                    >
                                        <!-- OTHER ATTRIBUTES: default chips -->
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="block text-xs font-medium leading-6 text-gray-800 dark:text-white">
                                                @{{ attribute.name }}
                                            </label>
                                            <span
                                                class="icon-cross cursor-pointer text-lg text-gray-400 hover:text-red-500"
                                                title="Remove this attribute"
                                                @click="removeAttribute(attribute)"
                                            ></span>
                                        </div>
                                        <div class="flex min-h-[38px] flex-wrap gap-1 rounded-md border p-1.5 dark:border-gray-800">
                                            <p
                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                v-for="option in attribute.options"
                                            >
                                                @{{ option.name }}
                                                <span
                                                    class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                    @click="removeOption(option)"
                                                ></span>
                                            </p>
                                        </div>
                                    </div>

                                    {!! view_render_event('frooxi.admin.catalog.products.create_form.attributes.controls.after') !!}
                                </div>
                            </x-slot>

                            <!-- Modal Footer -->
                            <x-slot:footer>
                                <div class="flex items-center gap-x-2.5">
                                    <!-- Back Button -->
                                    <x-admin::button
                                        button-type="button"
                                        class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                                        :title="trans('admin::app.catalog.products.index.create.back-btn')"
                                        v-if="attributes.length"
                                        @click="attributes = []"
                                    />

                                    <!-- Save Button -->
                                    <x-admin::button
                                        button-type="submit"
                                        class="primary-button"
                                        :title="trans('admin::app.catalog.products.index.create.save-btn')"
                                        ::loading="isLoading"
                                        ::disabled="isLoading || (attributes.length && ! hasValidSelection())"
                                        @click="attributes.length ? $event.preventDefault() || submitConfigurable() : ''"
                                    />
                                </div>
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-create-product-form', {
                template: '#v-create-product-form-template',

                data() {
                    return {
                        attributes: [],
                        superAttributes: {},
                        isLoading: false,
                    };
                },

                mounted() {
                    @if (isset($create_flash_sale))
                        this.$refs.productCreateModal.toggle();
                    @endif
                },

                methods: {
                    create(params, { resetForm, resetField, setErrors }) {
                        this.isLoading = true;
                        
                        params.flash_sale = 1;
                        params.type = 'simple';
                        params.attribute_family_id = 1;

                        this.attributes.forEach(attribute => {
                            params.super_attributes ||= {};
                            params.super_attributes[attribute.code] = this.superAttributes[attribute.code];
                        });

                        this.$axios.post("{{ route('admin.catalog.products.store') }}", params)
                            .then((response) => {
                                this.isLoading = false;

                                if (response.data.data.redirect_url) {
                                    window.location.href = response.data.data.redirect_url;
                                } else {
                                    this.attributes = response.data.data.attributes;
                                    this.setSuperAttributes();
                                }
                            })
                            .catch(error => {
                                this.isLoading = false;
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    },

                    removeOption(option) {
                        this.attributes.forEach(attribute => {
                            attribute.options = attribute.options.filter(item => item.id != option.id);
                        });
                        this.attributes = this.attributes.filter(attribute => attribute.options.length > 0);
                        this.setSuperAttributes();
                    },

                    removeAttribute(attribute) {
                        this.attributes = this.attributes.filter(a => a.code !== attribute.code);
                        this.setSuperAttributes();
                    },

                    setSuperAttributes() {
                        this.superAttributes = {};
                        this.attributes.forEach(attribute => {
                            this.superAttributes[attribute.code] = [];
                            attribute.options.forEach(option => {
                                this.superAttributes[attribute.code].push(option.id);
                            });
                        });
                    },

                    hasValidSelection() {
                        return this.attributes.length > 0 && this.attributes.some(attr => {
                            const ids = this.superAttributes[attr.code];
                            return ids && ids.length > 0;
                        });
                    },

                    submitConfigurable() {
                        if (this.isLoading) return;

                        const superAttrs = {};
                        this.attributes.forEach(attr => {
                            const ids = this.superAttributes[attr.code];
                            if (ids && ids.length > 0) {
                                superAttrs[attr.code] = ids;
                            }
                        });

                        if (Object.keys(superAttrs).length === 0) {
                            this.$emitter.emit('add-flash', { type: 'warning', message: 'Please select at least one size or color.' });
                            return;
                        }

                        this.isLoading = true;
                        const form = this.$el.querySelector('form');
                        const formData = new FormData(form);
                        const params = {};
                        formData.forEach((value, key) => { params[key] = value; });
                        params.super_attributes = superAttrs;
                        params.flash_sale = 1;

                        this.$axios.post("{{ route('admin.catalog.products.store') }}", params)
                            .then((response) => {
                                this.isLoading = false;
                                if (response.data.data.redirect_url) {
                                    window.location.href = response.data.data.redirect_url;
                                }
                            })
                            .catch(error => {
                                this.isLoading = false;
                                const msg = error.response?.data?.message || 'Failed to save product.';
                                this.$emitter.emit('add-flash', { type: 'error', message: msg });
                            });
                    },
                }
            })
        </script>
    @endPushOnce
</x-admin::layouts>
