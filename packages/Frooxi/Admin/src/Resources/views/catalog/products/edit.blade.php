<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.products.edit.title')
    </x-slot>

    {!! view_render_event('frooxi.admin.catalog.product.edit.before', ['product' => $product]) !!}

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="icon-cancel text-2xl text-red-400"></span>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Please fix the following errors:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-admin::form
        method="PUT"
        :action="request()->get('flash_sale') ? route('admin.storefront.flash_sale.update', $product->id) : route('admin.catalog.products.update', $product->id)"
        enctype="multipart/form-data"
    >
        @if (request()->get('flash_sale'))
            <input type="hidden" name="flash_sale" value="1">
        @endif

        {!! view_render_event('frooxi.admin.catalog.product.edit.actions.before', ['product' => $product]) !!}

        <!-- Page Header -->
        <div class="grid gap-2.5">
            <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
                <div class="grid gap-1.5">
                    <p class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
                        {{ request()->get('flash_sale') ? 'Edit Flash Sale Product' : trans('admin::app.catalog.products.edit.title') }}
                    </p>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Back Button -->
                    <a
                        href="{{ request()->get('flash_sale') ? route('admin.storefront.flash_sale.index') : route('admin.catalog.products.index') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                    >
                        <span class="icon-arrow-left text-lg"></span>
                        @lang('admin::app.account.edit.back-btn')
                    </a>

                    <!-- Preview Button -->
                    @if (
                        $product->status
                        && $product->visible_individually
                        && $product->url_key
                    )
                        <a
                            href="{{ route('shop.product_or_category.index', $product->url_key) }}"
                            class="secondary-button"
                            target="_blank"
                        >
                            @lang('admin::app.catalog.products.edit.preview')
                        </a>
                    @endif

                    <!-- Save Button -->
                    <button class="primary-button">
                        @lang('admin::app.catalog.products.edit.save-btn')
                    </button>
                </div>
            </div>
        </div>

        @php
            $channels = core()->getAllChannels();

            $currentChannel = core()->getRequestedChannel();

            $currentLocale = core()->getRequestedLocale();
        @endphp

        <!-- Channel and Locale Switcher -->
        <div class="mt-7 flex items-center justify-between gap-4 max-md:flex-wrap">
            <div class="flex items-center gap-x-1">
                <!-- Channel Switcher -->
                <x-admin::dropdown :class="$channels->count() <= 1 ? 'hidden' : ''">
                    <!-- Dropdown Toggler -->
                    <x-slot:toggle>
                        <button
                            type="button"
                            class="transparent-button px-1 py-1.5 hover:bg-gray-200 focus:bg-gray-200 dark:text-white dark:hover:bg-gray-800 dark:focus:bg-gray-800"
                        >
                            <span class="icon-store text-2xl"></span>
                            
                            <span v-pre>{{ $currentChannel->name }}</span>

                            <input
                                type="hidden"
                                name="channel"
                                value="{{ $currentChannel->code }}"
                            />

                            <span class="icon-sort-down text-2xl"></span>
                        </button>
                    </x-slot>

                    <!-- Dropdown Content -->
                    <x-slot:content class="!p-0">
                        @foreach ($channels as $channel)
                            <a
                                href="?{{ Arr::query(['channel' => $channel->code, 'locale' => $channel->default_locale?->code ?? $currentLocale->code ]) }}"
                                class="flex cursor-pointer gap-2.5 px-5 py-2 text-base hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                v-pre
                            >
                                {{ $channel->name }}
                            </a>
                        @endforeach
                    </x-slot>
                </x-admin::dropdown>

                <!-- Locale Switcher -->
                <x-admin::dropdown :class="$currentChannel->locales->count() <= 1 ? 'hidden' : ''">
                    <!-- Dropdown Toggler -->
                    <x-slot:toggle>
                        <button
                            type="button"
                            class="transparent-button px-1 py-1.5 hover:bg-gray-200 focus:bg-gray-200 dark:text-white dark:hover:bg-gray-800 dark:focus:bg-gray-800"
                        >
                            <span class="icon-language text-2xl"></span>

                            <span v-pre>{{ $currentLocale->name }}</span>
                            
                            <input
                                type="hidden"
                                name="locale"
                                value="{{ $currentLocale->code }}"
                            />

                            <span class="icon-sort-down text-2xl"></span>
                        </button>
                    </x-slot>

                    <!-- Dropdown Content -->
                    <x-slot:content class="!p-0">
                        @foreach ($currentChannel->locales->sortBy('name') as $locale)
                            <a
                                href="?{{ Arr::query(['channel' => $currentChannel->code, 'locale' => $locale->code]) }}"
                                class="flex gap-2.5 px-5 py-2 text-base cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-950 dark:text-white {{ $locale->code == $currentLocale->code ? 'bg-gray-100 dark:bg-gray-950' : ''}}"
                                v-pre
                            >
                                {{ $locale->name }}
                            </a>
                        @endforeach
                    </x-slot>
                </x-admin::dropdown>
            </div>
        </div>

        {!! view_render_event('frooxi.admin.catalog.product.edit.actions.after', ['product' => $product]) !!}

        <!-- body content -->
        {!! view_render_event('frooxi.admin.catalog.product.edit.form.before', ['product' => $product]) !!}

        <div class="mt-5 flex gap-6 max-xl:flex-wrap">
            @php
                $groupedColumns = $product->attribute_family->attribute_groups->groupBy('column');

                $isSingleColumn = $groupedColumns->count() !== 2;
            @endphp

            @foreach ($groupedColumns as $column => $groups)
                {!! view_render_event("frooxi.admin.catalog.product.edit.form.column_{$column}.before", ['product' => $product]) !!}

                <div class="flex flex-col gap-4 {{ $column == 1 ? 'flex-1 max-xl:flex-auto' : 'w-[360px] max-w-full max-sm:w-full' }}">
                    @foreach ($groups as $group)
                        @php $customAttributes = $product->getEditableAttributes($group); @endphp

                        @if (in_array($group->name, ['Meta Description', 'RMA', 'Tax Categories', 'Brands', 'Shipping']))
                            @continue
                        @endif

                        @if ($group->code === 'price' && $product->type === 'configurable')
                            @continue
                        @endif

                        @if (
                            $group->code === 'inventories' 
                            && (
                                $product->getTypeInstance()->isComposite()
                                || $product->type === 'downloadable'
                            )
                        )
                            @continue
                        @endif

                        @if ($group->code === 'rma')
                            @if (
                                ! in_array($product->type, explode(',', core()->getConfigData('sales.rma.setting.select_allowed_product_type'))) 
                                && (
                                    $product->type != 'simple' 
                                    && empty($product->parent_id)
                                )
                            )
                                @continue
                            @endif
                        @endif

                        @if ($customAttributes->isNotEmpty())
                            {!! view_render_event("frooxi.admin.catalog.product.edit.form.{$group->code}.before", ['product' => $product]) !!}

                            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
                                <p 
                                    class="mb-4 text-lg font-semibold text-gray-900 dark:text-white"
                                    v-pre
                                >
                                    {{ $group->name }}
                                </p>

                                @if ($group->code == 'meta_description')
                                    <x-admin::seo />
                                @endif

                                @foreach ($customAttributes as $attribute)
                                    {{-- Skip price attributes - we have custom price section --}}
                                    @if ($group->code == 'price')
                                        @continue
                                    @endif

                                    {{-- Skip inventory attributes - we have custom inventories section --}}
                                    @if ($group->code == 'inventories')
                                        @continue
                                    @endif

                                    {{-- Skip hidden attributes before rendering anything --}}
                                    @php
                                        $hiddenAttributes = [
                                            'tax_category_id',
                                            'brand',
                                            'color',
                                            'meta_title',
                                            'meta_keywords',
                                            'meta_description',
                                            'allow_rma',
                                            'rma_rule_id',
                                            'length',
                                            'width',
                                            'height',
                                            'weight',
                                            'shipping',
                                            'customer_group_prices',
                                            'manage_stock',
                                        ];

                                        if (request()->get('flash_sale')) {
                                            $hiddenAttributes = array_merge($hiddenAttributes, [
                                                'special_price',
                                                'special_price_from',
                                                'special_price_to',
                                                'cost',
                                            ]);
                                        }
                                    @endphp

                                    @if (in_array($attribute->code, $hiddenAttributes))
                                        @continue
                                    @endif

                                    {!! view_render_event("frooxi.admin.catalog.product.edit.form.{$group->code}.controls.before", ['product' => $product]) !!}

                                    <x-admin::form.control-group class="last:!mb-0">
                                        <x-admin::form.control-group.label>
                                            {!! $attribute->admin_name . ($attribute->is_required ? '<span class="required"></span>' : '') !!}

                                            @if (
                                                $attribute->value_per_channel
                                                && $channels->count() > 1
                                            )
                                                <span 
                                                    class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600"
                                                    v-pre
                                                >
                                                    {{ $currentChannel->name }}
                                                </span>
                                            @endif

                                            @if ($attribute->value_per_locale)
                                                <span
                                                    class="rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[10px] font-semibold leading-normal text-gray-600"
                                                    v-pre
                                                >
                                                    {{ $currentLocale->name }}
                                                </span>
                                            @endif
                                        </x-admin::form.control-group.label>

                                        @include ('admin::catalog.products.edit.controls', [
                                            'attribute' => $attribute,
                                            'product'   => $product,
                                        ])

                                        <x-admin::form.control-group.error :control-name="$attribute->code . (in_array($attribute->type, ['multiselect', 'checkbox']) ? '[]' : '')" />
                                    </x-admin::form.control-group>

                                    {!! view_render_event("frooxi.admin.catalog.product.edit.form.{$group->code}.controls.after", ['product' => $product]) !!}
                                @endforeach

                                @includeWhen($group->code == 'price', 'admin::catalog.products.edit.price.group')

                                @includeWhen($group->code === 'inventories', 'admin::catalog.products.edit.inventories')

                            </div>

                            {!! view_render_event("frooxi.admin.catalog.product.edit.form.{$group->code}.after", ['product' => $product]) !!}
                        @endif
                    @endforeach

                    @if ($column == 1)
                        <!-- Images View Blade File -->
                        @include('admin::catalog.products.edit.images')

                        <!-- Videos View Blade File -->
                        @include('admin::catalog.products.edit.videos')

                        <!-- Product Type View Blade File -->
                        @includeIf('admin::catalog.products.edit.types.' . $product->type)

                        <!-- Related, Cross Sells, Up Sells View Blade File (hidden) -->
                        {{-- @include('admin::catalog.products.edit.links') --}}

                        <!-- Include Product Type Additional Blade Files If Any -->
                        @foreach ($product->getTypeInstance()->getAdditionalViews() as $view)
                            @includeIf($view)
                        @endforeach
                    @elseif (! $isSingleColumn)
                        <!-- Channels View Blade File -->
                        @include('admin::catalog.products.edit.channels')

                        <!-- Categories View Blade File -->
                        @include('admin::catalog.products.edit.categories')
                    @endif
                </div>

                @if ($isSingleColumn && ($column == 1 || $column == 2))
                    <div class="w-[360px] max-w-full max-sm:w-full flex flex-col gap-4">
                        @if ($column == 2) 
                            <!-- Images View Blade File -->
                            @include('admin::catalog.products.edit.images')

                            <!-- Videos View Blade File -->
                            @include('admin::catalog.products.edit.videos')

                            <!-- Product Type View Blade File -->
                            @includeIf('admin::catalog.products.edit.types.' . $product->type)

                            <!-- Related, Cross Sells, Up Sells View Blade File (hidden) -->
                            {{-- @include('admin::catalog.products.edit.links') --}}

                            <!-- Include Product Type Additional Blade Files If Any -->
                            @foreach ($product->getTypeInstance()->getAdditionalViews() as $view)
                                @includeIf($view)
                            @endforeach
                        @endif

                        <!-- Channels View Blade File -->
                        @include('admin::catalog.products.edit.channels')

                        <!-- Categories View Blade File -->
                        @include('admin::catalog.products.edit.categories')
                    </div>
                @endif

                {!! view_render_event("frooxi.admin.catalog.product.edit.form.column_{$column}.after", ['product' => $product]) !!}
            @endforeach
        </div>

        {!! view_render_event('frooxi.admin.catalog.product.edit.form.after', ['product' => $product]) !!}

    </x-admin::form>

    {!! view_render_event('frooxi.admin.catalog.product.edit.after', ['product' => $product]) !!}

</x-admin::layouts>
