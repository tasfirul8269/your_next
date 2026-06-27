{!! view_render_event('frooxi.shop.components.layouts.header.desktop.bottom.before') !!}

<div class="w-full px-16 relative" id="main-header" style="padding-top: 16px; padding-bottom: 24px;">
    <div class="w-full" style="display:grid;grid-template-columns:1fr auto 1fr;align-items:center;">
        <!-- Logo - Left -->
        <div class="flex items-center flex-shrink-0" style="justify-self:start;">
            <a href="{{ route('shop.home.index') }}">
                <img
                    src="{{ request()->routeIs('shop.home.index') ? asset('themes/shop/logo.png') : asset('themes/shop/logo_black.png') }}"
                    width="70"
                    height="auto"
                    alt="{{ config('app.name') }}"
                    class="logo-image"
                >
            </a>
        </div>

        <!-- Categories - Center -->
        <div class="flex justify-center" style="justify-self:center;">
            <v-desktop-category></v-desktop-category>
        </div>

        <!-- Icons - Right -->
        <div class="flex items-center justify-end gap-8 flex-shrink-0" style="justify-self:end;">
            <!-- Search -->
            <v-search-toggle></v-search-toggle>

            <!-- Account -->
            <x-shop::dropdown position="bottom-right">
                <x-slot:toggle>
                    <span class="icon-users text-2xl hover:opacity-70 transition-colors cursor-pointer header-icon" style="color: {{ request()->routeIs('shop.home.index') ? '#ffffff' : '#1a1a1a' }} !important;"></span>
                </x-slot>

                <!-- Guest Dropdown -->
                @guest('customer')
                    <x-slot:content>
                        <div class="grid gap-2.5">
                            <p class="text-xl font-dmserif text-black">
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome-guest')
                            </p>

                            <p class="text-sm text-gray-500">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>

                        <p class="w-full mt-3 border border-zinc-200"></p>

                        <div class="flex gap-4 mt-6">
                            <a
                                href="{{ route('shop.customer.session.create') }}"
                                class="block w-max m-0 mx-auto bg-[#e30612] rounded-2xl px-7 py-3 text-center text-base font-medium text-white transition-all hover:opacity-80"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-in')
                            </a>

                            <a
                                href="{{ route('shop.customers.register.index') }}"
                                class="block w-max m-0 mx-auto bg-white border-2 border-[#e30612] rounded-2xl px-7 py-2.5 text-center text-base font-medium text-[#e30612] transition-all hover:bg-[#e30612] hover:text-white"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-up')
                            </a>
                        </div>
                    </x-slot:content>
                @endguest

                <!-- Customers Dropdown -->
                @auth('customer')
                    <x-slot:content class="!p-0">
                        <div class="grid gap-2.5 p-5 pb-0">
                            <p class="text-xl font-dmserif text-black" v-pre>
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome')’
                                {{ auth()->guard('customer')->user()->first_name }}
                            </p>

                            <p class="text-sm text-gray-500">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>

                        <p class="w-full mt-3 border border-zinc-200"></p>

                        <div class="mt-2.5 grid gap-1 pb-2.5">
                            <a
                                class="px-5 py-2 text-base text-black cursor-pointer transition-colors hover:bg-gray-100"
                                href="{{ route('shop.customers.account.profile.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.profile')
                            </a>

                            <a
                                class="px-5 py-2 text-base text-black cursor-pointer transition-colors hover:bg-gray-100"
                                href="{{ route('shop.customers.account.orders.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.orders')
                            </a>

                            @if ((bool) core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                <a
                                    class="px-5 py-2 text-base text-black cursor-pointer transition-colors hover:bg-gray-100"
                                    href="{{ route('shop.customers.account.wishlist.index') }}"
                                >
                                    @lang('shop::app.components.layouts.header.desktop.bottom.wishlist')
                                </a>
                            @endif

                            <x-shop::form
                                method="DELETE"
                                action="{{ route('shop.customer.session.destroy') }}"
                                id="customerLogout"
                            />

                            <a
                                class="px-5 py-2 text-base text-black cursor-pointer transition-colors hover:bg-gray-100"
                                href="{{ route('shop.customer.session.destroy') }}"
                                onclick="event.preventDefault(); document.getElementById('customerLogout').submit();"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.logout')
                            </a>
                        </div>
                    </x-slot:content>
                @endauth
            </x-shop::dropdown>

            <!-- Cart -->
            @if(core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                <div class="mini-cart-container">
                    @include('shop::checkout.cart.mini-cart', ['iconClass' => 'hover:opacity-70 transition-colors cursor-pointer text-2xl header-icon', 'iconColor' => request()->routeIs('shop.home.index') ? '#ffffff' : '#1a1a1a'])
                </div>
            @endif
        </div>
    </div>
</div>

@pushOnce('styles')
    <style>
        #main-header-container {
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            padding: 0 !important;
        }

        #main-header-container.is-homepage {
            left: 0 !important;
            right: 0 !important;
        }

        #main-header-container.scrolled {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
        }

        #main-header-container.is-homepage .logo-image {
            filter: brightness(0) invert(1) !important;
        }

        #main-header-container:not(.is-homepage) .logo-image {
            filter: none !important;
        }

        .nav-link {
            font-size: 12px !important;
            font-weight: 400 !important;
            text-transform: uppercase !important;
            letter-spacing: 1.2px !important;
            transition: all 0.2s ease !important;
            white-space: nowrap !important;
            text-decoration: none !important;
            display: inline-block !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        .nav-link:hover {
            opacity: 0.8 !important;
            transform: translateY(-1px);
        }

        #main-header-container.is-homepage .nav-link {
            color: #FFFFFF !important;
        }

        #main-header-container:not(.is-homepage) .nav-link {
            color: #1A1A1A !important;
        }

        /* Homepage: white icons */
        #main-header-container.is-homepage .icon-cart,
        #main-header-container.is-homepage .icon-search,
        #main-header-container.is-homepage .icon-users,
        #main-header-container.is-homepage .icon-cancel,
        #main-header-container.is-homepage .header-icon,
        #main-header-container.is-homepage [class^="icon-"],
        #main-header-container.is-homepage [class*=" icon-"],
        #main-header-container.is-homepage .mini-cart-container .icon-cart {
            color: #FFFFFF !important;
            fill: #FFFFFF !important;
        }

        /* Non-homepage: black icons */
        #main-header-container:not(.is-homepage) .icon-cart,
        #main-header-container:not(.is-homepage) .icon-search,
        #main-header-container:not(.is-homepage) .icon-users,
        #main-header-container:not(.is-homepage) .icon-cancel,
        #main-header-container:not(.is-homepage) .header-icon,
        #main-header-container:not(.is-homepage) [class^="icon-"],
        #main-header-container:not(.is-homepage) [class*=" icon-"],
        #main-header-container:not(.is-homepage) .mini-cart-container .icon-cart {
            color: #1a1a1a !important;
            fill: #1a1a1a !important;
        }

        /* Category link colors */
        #main-header-container.is-homepage .nav-category-link {
            color: #ffffff !important;
        }

        #main-header-container:not(.is-homepage) .nav-category-link {
            color: #1a1a1a !important;
        }

        #main-header-container:not(.is-homepage) .nav-category-link:hover {
            opacity: 0.7;
        }

        #main-header-container.is-homepage .nav-category-link-indicator {
            background-color: #ffffff !important;
        }

        #main-header-container:not(.is-homepage) .nav-category-link-indicator {
            background-color: #1a1a1a !important;
        }

        /* Mini Cart Badge */
    </style>
