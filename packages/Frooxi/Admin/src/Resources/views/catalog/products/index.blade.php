<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.products.index.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
            @lang('admin::app.catalog.products.index.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            <!-- Export Modal -->
            <x-admin::datagrid.export
                :src="route('admin.catalog.products.index')"
                class="secondary-button"
            />

            {!! view_render_event('frooxi.admin.catalog.products.create.before') !!}

            @if (bouncer()->hasPermission('catalog.products.create'))
                <v-create-product-form>
                    <button
                        type="button"
                        class="primary-button"
                    >
                        @lang('admin::app.catalog.products.index.create-btn')
                    </button>
                </v-create-product-form>
            @endif

            {!! view_render_event('frooxi.admin.catalog.products.create.after') !!}
        </div>
    </div>

    {!! view_render_event('frooxi.admin.catalog.products.list.before') !!}

    <!-- Datagrid -->
    <x-admin::datagrid
        :src="route('admin.catalog.products.index')"
        :isMultiRow="true"
    >
        <!-- Datagrid Header -->
        @php
            $hasPermission = bouncer()->hasPermission('catalog.products.edit') || bouncer()->hasPermission('catalog.products.delete');
        @endphp

        <template #header="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.table.head :isMultiRow="true" />
            </template>

            <template v-else>
                <span></span>
            </template>
        </template>

        <template #body="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.table.body :isMultiRow="true" />
            </template>

            <template v-else>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse" style="min-width: 700px;">
                        <colgroup>
                            @if ($hasPermission)
                            <col style="width: 48px;" />
                            @endif
                            <col style="width: 70px;" />   {{-- Image --}}
                            <col style="width: auto;" />   {{-- Name --}}
                            <col style="width: 120px;" />  {{-- Price --}}
                            <col style="width: 90px;" />   {{-- Quantity --}}
                            <col style="width: 80px;" />   {{-- Status --}}
                            <col style="width: 100px;" />  {{-- Actions --}}
                        </colgroup>
                        <thead>
                            <tr class="border-b bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
                                @if ($hasPermission)
                                <th class="px-4 py-3">
                                    <label
                                        class="flex w-max cursor-pointer select-none items-center gap-1"
                                        for="mass_action_select_all_records"
                                    >
                                        <input
                                            type="checkbox"
                                            name="mass_action_select_all_records"
                                            id="mass_action_select_all_records"
                                            class="peer hidden"
                                            :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                            @change="selectAll"
                                        >
                                        <span
                                            class="icon-uncheckbox cursor-pointer rounded-md text-2xl"
                                            :class="[
                                                applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checked peer-checked:text-blue-600' : (
                                                    applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-partial peer-checked:text-blue-600' : ''
                                                ),
                                            ]"
                                        >
                                        </span>
                                    </label>
                                </th>
                                @endif
                                <th class="px-4 py-3 max-sm:px-2 max-sm:py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                <th class="px-4 py-3 max-sm:px-2 max-sm:py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 max-sm:px-2 max-sm:py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                <th class="px-4 py-3 max-sm:px-2 max-sm:py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 max-sm:px-2 max-sm:py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 max-sm:px-2 max-sm:py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                class="border-b transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                                v-for="record in available.records"
                                :key="record.product_id"
                            >
                                <!-- Checkbox -->
                                @if ($hasPermission)
                                <td class="px-4 py-3">
                                    <input
                                        type="checkbox"
                                        :name="`mass_action_select_record_${record.product_id}`"
                                        :id="`mass_action_select_record_${record.product_id}`"
                                        :value="record.product_id"
                                        class="peer hidden"
                                        v-model="applied.massActions.indices"
                                    >
                                    <label
                                        class="icon-uncheckbox peer-checked:icon-checked cursor-pointer rounded-md text-2xl peer-checked:text-blue-600"
                                        :for="`mass_action_select_record_${record.product_id}`"
                                    ></label>
                                </td>
                                @endif

                                <!-- Product Image -->
                                <td class="px-4 py-3">
                                    <template v-if="record.base_image">
                                        <img class="h-16 w-16 rounded object-cover" :src="record.base_image" />
                                    </template>
                                    <template v-else>
                                        <div class="relative h-16 w-16 rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert">
                                            <img src="{{ frooxi_asset('images/product-placeholders/front.svg')}}" class="h-full w-full object-cover">
                                            <p class="absolute bottom-0 w-full text-center text-[6px] font-semibold text-gray-400">
                                                @lang('admin::app.catalog.products.index.datagrid.product-image')
                                            </p>
                                        </div>
                                    </template>
                                </td>

                                <!-- Product Name & SKU -->
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                                        @{{ record.name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        SKU: @{{ record.sku }}
                                    </p>
                                </td>

                                <!-- Price (already formatted by DataGrid closure) -->
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white" v-html="record.price"></p>
                                </td>

                                <!-- Quantity (already formatted by DataGrid closure) -->
                                <td class="px-4 py-3">
                                    <p class="text-sm" v-html="record.quantity"></p>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    <p :class="[record.status ? 'label-active': 'label-info']" class="inline-block">
                                        @{{ record.status ? "@lang('admin::app.catalog.products.index.datagrid.active')" : "@lang('admin::app.catalog.products.index.datagrid.disable')" }}
                                    </p>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a
                                            v-if="record.url_key"
                                            :href="'{{ url('/') }}/' + record.url_key"
                                            target="_blank"
                                            class="cursor-pointer rounded-md p-1.5 text-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 icon-eye"
                                            title="@lang('admin::app.view')"
                                        ></a>
                                        <span
                                            class="cursor-pointer rounded-md p-1.5 text-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                                            :class="action.icon"
                                            v-text="! action.icon ? action.title : ''"
                                            v-for="action in record.actions"
                                            @click="performAction(action)"
                                        ></span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
        </template>
    </x-admin::datagrid>

    {!! view_render_event('frooxi.admin.catalog.products.list.after') !!}

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
                        {{ request()->get('flash_sale') ? 'Create Flash Sale Product' : trans('admin::app.catalog.products.index.create-btn') }}
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
                                    {{ request()->get('flash_sale') ? 'Create Flash Sale Product' : trans('admin::app.catalog.products.index.create.title') }}
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

                                    <!-- Product Type -->
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('admin::app.catalog.products.index.create.type')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="select"
                                            name="type"
                                            rules="required"
                                            :label="trans('admin::app.catalog.products.index.create.type')"
                                        >
                                            @foreach(config('product_types') as $key => $type)
                                                <option value="{{ $key }}">
                                                    @lang($type['name'])
                                                </option>
                                            @endforeach
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error control-name="type" />
                                    </x-admin::form.control-group>

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
                                        <!-- COLOR ATTRIBUTE: custom color name + picker -->
                                        <template v-if="attribute.code === 'color'">
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

                                            <!-- Selected colors as chips -->
                                            <div class="flex min-h-[38px] flex-wrap gap-1 rounded-md border p-1.5 dark:border-gray-800 mb-2" v-if="attribute.options.length">
                                                <p
                                                    class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white text-xs"
                                                    v-for="option in attribute.options"
                                                >
                                                    <span
                                                        v-if="option.swatch_value"
                                                        class="inline-block w-3 h-3 rounded-full border border-white ltr:mr-1.5 rtl:ml-1.5"
                                                        :style="{ background: option.swatch_value }"
                                                    ></span>
                                                    @{{ option.name }}
                                                    <span
                                                        class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                        @click="removeOption(option)"
                                                    ></span>
                                                </p>
                                            </div>

                                            <!-- Add color form -->
                                            <div class="flex gap-2 items-end">
                                                <div class="flex-1">
                                                    <label class="block text-[11px] text-gray-500 mb-0.5">Color Name</label>
                                                    <input
                                                        type="text"
                                                        v-model="newColorName"
                                                        placeholder="e.g. Navy Blue"
                                                        class="w-full rounded border border-gray-200 dark:border-gray-700 px-2.5 py-1.5 text-sm dark:bg-gray-900 dark:text-white"
                                                        @keydown.enter.prevent="addCustomColor(attribute)"
                                                    />
                                                </div>
                                                <div class="w-24">
                                                    <label class="block text-[11px] text-gray-500 mb-0.5">Color Code</label>
                                                    <div class="flex items-center gap-1">
                                                        <input
                                                            type="color"
                                                            v-model="newColorCode"
                                                            class="w-9 h-9 rounded border border-gray-200 dark:border-gray-700 cursor-pointer p-0.5"
                                                        />
                                                        <input
                                                            type="text"
                                                            v-model="newColorCode"
                                                            placeholder="#000000"
                                                            class="w-full rounded border border-gray-200 dark:border-gray-700 px-2 py-1.5 text-sm dark:bg-gray-900 dark:text-white font-mono"
                                                        />
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="secondary-button h-9 px-3 whitespace-nowrap"
                                                    @click="addCustomColor(attribute)"
                                                >
                                                    + Add Color
                                                </button>
                                            </div>
                                        </template>

                                        <!-- SIZE ATTRIBUTE: button → size picker popup -->
                                        <template v-else-if="attribute.code === 'size'">
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

                                            <!-- Selected sizes as chips -->
                                            <div class="flex min-h-[38px] flex-wrap gap-1 rounded-md border p-1.5 dark:border-gray-800 mb-2">
                                                <p
                                                    class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white text-xs"
                                                    v-for="option in attribute.options"
                                                >
                                                    @{{ option.name }}
                                                    <span
                                                        class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                        @click="removeOption(option)"
                                                    ></span>
                                                </p>
                                                <p v-if="! attribute.options.length" class="text-xs text-gray-400 px-1 py-1">No sizes selected</p>
                                            </div>

                                            <!-- Open size picker button -->
                                            <button
                                                type="button"
                                                class="secondary-button text-sm"
                                                @click="openSizePicker(attribute)"
                                            >
                                                Select Sizes
                                            </button>
                                        </template>

                                        <!-- OTHER ATTRIBUTES: default chips -->
                                        <template v-else>
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
                                        </template>
                                    </div>

                                    {!! view_render_event('frooxi.admin.catalog.products.create_form.attributes.controls.after') !!}
                                </div>

                                <!-- Size Picker Modal (inline overlay) -->
                                <div
                                    v-if="showSizePicker"
                                    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50"
                                    @click.self="showSizePicker = false"
                                >
                                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-2xl mx-4 p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Select Sizes</h3>
                                            <button type="button" class="text-gray-400 hover:text-gray-600" @click="showSizePicker = false">
                                                <span class="icon-cross text-2xl"></span>
                                            </button>
                                        </div>

                                        <div class="flex flex-wrap gap-2 max-h-72 overflow-y-auto mb-4">
                                            <button
                                                type="button"
                                                v-for="option in sizePickerOptions"
                                                :key="option.id"
                                                class="px-3 py-1.5 rounded text-sm font-semibold border-2 transition-all"
                                                :class="isSizeSelected(option) ? 'bg-gray-800 text-white border-gray-800' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:border-gray-600'"
                                                @click="toggleSizeOption(option)"
                                            >
                                                @{{ option.name }}
                                            </button>
                                        </div>

                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="transparent-button hover:bg-gray-200 dark:hover:bg-gray-800 dark:text-white" @click="showSizePicker = false">Cancel</button>
                                            <button type="button" class="primary-button" @click="confirmSizes">Done</button>
                                        </div>
                                    </div>
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
                                        button-type="button"
                                        class="primary-button"
                                        :title="trans('admin::app.catalog.products.index.create.save-btn')"
                                        ::loading="isLoading"
                                        ::disabled="isLoading || (attributes.length && ! hasValidSelection())"
                                        @click="attributes.length ? submitConfigurable() : $el.closest('form').requestSubmit()"
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

                        // Custom color inputs
                        newColorName: '',
                        newColorCode: '#000000',

                        // Size picker state
                        showSizePicker: false,
                        sizePickerAttribute: null,
                        sizePickerOptions: [],
                        tempSelectedSizes: [],
                    };
                },

                methods: {
                    create(params, { resetForm, resetField, setErrors }) {
                        this.isLoading = true;

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
                                    // Reset all selections when attributes are first loaded
                                    this.sizePickerOptions = [];

                                    const rawAttributes = response.data.data.attributes;

                                    rawAttributes.forEach(attr => {
                                        if (attr.code === 'size') {
                                            // Store all available size options for the picker, start with none selected
                                            this.sizePickerOptions = attr.options;
                                            attr.options = [];
                                        } else if (attr.code === 'color') {
                                            // Start with no colors selected — user adds custom ones
                                            attr.options = [];
                                        }
                                    });

                                    this.attributes = rawAttributes;
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

                        // Remove attributes that have no options left
                        // (color and size no longer get a special pass — user must explicitly keep them)
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

                    // ─── Custom Color Logic ───────────────────────────────────────
                    addCustomColor(attribute) {
                        const name = this.newColorName.trim();
                        const code = this.newColorCode.trim();

                        if (! name) {
                            this.$emitter.emit('add-flash', { type: 'warning', message: 'Please enter a color name' });
                            return;
                        }

                        // Check duplicate by name in current list
                        const exists = attribute.options.find(o => o.name.toLowerCase() === name.toLowerCase());
                        if (exists) {
                            this.$emitter.emit('add-flash', { type: 'warning', message: 'Color "' + name + '" is already added' });
                            return;
                        }

                        // Call API to persist the color option and get real DB ID
                        this.$axios.post("{{ route('admin.api.attributes.color-options') }}", {
                            name: name,
                            swatch_value: code,
                        })
                        .then(response => {
                            const opt = response.data.data;

                            attribute.options.push({
                                id: opt.id,
                                name: opt.name,
                                swatch_value: opt.swatch_value || code || null,
                            });

                            this.superAttributes[attribute.code] = this.superAttributes[attribute.code] || [];
                            this.superAttributes[attribute.code].push(opt.id);

                            this.newColorName = '';
                            this.newColorCode = '#000000';
                        })
                        .catch(() => {
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Failed to save color. Please try again.' });
                        });
                    },

                    // ─── Size Picker Logic ────────────────────────────────────────
                    openSizePicker(attribute) {
                        this.sizePickerAttribute = attribute;
                        this.tempSelectedSizes = [...attribute.options];

                        // If size options are already loaded from the attribute response, use them directly
                        if (this.sizePickerOptions.length) {
                            this.showSizePicker = true;
                            return;
                        }

                        // Fallback: fetch from API
                        this.$axios.get("{{ route('admin.api.attributes.options') }}")
                            .then(response => {
                                this.sizePickerOptions = (response.data.data && response.data.data.size)
                                    ? response.data.data.size
                                    : [];
                            })
                            .catch(() => {
                                this.sizePickerOptions = attribute.options;
                            })
                            .finally(() => {
                                this.showSizePicker = true;
                            });

                        return; // will open after fetch
                    },

                    isSizeSelected(option) {
                        return this.tempSelectedSizes.some(s => s.id == option.id);
                    },

                    toggleSizeOption(option) {
                        const idx = this.tempSelectedSizes.findIndex(s => s.id == option.id);
                        if (idx >= 0) {
                            this.tempSelectedSizes.splice(idx, 1);
                        } else {
                            this.tempSelectedSizes.push(option);
                        }
                    },

                    confirmSizes() {
                        if (this.sizePickerAttribute) {
                            this.sizePickerAttribute.options = [...this.tempSelectedSizes];
                            this.setSuperAttributes();
                        }
                        this.showSizePicker = false;
                    },

                    // ─── Validate that at least one attribute has a selection ─────
                    hasValidSelection() {
                        return this.attributes.length > 0 && this.attributes.some(attr => {
                            const ids = this.superAttributes[attr.code];
                            return ids && ids.length > 0;
                        });
                    },

                    // ─── Submit the configurable product form ─────────────────────
                    submitConfigurable() {
                        if (this.isLoading) return;

                        // Build super_attributes from current selections
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

                        // Gather the existing form params (type, sku, attribute_family_id)
                        // They are stored in hidden inputs inside the form
                        const form = this.$el.querySelector('form');
                        const formData = new FormData(form);
                        const params = {};
                        formData.forEach((value, key) => { params[key] = value; });

                        params.super_attributes = superAttrs;

                        this.$axios.post("{{ route('admin.catalog.products.store') }}", params)
                            .then((response) => {
                                this.isLoading = false;

                                if (response.data.data.redirect_url) {
                                    window.location.href = response.data.data.redirect_url;
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: 'Unexpected response from server.' });
                                }
                            })
                            .catch(error => {
                                this.isLoading = false;
                                const msg = error.response?.data?.message || 'Failed to save product. Please try again.';
                                this.$emitter.emit('add-flash', { type: 'error', message: msg });
                            });
                    },
                }
            })
        </script>

        {{-- Auto-append flash_sale parameter to redirect URL if coming from flash sale page --}}
        @if (request()->get('flash_sale'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const createProductForm = app.component('v-create-product-form');
                    if (createProductForm) {
                        const originalCreate = createProductForm.methods.create;
                        createProductForm.methods.create = function(params, { resetForm, resetField, setErrors }) {
                            params.flash_sale = 1;
                            return originalCreate.call(this, params, { resetForm, resetField, setErrors });
                        };
                    }
                });
            </script>
        @endif
    @endPushOnce
</x-admin::layouts>
