<x-admin::layouts>
    <x-slot:title>
        Payment Methods
    </x-slot>

    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
                Payment Methods
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Configure SSLCommerz and bKash payment gateway credentials
            </p>
        </div>
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
        action="{{ route('admin.sales.payment_methods.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <input type="hidden" name="channel" value="{{ $channelCode }}">
        <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

        <div class="space-y-6 pb-24">

            {{-- ════════════════════════════════ --}}
            {{-- SSLCommerz                        --}}
            {{-- ════════════════════════════════ --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <!-- Header -->
                <div class="mb-5 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30">
                            <span class="icon-payment text-xl text-blue-600 dark:text-blue-400"></span>
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                SSLCommerz
                            </h2>

                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                Bangladeshi payment gateway — cards, bKash, Nagad, Rocket, internet banking
                            </p>
                        </div>
                    </div>

                    <!-- Enable toggle -->
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="sslcommerz[active]" value="0">

                        <input
                            type="checkbox"
                            name="sslcommerz[active]"
                            value="1"
                            class="peer sr-only"
                            {{ $methods['sslcommerz']['active'] ? 'checked' : '' }}
                        >

                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-amber-500 peer-checked:after:translate-x-full peer-checked:after:border-white dark:border-gray-600 dark:bg-gray-700"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Display Title -->
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Display Title
                        </label>

                        <input
                            type="text"
                            name="sslcommerz[title]"
                            value="{{ old('sslcommerz.title', $methods['sslcommerz']['title']) }}"
                            placeholder="SSLCommerz"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- Store ID -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Store ID
                        </label>

                        <input
                            type="text"
                            name="sslcommerz[store_id]"
                            value="{{ old('sslcommerz.store_id', $methods['sslcommerz']['store_id']) }}"
                            placeholder="your_store_id"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- Store Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Store Password
                        </label>

                        <input
                            type="password"
                            name="sslcommerz[store_password]"
                            value="{{ old('sslcommerz.store_password', $methods['sslcommerz']['store_password']) }}"
                            placeholder="••••••••"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- Sandbox toggle -->
                    <div class="sm:col-span-2 pt-1">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Sandbox Mode
                                </p>

                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    Enable for testing. Disable for live payments.
                                </p>
                            </div>

                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="sslcommerz[sandbox]" value="0">

                                <input
                                    type="checkbox"
                                    name="sslcommerz[sandbox]"
                                    value="1"
                                    class="peer sr-only"
                                    {{ $methods['sslcommerz']['sandbox'] ? 'checked' : '' }}
                                >

                                <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-amber-500 peer-checked:after:translate-x-full peer-checked:after:border-white dark:border-gray-600 dark:bg-gray-700"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════ --}}
            {{-- bKash                             --}}
            {{-- ════════════════════════════════ --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <!-- Header -->
                <div class="mb-5 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-pink-50 dark:bg-pink-900/30">
                            <span class="icon-payment text-xl text-pink-600 dark:text-pink-400"></span>
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                bKash
                            </h2>

                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                bKash Tokenized Payment Gateway — mobile financial service
                            </p>
                        </div>
                    </div>

                    <!-- Enable toggle -->
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="bkash[active]" value="0">

                        <input
                            type="checkbox"
                            name="bkash[active]"
                            value="1"
                            class="peer sr-only"
                            {{ $methods['bkash']['active'] ? 'checked' : '' }}
                        >

                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-amber-500 peer-checked:after:translate-x-full peer-checked:after:border-white dark:border-gray-600 dark:bg-gray-700"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Display Title -->
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Display Title
                        </label>

                        <input
                            type="text"
                            name="bkash[title]"
                            value="{{ old('bkash.title', $methods['bkash']['title']) }}"
                            placeholder="bKash"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Username
                        </label>

                        <input
                            type="text"
                            name="bkash[username]"
                            value="{{ old('bkash.username', $methods['bkash']['username']) }}"
                            placeholder="sandboxTokenizedUser02"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password
                        </label>

                        <input
                            type="password"
                            name="bkash[password]"
                            value="{{ old('bkash.password', $methods['bkash']['password']) }}"
                            placeholder="••••••••"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- App Key -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            App Key
                        </label>

                        <input
                            type="text"
                            name="bkash[app_key]"
                            value="{{ old('bkash.app_key', $methods['bkash']['app_key']) }}"
                            placeholder="4f6o0cjiki2rfm34kfdadl1eqq"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- App Secret -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            App Secret
                        </label>

                        <input
                            type="password"
                            name="bkash[app_secret]"
                            value="{{ old('bkash.app_secret', $methods['bkash']['app_secret']) }}"
                            placeholder="••••••••"
                            class="form-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors placeholder:text-gray-400 hover:border-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <!-- Sandbox toggle -->
                    <div class="sm:col-span-2 pt-1">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Sandbox Mode
                                </p>

                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    Uses <code class="rounded bg-gray-100 px-1 dark:bg-gray-800">https://tokenized.sandbox.bka.sh</code> when enabled.
                                </p>
                            </div>

                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="bkash[sandbox]" value="0">

                                <input
                                    type="checkbox"
                                    name="bkash[sandbox]"
                                    value="1"
                                    class="peer sr-only"
                                    {{ $methods['bkash']['sandbox'] ? 'checked' : '' }}
                                >

                                <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-amber-500 peer-checked:after:translate-x-full peer-checked:after:border-white dark:border-gray-600 dark:bg-gray-700"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.space-y-6 -->

        {{-- Sticky Save Bar --}}
        <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-200 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900/95 sm:px-6">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4">
                <p class="hidden text-sm text-gray-500 dark:text-gray-400 sm:block">
                    Changes apply to the <strong>{{ $channelCode }}</strong> channel.
                </p>

                <button
                    type="submit"
                    class="primary-button flex w-full items-center justify-center gap-2 sm:w-auto"
                >
                    <span class="icon-save text-lg"></span>
                    Save Payment Settings
                </button>
            </div>
        </div>
    </form>
</x-admin::layouts>