@endPushOnce

@pushOnce('scripts')
    <!-- Desktop Category Component -->
    <script type="text/x-template" id="v-desktop-category-template">
        <nav
            class="relative"
            style="display: block;"
            @mouseenter="cancelClose"
            @mouseleave="scheduleClose"
        >
            <div class="flex items-center justify-center gap-10">
                <!-- Flash Sale Link -->
                <div class="py-4">
                    <a
                        href="{{ route('shop.flash-sale.index') }}"
                        class="nav-category-link relative inline-flex whitespace-nowrap pb-1 text-sm font-medium uppercase transition-all duration-200"
                        style="color: {{ request()->routeIs('shop.home.index') ? '#ffffff' : '#1a1a1a' }} !important; letter-spacing: 0.16em; text-decoration: none;"
                    >
                        Flash Sale

                        <span
                            class="nav-category-link-indicator absolute inset-x-0 -bottom-1 h-0.5 rounded-full transition-opacity duration-200"
                            style="{{ request()->routeIs('shop.flash-sale.index') ? 'opacity: 100;' : 'opacity: 0;' }}"
                        ></span>
                    </a>
                </div>

                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="py-4"
                    @mouseenter="openMenu(category)"
                >
                    <a
                        :href="category.url"
                        class="nav-category-link relative inline-flex whitespace-nowrap pb-1 text-sm font-medium uppercase transition-all duration-200"
                        style="color: {{ request()->routeIs('shop.home.index') ? '#ffffff' : '#1a1a1a' }} !important; letter-spacing: 0.16em; text-decoration: none;"
                        :style="{ opacity: hoveredCategoryId && hoveredCategoryId !== category.id ? '0.7' : '1' }"
                    >
                        @{{ category.name }}

                        <span
                            class="nav-category-link-indicator absolute inset-x-0 -bottom-1 h-0.5 rounded-full transition-opacity duration-200"
                            :class="hoveredCategoryId === category.id ? 'opacity-100' : 'opacity-0'"
                        ></span>
                    </a>
                </div>
            </div>

            <transition name="mega-menu-fade">
                <div
                    v-if="shouldShowDropdown"
                    class="fixed z-[3000]"
                    :style="{ top: megaMenuTop + 'px', left: '50%', transform: 'translateX(-50%)' }"
                    @mouseenter="cancelClose"
                    @mouseleave="scheduleClose"
                >
                    <div class="mt-4 bg-white" style="padding: 24px 32px; border-radius: 5px; box-shadow: 0 18px 48px rgba(15,23,42,0.12);">
                        <div
                            v-if="showColumnLayout"
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px 40px;"
                        >
                            <div
                                v-for="child in activeCategory.children"
                                :key="child.id"
                            >
                                <a
                                    :href="child.url"
                                    class="block font-semibold transition-colors duration-200"
                                    style="font-size: 15px; color: #333333; text-decoration: none;"
                                    onmouseover="this.style.color='#000000'"
                                    onmouseout="this.style.color='#333333'"
                                >
                                    @{{ child.name }}
                                </a>

                                <div class="mt-3 flex flex-col gap-1">
                                    <a
                                        :href="child.url"
                                        class="text-sm transition-colors duration-200"
                                        style="line-height: 2rem; color: #555555; text-decoration: none;"
                                        onmouseover="this.style.color='#000000'"
                                        onmouseout="this.style.color='#555555'"
                                    >
                                        All
                                    </a>

                                    <a
                                        v-for="grandchild in child.children || []"
                                        :key="grandchild.id"
                                        :href="grandchild.url"
                                        class="text-sm transition-colors duration-200"
                                        style="line-height: 2rem; color: #555555; text-decoration: none;"
                                        onmouseover="this.style.color='#000000'"
                                        onmouseout="this.style.color='#555555'"
                                    >
                                        @{{ grandchild.name }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px 40px; min-width: 320px;"
                        >
                            <a
                                v-for="child in activeCategory.children"
                                :key="child.id"
                                :href="child.url"
                                style="display: block; font-size: 14px; line-height: 2.25rem; color: #555555; text-decoration: none; white-space: nowrap;"
                                onmouseover="this.style.color='#000000'"
                                onmouseout="this.style.color='#555555'"
                            >
                                @{{ child.name }}
                            </a>
                        </div>
                    </div>
                </div>
            </transition>
        </nav>
    </script>

    <script type="module">
        app.component("v-desktop-category", {
            template: '#v-desktop-category-template',

            data() {
                return {
                    categories: [],
                    isLoading: true,
                    hoveredCategoryId: null,
                    closeTimer: null,
                    megaMenuTop: 0,
                };
            },

            computed: {
                activeCategory() {
                    if (! this.hoveredCategoryId) {
                        return null;
                    }

                    return this.categories.find((category) => category.id === this.hoveredCategoryId) || null;
                },

                shouldShowDropdown() {
                    return this.hasChildren(this.activeCategory);
                },

                showColumnLayout() {
                    return this.hasGrandchildren(this.activeCategory);
                },
            },

            mounted() {
                this.getCategories();
                this.updateMegaMenuTop();
                window.addEventListener('scroll', this.updateMegaMenuTop);
                window.addEventListener('resize', this.updateMegaMenuTop);
            },

            beforeUnmount() {
                window.removeEventListener('scroll', this.updateMegaMenuTop);
                window.removeEventListener('resize', this.updateMegaMenuTop);

                if (this.closeTimer) {
                    clearTimeout(this.closeTimer);
                }
            },

            methods: {
                async getCategories() {
                    this.isLoading = true;

                    try {
                        const response = await this.$axios.get("{{ route('shop.api.categories.tree') }}", {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        this.categories = response.data && Array.isArray(response.data.data)
                            ? response.data.data
                            : [];
                    } catch (error) {
                        console.error('Failed to load categories:', error);
                        this.categories = [];
                    } finally {
                        this.isLoading = false;
                    }
                },

                hasChildren(category) {
                    return Boolean(category?.children?.length);
                },

                hasGrandchildren(category) {
                    if (! this.hasChildren(category)) {
                        return false;
                    }

                    return category.children.some((child) => child.children && child.children.length);
                },

                updateMegaMenuTop() {
                    const header = document.getElementById('main-header-container');

                    if (header) {
                        this.megaMenuTop = header.getBoundingClientRect().bottom;
                    } else {
                        this.megaMenuTop = 80;
                    }
                },

                openMenu(category) {
                    this.cancelClose();

                    if (! this.hasChildren(category)) {
                        this.hoveredCategoryId = null;
                        return;
                    }

                    this.hoveredCategoryId = category.id;
                    this.updateMegaMenuTop();
                },

                scheduleClose() {
                    this.cancelClose();

                    this.closeTimer = setTimeout(() => {
                        this.hoveredCategoryId = null;
                    }, 150);
                },

                cancelClose() {
                    if (this.closeTimer) {
                        clearTimeout(this.closeTimer);
                        this.closeTimer = null;
                    }
                },
            },
        });
    </script>

    <style>
        .mega-menu-fade-enter-active,
        .mega-menu-fade-leave-active {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .mega-menu-fade-enter-from,
        .mega-menu-fade-leave-to {
            opacity: 0;
            transform: translate(-50%, -8px);
        }

        .mega-menu-fade-enter-to,
        .mega-menu-fade-leave-from {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    </style>

    <script type="text/x-template" id="v-search-toggle-template">
        <div>
            <!-- Search Icon Button -->
            <button
                @click="openSearchDrawer"
                class="hover:opacity-70 transition-colors p-1"
                aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.search')"
            >
                <span class="icon-search text-2xl header-icon" style="color: {{ request()->routeIs('shop.home.index') ? '#ffffff' : '#1a1a1a' }} !important;"></span>
            </button>

            <!-- Search Drawer Overlay -->
            <transition name="drawer-fade">
                <div
                    v-if="isOpen"
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9990]"
                    @click="closeSearchDrawer"
                ></div>
            </transition>

            <!-- Search Drawer -->
            <transition name="drawer-slide">
                <div
                    v-if="isOpen"
                    class="fixed top-0 right-0 bottom-0 w-full max-w-md bg-white z-[9995] flex flex-col shadow-2xl"
                >
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="icon-search text-xl text-gray-900"></span>
                            <span class="font-medium text-base text-gray-900">Search Products</span>
                        </div>
                        <button
                            @click="closeSearchDrawer"
                            class="text-gray-400 hover:text-gray-900 transition-colors p-1"
                        >
                            <span class="icon-cancel text-xl"></span>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="px-6 py-4 border-b border-gray-100">
                        <form @submit.prevent="handleSearch" class="relative">
                            <input
                                ref="searchInput"
                                v-model="searchQuery"
                                @input="debounceSearch"
                                type="text"
                                class="w-full py-3 pl-12 pr-10 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-xl focus:border-gray-900 focus:outline-none focus:bg-white transition-all"
                                placeholder="Search for products..."
                                minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                                maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                            />
                            <div class="icon-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></div>
                            
                            <button
                                v-if="searchQuery"
                                type="button"
                                @click="clearSearch"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors"
                            >
                                <span class="icon-cancel text-lg"></span>
                            </button>
                        </form>
                    </div>

                    <!-- Search Results -->
                    <div class="flex-1 overflow-y-auto">
                        <!-- Loading State -->
                        <div v-if="isLoading" class="px-6 py-8">
                            <div class="animate-pulse space-y-4">
                                <div v-for="i in 4" :key="i" class="flex gap-4">
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Suggestions -->
                        <div v-else-if="products.length > 0" class="py-4">
                            <div class="px-6 pb-3">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">
                                    @{{ products.length }} product@{{ products.length !== 1 ? 's' : '' }} found
                                </p>
                            </div>
                            
                            <a
                                v-for="product in products"
                                :key="product.id"
                                :href="product.url"
                                class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50 transition-colors"
                                @click="closeSearchDrawer"
                            >
                                <!-- Product Image -->
                                <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                    <img
                                        :src="product.image || '{{ asset('themes/shop/default/build/assets/small-product-placeholder-CMT26fOX.webp') }}'"
                                        :alt="product.name"
                                        class="w-full h-full object-cover"
                                    />
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                        @{{ product.name }}
                                    </h3>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-base font-semibold text-gray-900" v-if="product.price">
                                            @{{ product.price }}
                                        </span>
                                        <span v-if="product.old_price" class="text-xs text-gray-400 line-through">
                                            @{{ product.old_price }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <span class="icon-arrow-right text-gray-400 flex-shrink-0"></span>
                            </a>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="searchQuery && !isLoading" class="px-6 py-16 text-center">
                            <div class="icon-search text-6xl text-gray-300 mb-4"></div>
                            <p class="text-base text-gray-500">No products found</p>
                            <p class="text-sm text-gray-400 mt-2">Try searching with different keywords</p>
                        </div>

                        <!-- Initial State -->
                        <div v-else class="px-6 py-16 text-center">
                            <div class="icon-search text-6xl text-gray-300 mb-4"></div>
                            <p class="text-base text-gray-500">Start typing to search products</p>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component("v-search-toggle", {
            template: '#v-search-toggle-template',
            data() {
                return {
                    isOpen: false,
                    searchQuery: '',
                    products: [],
                    isLoading: false,
                    debounceTimer: null,
                    abortController: null,
                };
            },

            mounted() {
                this._openSearchHandler = () => {
                    // Only respond if this component's root element is actually visible
                    // (offsetParent === null when inside a display:none container)
                    if (this.$el && this.$el.offsetParent !== null) {
                        this.openSearchDrawer();
                    }
                };
                window.addEventListener('open-search-drawer', this._openSearchHandler);

                this._escapeHandler = (e) => {
                    if (e.key === 'Escape' && this.isOpen) {
                        this.closeSearchDrawer();
                    }
                };
                document.addEventListener('keydown', this._escapeHandler);
            },

            beforeUnmount() {
                window.removeEventListener('open-search-drawer', this._openSearchHandler);
                document.removeEventListener('keydown', this._escapeHandler);
                if (this.abortController) {
                    this.abortController.abort();
                }
            },

            methods: {
                openSearchDrawer() {
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                    this.$nextTick(() => {
                        this.$refs.searchInput?.focus();
                    });
                },

                closeSearchDrawer() {
                    this.isOpen = false;
                    document.body.style.overflow = 'auto';
                    this.searchQuery = '';
                    this.products = [];
                    if (this.abortController) {
                        this.abortController.abort();
                    }
                },

                clearSearch() {
                    this.searchQuery = '';
                    this.products = [];
                    this.isLoading = false;
                    clearTimeout(this.debounceTimer);
                    if (this.abortController) {
                        this.abortController.abort();
                    }
                },

                debounceSearch() {
                    clearTimeout(this.debounceTimer);

                    if (this.searchQuery.length < {{ core()->getConfigData('catalog.products.search.min_query_length') ?? 2 }}) {
                        this.products = [];
                        this.isLoading = false;
                        return;
                    }

                    this.isLoading = true;

                    this.debounceTimer = setTimeout(() => {
                        this.searchProducts();
                    }, 150);
                },

                async searchProducts() {
                    const query = this.searchQuery.trim();

                    if (!query) {
                        this.products = [];
                        this.isLoading = false;
                        return;
                    }

                    // Cancel any previous in-flight request
                    if (this.abortController) {
                        this.abortController.abort();
                    }

                    const controller = new AbortController();
                    this.abortController = controller;

                    this.isLoading = true;
                    try {
                        const response = await fetch(
                            `{{ route('shop.search.suggestions') }}?query=${encodeURIComponent(query)}`,
                            { signal: controller.signal }
                        );
                        const result = await response.json();

                        // Only update if this is still the active request
                        if (this.abortController === controller) {
                            this.products = result.data || [];
                            this.isLoading = false;
                        }
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error('Search failed:', error);
                            if (this.abortController === controller) {
                                this.products = [];
                                this.isLoading = false;
                            }
                        }
                    }
                },

                handleSearch() {
                    if (this.searchQuery.length >= {{ core()->getConfigData('catalog.products.search.min_query_length') ?? 2 }}) {
                        window.location.href = `{{ route('shop.search.index') }}?query=${encodeURIComponent(this.searchQuery)}`;
                    }
                },
            },
        });
    </script>

    <style>
        /* Search Drawer Transitions */
        .drawer-fade-enter-active,
        .drawer-fade-leave-active {
            transition: opacity 0.3s ease;
        }
        .drawer-fade-enter-from,
        .drawer-fade-leave-to {
            opacity: 0;
        }

        .drawer-slide-enter-active,
        .drawer-slide-leave-active {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .drawer-slide-enter-from,
        .drawer-slide-leave-to {
            transform: translateX(100%);
        }
    </style>

@endPushOnce

{!! view_render_event('frooxi.shop.components.layouts.header.desktop.bottom.after') !!}
