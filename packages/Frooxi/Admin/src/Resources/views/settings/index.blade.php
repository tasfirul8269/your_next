<x-admin::layouts>
    <!-- Page title -->
    <x-slot:title>
        @lang('admin::app.components.layouts.sidebar.settings')
    </x-slot>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
            @lang('admin::app.components.layouts.sidebar.settings')
        </h1>

        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            @lang('admin::app.configuration.index.info')
        </p>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <form
        action="{{ route('admin.settings.page.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <!-- Hidden channel/locale -->
        <input type="hidden" name="channel" value="{{ $channelCode }}">
        <input type="hidden" name="locale" value="{{ $localeCode }}">

        <div class="space-y-6 pb-24">

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section 1 · Store Information               --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <!-- Section header -->
                <div class="mb-5 flex items-start gap-3">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30">
                        <span class="icon-store text-xl text-amber-600 dark:text-amber-400"></span>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Store Information
                        </h2>

                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            Basic details about your store
                        </p>
                    </div>
                </div>

                <!-- Fields grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Store Name -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Store Name
                        </label>

                        <input
                            type="text"
                            name="store_name"
                            value="{{ old('store_name', $settings['store_name']) }}"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="Frooxi"
                        >
                    </div>

                    <!-- Store Email -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Store Email
                        </label>

                        <input
                            type="email"
                            name="store_email"
                            value="{{ old('store_email', $settings['store_email']) }}"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="hello@frooxi.com"
                        >
                    </div>

                    <!-- Store Phone -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Store Phone
                        </label>

                        <input
                            type="text"
                            name="store_phone"
                            value="{{ old('store_phone', $settings['store_phone']) }}"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="+880 1XXXXXXXXX"
                        >
                    </div>

                    <!-- Store Address -->
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Store Address
                        </label>

                        <textarea
                            name="store_address"
                            rows="2"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="Dhaka, Bangladesh"
                        >{{ old('store_address', $settings['store_address']) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section 2 · Catalog Settings                --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <!-- Section header -->
                <div class="mb-5 flex items-start gap-3">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30">
                        <span class="icon-bag text-xl text-amber-600 dark:text-amber-400"></span>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Catalog Settings
                        </h2>

                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            Control how products are displayed in your storefront
                        </p>
                    </div>
                </div>

                <!-- Fields grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Products Per Page -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Products Per Page
                        </label>

                        <select
                            name="products_per_page"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                            @foreach ([12, 24, 36, 48] as $perPage)
                                <option
                                    value="{{ $perPage }}"
                                    {{ (string) old('products_per_page', $settings['products_per_page']) == (string) $perPage ? 'selected' : '' }}
                                >
                                    {{ $perPage }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Default Sort Order -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Default Sort Order
                        </label>

                        <select
                            name="default_sort"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                            @foreach ([
                                'price-asc'  => 'Price Low to High',
                                'price-desc' => 'Price High to Low',
                                'created_at-desc' => 'Newest First',
                                'name-asc'   => 'Name A–Z',
                            ] as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    {{ old('default_sort', $settings['default_sort']) === $value ? 'selected' : '' }}
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Show Out of Stock Products (toggle) -->
                    <div class="sm:col-span-2 pt-1">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Show Out of Stock Products
                                </p>

                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    When enabled, products with zero inventory remain visible in the catalogue
                                </p>
                            </div>

                            <label class="relative inline-flex cursor-pointer items-center">
                                <input
                                    type="hidden"
                                    name="show_out_of_stock"
                                    value="0"
                                >

                                <input
                                    type="checkbox"
                                    name="show_out_of_stock"
                                    value="1"
                                    class="peer sr-only"
                                    {{ old('show_out_of_stock', $settings['show_out_of_stock']) ? 'checked' : '' }}
                                >

                                <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-amber-500 peer-checked:after:translate-x-full peer-checked:after:border-white dark:border-gray-600 dark:bg-gray-700 dark:peer-checked:bg-amber-500"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section 3 · Order Settings                  --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <!-- Section header -->
                <div class="mb-5 flex items-start gap-3">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30">
                        <span class="icon-order text-xl text-amber-600 dark:text-amber-400"></span>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Order Settings
                        </h2>

                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            Configure order processing preferences
                        </p>
                    </div>
                </div>

                <!-- Fields grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Order Number Prefix -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Order Number Prefix
                        </label>

                        <input
                            type="text"
                            name="order_prefix"
                            value="{{ old('order_prefix', $settings['order_prefix']) }}"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="ORD-"
                        >

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Prepended to every order number, e.g. "ORD-0001"
                        </p>
                    </div>

                    <!-- Minimum Order Amount -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Minimum Order Amount (BDT)
                        </label>

                        <input
                            type="number"
                            name="min_order_amount"
                            value="{{ old('min_order_amount', $settings['min_order_amount'] ?? 0) }}"
                            min="0"
                            step="0.01"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="0"
                        >

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Set to 0 to disable minimum order requirement
                        </p>
                    </div>
                </div>
            </div>

        </div><!-- /.space-y-6 -->

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- Sticky Save Bar                                     --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-200 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900/95 sm:px-6">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4">
                <p class="hidden text-sm text-gray-500 dark:text-gray-400 sm:block">
                    Changes are saved across all store channels.
                </p>

                <button
                    type="submit"
                    class="primary-button flex w-full items-center justify-center gap-2 sm:w-auto"
                >
                    <span class="icon-save text-lg"></span>

                    Save Settings
                </button>
            </div>
        </div>
    </form>
</x-admin::layouts>
