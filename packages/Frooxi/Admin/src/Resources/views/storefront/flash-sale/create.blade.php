<x-admin::layouts>
    <x-slot:title>
        Create Flash Sale Product
    </x-slot>

    <x-admin::form
        :action="route('admin.storefront.flash_sale.store') . '?flash_sale=1'"
        enctype="multipart/form-data"
    >
        <!-- Page Header -->
        <div class="grid gap-2.5">
            <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
                <div class="grid gap-1.5">
                    <p class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
                        Create Flash Sale Product
                    </p>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Back Button -->
                    <a
                        href="{{ route('admin.storefront.flash_sale.index') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                    >
                        <span class="icon-arrow-left text-lg"></span>
                        Back
                    </a>

                    <!-- Save Button -->
                    <button class="primary-button">
                        Save Flash Sale Product
                    </button>
                </div>
            </div>
        </div>

        @php
            $channels = core()->getAllChannels();
            $currentChannel = core()->getRequestedChannel();
            $currentLocale = core()->getRequestedLocale();
        @endphp

        <!-- body content -->
        <div class="mt-5 flex gap-6 max-xl:flex-wrap">
            <!-- Left Column (flex-1) -->
            <div class="flex-1 max-xl:flex-auto">
                <!-- General Section -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        General
                    </p>

                    <!-- SKU -->
                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label class="required">
                            SKU
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="sku"
                            name="sku"
                            ::rules="[]"
                            :value="old('sku')"
                            label="SKU"
                        />

                        <x-admin::form.control-group.error control-name="sku" />
                    </x-admin::form.control-group>
                </div>

                <!-- Product Number Section -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Product Number
                    </p>

                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label>
                            Product Number
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="product_number"
                            name="product_number"
                            ::rules="[]"
                            :value="old('product_number')"
                            label="Product Number"
                        />

                        <x-admin::form.control-group.error control-name="product_number" />
                    </x-admin::form.control-group>
                </div>

                <!-- Description Section -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Description
                        @if ($currentChannel->locales->count() > 1)
                            <span class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600">
                                {{ $currentLocale->name }}
                            </span>
                        @endif
                    </p>

                    <!-- Name -->
                    <x-admin::form.control-group class="mb-4 last:!mb-0">
                        <x-admin::form.control-group.label class="required">
                            Name
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="name"
                            name="name"
                            ::rules="[]"
                            :value="old('name')"
                            label="Name"
                        />

                        <x-admin::form.control-group.error control-name="name" />
                    </x-admin::form.control-group>
                </div>

                <!-- URL Key Section -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        URL Key
                        @if ($currentChannel->locales->count() > 1)
                            <span class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600">
                                {{ $currentLocale->name }}
                            </span>
                        @endif
                    </p>

                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label>
                            URL Key
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="url_key"
                            name="url_key"
                            ::rules="[]"
                            :value="old('url_key')"
                            label="URL Key"
                        />

                        <x-admin::form.control-group.error control-name="url_key" />
                    </x-admin::form.control-group>
                </div>

                <!-- Short Description Section -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Short Description
                        @if ($currentChannel->locales->count() > 1)
                            <span class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600">
                                {{ $currentLocale->name }}
                            </span>
                        @endif
                    </p>

                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label>
                            Short Description
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            id="short_description"
                            name="short_description"
                            ::rules="[]"
                            :value="old('short_description')"
                            label="Short Description"
                            :tinymce="true"
                        />

                        <x-admin::form.control-group.error control-name="short_description" />
                    </x-admin::form.control-group>
                </div>

                <!-- Description Section (Main) -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Description
                        @if ($currentChannel->locales->count() > 1)
                            <span class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600">
                                {{ $currentLocale->name }}
                            </span>
                        @endif
                    </p>

                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label>
                            Description
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            id="description"
                            name="description"
                            ::rules="[]"
                            :value="old('description')"
                            label="Description"
                            :tinymce="true"
                        />

                        <x-admin::form.control-group.error control-name="description" />
                    </x-admin::form.control-group>
                </div>

                <!-- Delivery Timeline Section -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Delivery Timeline
                        @if ($currentChannel->locales->count() > 1)
                            <span class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600">
                                {{ $currentLocale->name }}
                            </span>
                        @endif
                    </p>

                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label>
                            Delivery Timeline
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            id="delivery_timeline"
                            name="delivery_timeline"
                            ::rules="[]"
                            :value="old('delivery_timeline')"
                            label="Delivery Timeline"
                            :tinymce="false"
                        />

                        <x-admin::form.control-group.error control-name="delivery_timeline" />
                    </x-admin::form.control-group>
                </div>

                <!-- Care Instructions Section -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Care Instructions
                        @if ($currentChannel->locales->count() > 1)
                            <span class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600">
                                {{ $currentLocale->name }}
                            </span>
                        @endif
                    </p>

                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label>
                            Care Instructions
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            id="care_instructions"
                            name="care_instructions"
                            ::rules="[]"
                            :value="old('care_instructions')"
                            label="Care Instructions"
                            :tinymce="false"
                        />

                        <x-admin::form.control-group.error control-name="care_instructions" />
                    </x-admin::form.control-group>
                </div>

                <!-- Images -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <!-- Panel Header -->
                    <div class="mb-4 flex justify-between gap-5">
                        <div class="flex flex-col gap-2">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Images
                            </p>

                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                Image resolution should be like 560px X 609px
                            </p>
                        </div>
                    </div>

                    <!-- Image Upload Area -->
                    <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-6 text-center hover:border-[#D4A84B] transition-colors dark:border-gray-700 dark:bg-gray-800/50">
                        <x-admin::media.images
                            name="images[files]"
                            allow-multiple="true"
                            show-placeholders="true"
                        />
                    </div>

                    <x-admin::form.control-group.error control-name="images.files[0]" />
                </div>

                <!-- Videos -->
                <div class="mb-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <!-- Panel Header -->
                    <div class="mb-4 flex justify-between gap-5">
                        <div class="flex flex-col gap-2">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Videos
                            </p>

                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                YouTube or Vimeo URL
                            </p>
                        </div>
                    </div>

                    <!-- Video Upload Area -->
                    <x-admin::media.videos
                        name="videos[files]"
                        allow-multiple="true"
                    />

                    <x-admin::form.control-group.error control-name="videos.files[0]" />
                </div>
            </div>

            <!-- Right Column (w-[360px]) -->
            <div class="w-[360px] max-w-full max-sm:w-full flex flex-col gap-4">
                <!-- Price -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Price
                    </p>

                    <!-- Base Price -->
                    <x-admin::form.control-group class="last:!mb-0">
                        <x-admin::form.control-group.label class="required">
                            Base Price
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="price"
                            id="price"
                            name="price"
                            ::rules="[]"
                            :value="old('price')"
                            label="Base Price"
                        >
                            <x-slot:currency>
                                {{ core()->getBaseCurrencyCode() }}
                            </x-slot>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="price" />
                    </x-admin::form.control-group>

                    <!-- Discount Percentage -->
                    <x-admin::form.control-group class="!mt-4 last:!mb-0">
                        <x-admin::form.control-group.label class="required">
                            Discount Percentage
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="number"
                            id="flash_sale_discount"
                            name="flash_sale_discount"
                            ::rules="'required|numeric|min_value:1|max_value:99'"
                            :value="old('flash_sale_discount')"
                            label="Discount Percentage"
                            min="1"
                            max="99"
                        >
                            <x-slot:suffix>
                                <span class="text-sm text-gray-500">%</span>
                            </x-slot>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="flash_sale_discount" />
                    </x-admin::form.control-group>
                </div>

                <!-- Settings -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Settings
                    </p>

                    <div class="grid gap-4">
                        <!-- New -->
                        <x-admin::form.control-group class="last:!mb-0">
                            <x-admin::form.control-group.label>
                                New
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="switch"
                                id="new"
                                name="new"
                                value="1"
                                label="New"
                            />
                        </x-admin::form.control-group>

                        <!-- Featured -->
                        <x-admin::form.control-group class="last:!mb-0">
                            <x-admin::form.control-group.label>
                                Featured
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="switch"
                                id="featured"
                                name="featured"
                                value="1"
                                label="Featured"
                            />
                        </x-admin::form.control-group>

                        <!-- Visible Individually -->
                        <x-admin::form.control-group class="last:!mb-0">
                            <x-admin::form.control-group.label>
                                Visible Individually
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="switch"
                                id="visible_individually"
                                name="visible_individually"
                                value="1"
                                :checked="true"
                                label="Visible Individually"
                            />
                        </x-admin::form.control-group>

                        <!-- Status -->
                        <x-admin::form.control-group class="last:!mb-0">
                            <x-admin::form.control-group.label>
                                Status
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="switch"
                                id="status"
                                name="status"
                                value="1"
                                :checked="true"
                                label="Status"
                            />
                        </x-admin::form.control-group>

                        <!-- Guest Checkout -->
                        <x-admin::form.control-group class="last:!mb-0">
                            <x-admin::form.control-group.label>
                                Guest Checkout
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="switch"
                                id="guest_checkout"
                                name="guest_checkout"
                                value="1"
                                :checked="true"
                                label="Guest Checkout"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- Inventories -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Inventories
                    </p>

                    <!-- Manage Stock -->
                    <x-admin::form.control-group class="mb-4">
                        <x-admin::form.control-group.label>
                            Manage Stock
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="switch"
                            name="manage_stock"
                            value="1"
                            label="Manage Stock"
                            :checked="true"
                            id="manage_stock"
                        />

                        <p class="mt-1 text-xs text-gray-500">
                            Enable to track and manage stock quantity
                        </p>
                    </x-admin::form.control-group>

                    <v-inventories>
                        <div class="mt-3">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Stock Quantity
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="inventories[1]"
                                    ::rules="[]"
                                    :value="old('inventories.1', 0)"
                                    label="Stock Quantity"
                                    placeholder="Enter stock quantity"
                                />

                                <x-admin::form.control-group.error control-name="inventories[1]" />

                                <p class="mt-1 text-xs text-gray-500">
                                    Stock will automatically reduce after each purchase
                                </p>
                            </x-admin::form.control-group>
                        </div>
                    </v-inventories>
                </div>

                <!-- Categories -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Categories
                    </p>

                    <v-product-categories>
                        <x-admin::shimmer.tree />
                    </v-product-categories>
                </div>
            </div>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <!-- v-inventories component -->
        <script
            type="text/x-template"
            id="v-inventories-template"
        >
            <div v-show="manageStock">
                <slot></slot>
            </div>
        </script>

        <script type="module">
            app.component('v-inventories', {
                template: '#v-inventories-template',

                data() {
                    return {
                        manageStock: true,
                    }
                },

                mounted() {
                    let self = this;
                    document.getElementById('manage_stock').addEventListener('change', function(e) {
                        self.manageStock = e.target.checked;
                    });
                }
            });
        </script>

        <!-- v-product-categories component -->
        <script
            type="text/x-template"
            id="v-product-categories-template"
        >
            <div>
                <template v-if="isLoading">
                    <x-admin::shimmer.tree />
                </template>

                <template v-else>
                    <x-admin::tree.view
                        input-type="checkbox"
                        selection-type="individual"
                        name-field="categories"
                        id-field="id"
                        value-field="id"
                        ::items="categories"
                        :fallback-locale="config('app.fallback_locale')"
                    />
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-product-categories', {
                template: '#v-product-categories-template',

                data() {
                    return {
                        isLoading: true,
                        categories: [],
                    }
                },

                mounted() {
                    this.get();
                },

                methods: {
                    get() {
                        axios.get("{{ route('admin.catalog.categories.tree') }}", {
                                params: {
                                    channel: "{{ $currentChannel->code }}",
                                }
                            })
                            .then(response => {
                                this.isLoading = false;
                                this.categories = response.data.data;
                            }).catch(error => {
                                console.log(error);
                            });
                    }
                }
            });
        </script>
    @endpushOnce
</x-admin::layouts>
