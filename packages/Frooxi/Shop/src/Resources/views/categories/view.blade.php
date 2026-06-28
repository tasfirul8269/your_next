<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="description"
        content="{{ trim($category->meta_description) != '' ? $category->meta_description : \Illuminate\Support\Str::limit(strip_tags($category->description), 120, '') }}"
    />

    <meta
        name="keywords"
        content="{{ $category->meta_keywords }}"
    />

    @if (core()->getConfigData('catalog.rich_snippets.categories.enable'))
        <script type="application/ld+json">
            {!! app('Frooxi\Product\Helpers\SEO')->getCategoryJsonLd($category) !!}
        </script>
    @endif
@endpush

<x-shop::layouts>
    <x-slot:title>
        {{ trim($category->meta_title) != '' ? $category->meta_title : $category->name }}
    </x-slot>

    <style>
        @keyframes shimmerPulse {
            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .4;
            }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        #category-page-product-grid::-webkit-scrollbar,
        #category-page-product-skeleton-grid::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div style="width:100%;background:#fff;padding:36px 16px 72px;">
        <div style="max-width:1400px;margin:0 auto;">
            <div style="padding:12px 0 36px;text-align:center;">
                <h1
                    style="margin:0;font-family:Montserrat,sans-serif;font-size:clamp(26px,4vw,42px);font-weight:500;line-height:1.05;letter-spacing:.08em;text-transform:uppercase;color:#111;"
                >
                    {{ $category->name }}
                </h1>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
                <button
                    id="category-page-filter-btn"
                    onclick="pcOpenDrawer()"
                    style="display:inline-flex;align-items:center;gap:8px;background:none;border:1.5px solid #d1d5db;border-radius:9999px;padding:9px 20px;font-family:Montserrat,sans-serif;font-size:13px;font-weight:500;color:#111;cursor:pointer;"
                    onmouseover="this.style.borderColor='#111'"
                    onmouseout="this.style.borderColor='#d1d5db'"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                        <line x1="11" y1="18" x2="13" y2="18"></line>
                    </svg>
                    Filter
                    <span id="filter-count-badge" style="display:none;background:#D63044;color:#fff;font-size:10px;font-weight:600;border-radius:9999px;padding:1px 7px;line-height:1.6;"></span>
                </button>

                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-family:Montserrat,sans-serif;font-size:13px;color:#9ca3af;">Sort by:</span>

                    <div style="position:relative;display:inline-block;">
                        <select
                            id="sort-select"
                            onchange="pcApplySort(this.value)"
                            style="font-family:Montserrat,sans-serif;font-size:13px;color:#111;border:1.5px solid #e5e7eb;border-radius:9999px;padding:9px 40px 9px 18px;background:#fff;appearance:none;-webkit-appearance:none;cursor:pointer;outline:none;"
                        >
                            <option value="">Featured</option>
                            <option value="created_at-desc">Newest First</option>
                            <option value="created_at-asc">Oldest First</option>
                            <option value="name-asc">Name: A → Z</option>
                            <option value="name-desc">Name: Z → A</option>
                            <option value="price-asc">Price: Low → High</option>
                            <option value="price-desc">Price: High → Low</option>
                        </select>

                        <div style="position:absolute;right:14px;top:50%;transform:translateY(-50%);pointer-events:none;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div id="product-loading" style="display:block;">
                <div id="category-page-product-skeleton-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px;">
                    @for ($i = 0; $i < 8; $i++)
                        <div class="skeleton-card">
                            <div style="width:100%;aspect-ratio:2/3;border-radius:8px;background:#e5e7eb;animation:shimmerPulse 1.5s infinite;"></div>
                            <div style="margin-top:12px;height:14px;width:70%;border-radius:4px;background:#e5e7eb;animation:shimmerPulse 1.5s infinite;"></div>
                            <div style="margin-top:7px;height:13px;width:40%;border-radius:4px;background:#e5e7eb;animation:shimmerPulse 1.5s infinite;"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div id="product-empty" style="display:none;text-align:center;padding:64px 16px;color:#9ca3af;font-family:Montserrat,sans-serif;font-size:15px;">
                @lang('shop::app.categories.view.empty')
            </div>

            <div id="category-page-product-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px;"></div>

            <div id="infinite-scroll-sentinel" style="height:1px;"></div>
            <div id="infinite-scroll-spinner" style="display:none;justify-content:center;padding:24px 0;">
                <svg style="width:32px;height:32px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#D63044" stroke-width="2">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                </svg>
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (window.innerWidth <= 768) {
                var grid = document.getElementById('category-page-product-grid');
                var skeleton = document.getElementById('category-page-product-skeleton-grid');

                if (grid) {
                    grid.style.gridTemplateColumns = 'repeat(2,1fr)';
                    grid.style.gap = '10px';
                }

                if (skeleton) {
                    skeleton.style.gridTemplateColumns = 'repeat(2,1fr)';
                    skeleton.style.gap = '10px';
                }
            }
        })();
    </script>

    <div
        id="pc-drawer-overlay"
        onclick="pcCloseDrawer()"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9990;backdrop-filter:blur(2px);"
    ></div>

    <div
        id="pc-filter-drawer"
        style="position:fixed;top:0;left:0;bottom:0;width:360px;max-width:95vw;background:#fff;z-index:9995;transform:translateX(-100%);transition:transform .38s cubic-bezier(.19,1,.22,1);display:flex;flex-direction:column;box-shadow:8px 0 40px rgba(0,0,0,.12);"
    >
        <div style="display:flex;align-items:center;justify-content:space-between;padding:24px 28px 20px;border-bottom:1px solid #f3f4f6;">
            <div style="display:flex;align-items:center;gap:10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="6" x2="20" y2="6"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                    <line x1="11" y1="18" x2="13" y2="18"></line>
                </svg>
                <span style="font-family:Montserrat,sans-serif;font-size:14px;font-weight:600;color:#111;letter-spacing:.2px;">Filters</span>
            </div>

            <div style="display:flex;align-items:center;gap:16px;">
                <button
                    onclick="pcClearFilters()"
                    style="font-family:Montserrat,sans-serif;font-size:12px;color:#9ca3af;background:none;border:none;cursor:pointer;letter-spacing:.2px;"
                    onmouseover="this.style.color='#111'"
                    onmouseout="this.style.color='#9ca3af'"
                >
                    Clear all
                </button>

                <button
                    onclick="pcCloseDrawer()"
                    style="background:none;border:none;cursor:pointer;color:#9ca3af;display:flex;padding:2px;"
                    onmouseover="this.style.color='#111'"
                    onmouseout="this.style.color='#9ca3af'"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div style="flex:1;overflow-y:auto;padding:0;">
            <div id="pc-cat-filter-section" style="border-bottom:1px solid #f5f5f5;">
                <div
                    onclick="pcToggleSection('pc-cat-list','pc-cat-arrow')"
                    style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:20px 28px;"
                >
                    <span id="pc-cat-filter-title" style="font-family:Montserrat,sans-serif;font-size:11px;font-weight:700;color:#111;letter-spacing:1.2px;text-transform:uppercase;">Category</span>
                    <svg id="pc-cat-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s;flex-shrink:0;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div id="pc-cat-list" style="padding:0 28px 20px;display:flex;flex-direction:column;gap:2px;"></div>
            </div>

            <div style="border-bottom:1px solid #f5f5f5;">
                <div
                    onclick="pcToggleSection('pc-price-body','pc-price-arrow')"
                    style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:20px 28px 16px;"
                >
                    <span style="font-family:Montserrat,sans-serif;font-size:11px;font-weight:700;color:#111;letter-spacing:1.2px;text-transform:uppercase;">Price</span>
                    <svg id="pc-price-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s;flex-shrink:0;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div id="pc-price-body" style="padding:0 28px 28px;">
                    <div id="pc-slider-track" style="position:relative;height:2px;background:#D63044;margin:16px 0 20px;cursor:pointer;">
                        <div id="price-track-fill" style="position:absolute;top:0;height:100%;background:#D63044;left:0%;width:100%;"></div>
                        <div id="pc-thumb-min" style="position:absolute;top:50%;width:22px;height:22px;background:#D63044;border-radius:50%;transform:translate(-50%,-50%);cursor:grab;left:0%;z-index:2;"></div>
                        <div id="pc-thumb-max" style="position:absolute;top:50%;width:22px;height:22px;background:#D63044;border-radius:50%;transform:translate(-50%,-50%);cursor:grab;left:100%;z-index:2;"></div>
                    </div>

                    <div style="font-family:Montserrat,sans-serif;font-size:13px;color:#9ca3af;display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                        <span>Price:</span>
                        <span style="font-weight:400;">৳</span>
                        <span id="price-display-min" style="color:#111;font-weight:500;">--</span>
                        <span>-</span>
                        <span style="font-weight:400;">৳</span>
                        <span id="price-display-max" style="color:#111;font-weight:500;">--</span>
                    </div>
                </div>
            </div>

            <div style="border-bottom:1px solid #f5f5f5;">
                <div
                    onclick="pcToggleSection('pc-size-body','pc-size-arrow')"
                    style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:20px 28px;"
                >
                    <span style="font-family:Montserrat,sans-serif;font-size:11px;font-weight:700;color:#111;letter-spacing:1.2px;text-transform:uppercase;">Size</span>
                    <svg id="pc-size-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div id="pc-size-body" style="padding:4px 28px 20px;display:block;">
                    <button
                        id="pc-size-trigger"
                        type="button"
                        onclick="pcOpenSizeSelector()"
                        style="width:100%;min-height:48px;padding:0 16px;border:1.5px solid #e5e7eb;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;font-family:Montserrat,sans-serif;font-size:13px;font-weight:500;color:#111;cursor:pointer;text-align:left;"
                    >
                        <span id="pc-size-trigger-label">Select Size</span>

                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <div
                        id="pc-size-trigger-summary"
                        style="display:none;margin-top:8px;font-family:Montserrat,sans-serif;font-size:11px;font-weight:500;color:#6b7280;letter-spacing:.2px;"
                    ></div>
                </div>
            </div>

        </div>

        <div style="padding:16px 28px;border-top:1px solid #f3f4f6;">
            <button
                onclick="pcApplyFilters()"
                style="width:100%;height:50px;background:#D63044;color:#fff;border:none;border-radius:10px;font-family:Montserrat,sans-serif;font-size:13px;font-weight:600;cursor:pointer;letter-spacing:.5px;"
                onmouseover="this.style.background='#c00510'"
                onmouseout="this.style.background='#D63044'"
            >
                Apply Filters
            </button>
        </div>
    </div>

    <div
        id="pc-size-selector-overlay"
        onclick="pcCloseSizeSelector()"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10000;"
    ></div>

    <div
        id="pc-size-selector-modal"
        style="display:none;position:fixed;background:#fff;z-index:10001;box-shadow:0 24px 80px rgba(15,23,42,.2);overflow:hidden;"
    >
        <div style="display:flex;align-items:center;justify-content:space-between;padding:24px 24px 16px;border-bottom:1px solid #f3f4f6;gap:16px;">
            <span style="font-family:Montserrat,sans-serif;font-size:20px;font-weight:500;color:#111;">Select Size</span>

            <button
                type="button"
                onclick="pcCloseSizeSelector()"
                style="background:none;border:none;cursor:pointer;color:#111;display:flex;align-items:center;justify-content:center;padding:0;"
            >
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div style="padding:18px 24px 24px;">
            <div style="position:relative;">
                <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;color:#111;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                <input
                    id="pc-size-search"
                    type="text"
                    placeholder="Search Size"
                    oninput="pcFilterSizeOptions(this.value)"
                    style="width:100%;height:48px;border:1.5px solid #e5e7eb;border-radius:10px;padding:0 16px 0 46px;font-family:Montserrat,sans-serif;font-size:14px;color:#111;outline:none;background:#fff;"
                >
            </div>

            <div
                id="pc-size-selector-scroll"
                style="margin-top:18px;overflow-y:auto;"
            >
                <div
                    id="pc-size-selector-grid"
                    style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;"
                ></div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            (function () {
                var CATEGORY_ID = @json((string) $category->id);
                var PRODUCTS_API_URL = @json(route('shop.api.products.index'));
                var WISHLIST_INDEX_URL = @json(route('shop.api.customers.account.wishlist.index'));
                var WISHLIST_STORE_URL = @json(route('shop.api.customers.account.wishlist.store'));
                var FILTER_ATTRIBUTES_API_URL = @json(route('shop.api.categories.attributes'));
                var FILTER_ATTRIBUTE_OPTIONS_URL = @json(route('shop.api.categories.attribute_options', ['attribute_id' => '__ATTRIBUTE_ID__']));
                var CART_STORE_URL = '/api/checkout/cart';
                var LOGIN_URL = @json(route('shop.customer.session.index'));
                var PRICE_RANGE_URL = @json(route('shop.api.categories.price_range', ['id' => $category->id]));
                var EMPTY_TEXT = @json(trans('shop::app.categories.view.empty'));
                var ADD_TO_WISHLIST_LABEL = @json(trans('shop::app.components.products.card.add-to-wishlist'));
                var isCustomer = @json(auth()->guard('customer')->check());
                var wishlistEnabled = @json((bool) core()->getConfigData('customer.settings.wishlist.wishlist_option'));

                var state = {
                    activeCategoryId: String(CATEGORY_ID),
                    activeParentId: String(CATEGORY_ID),
                    page: 1,
                    lastPage: 1,
                    loading: false,
                    sort: '',
                    filters: {},
                };

                var cache = {};
                var selectedSizes = {};
                var selectedColors = {};
                var selectedCats = {};
                var sizeOptions = [
                    { id: '1-2', label: '1-2' },
                    { id: '2', label: '2' },
                    { id: '2-3', label: '2-3' },
                    { id: '3', label: '3' },
                    { id: '3-4', label: '3-4' },
                    { id: '4', label: '4' },
                    { id: '4-5', label: '4-5' },
                    { id: '5', label: '5' },
                    { id: '5-6', label: '5-6' },
                    { id: '6', label: '6' },
                    { id: '6-7', label: '6-7' },
                    { id: '6-8', label: '6-8' },
                    { id: '7', label: '7' },
                    { id: '7-8', label: '7-8' },
                    { id: '8', label: '8' },
                    { id: '8-9', label: '8-9' },
                    { id: '9', label: '9' },
                    { id: '9-10', label: '9-10' },
                    { id: '10', label: '10' },
                    { id: '10-11', label: '10-11' },
                    { id: '11', label: '11' },
                    { id: '11-12', label: '11-12' },
                    { id: '12', label: '12' },
                    { id: '12-13', label: '12-13' },
                    { id: '13', label: '13' },
                    { id: '13-14', label: '13-14' },
                    { id: '14', label: '14' },
                    { id: '14-15', label: '14-15' },
                    { id: '16', label: '16' },
                    { id: '20', label: '20' },
                    { id: '22', label: '22' },
                    { id: '24', label: '24' },
                    { id: '26', label: '26' },
                    { id: '28', label: '28' },
                    { id: '30', label: '30' },
                    { id: '32', label: '32' },
                    { id: '34', label: '34' },
                    { id: '36', label: '36' },
                    { id: '38', label: '38' },
                    { id: '40', label: '40' },
                    { id: '42', label: '42' },
                    { id: '44', label: '44' },
                    { id: '46', label: '46' },
                    { id: '48', label: '48' },
                    { id: '50', label: '50' },
                    { id: '52', label: '52' },
                    { id: '54', label: '54' },
                    { id: '56', label: '56' },
                    { id: 'Free', label: 'Free' },
                    { id: 'L', label: 'L' },
                    { id: 'M', label: 'M' },
                    { id: 'S', label: 'S' },
                    { id: 'semi-stitched', label: 'semi-stitched' },
                    { id: 'Unstitched', label: 'Unstitched' },
                    { id: 'XL', label: 'XL' },
                    { id: 'XXL', label: 'XXL' },
                ];
                var sizeOptionsPromise = null;
                var sizeSearchTerm = '';
                var isLoadingSizeOptions = false;
                var categoryTreePromise = null;
                var PRICE_MIN = 0;
                var PRICE_MAX = 100;
                var PRICE_STEP = 10;
                var sliderVal = { min: 0, max: 100 };

                function cacheKey() {
                    return state.activeCategoryId + '__' + state.sort + '__' + JSON.stringify(state.filters);
                }

                function normalizePriceValue(value) {
                    return Math.round(Number(value || 0) * 100) / 100;
                }

                function formatPriceValue(value) {
                    var normalizedValue = normalizePriceValue(value);

                    return Number.isInteger(normalizedValue)
                        ? normalizedValue.toLocaleString()
                        : normalizedValue.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                }

                function updatePriceStep() {
                    var rangeSpan = Math.max(PRICE_MAX - PRICE_MIN, 0);

                    PRICE_STEP = rangeSpan <= 0
                        ? 0
                        : Math.min(10, rangeSpan);
                }

                function setPriceBounds(minPrice, maxPrice) {
                    PRICE_MIN = normalizePriceValue(minPrice);
                    PRICE_MAX = Math.max(PRICE_MIN, normalizePriceValue(maxPrice));

                    updatePriceStep();

                    sliderVal.min = PRICE_MIN;
                    sliderVal.max = PRICE_MAX;

                    pcRenderSlider();
                }

                function fetchPriceRange() {
                    return fetch(PRICE_RANGE_URL, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            var minPrice = data.data && data.data.min_price !== null && data.data.min_price !== undefined
                                ? data.data.min_price
                                : 0;
                            var maxPrice = data.data && data.data.max_price !== null && data.data.max_price !== undefined
                                ? data.data.max_price
                                : minPrice;

                            setPriceBounds(minPrice, maxPrice);
                        })
                        .catch(function (error) {
                            console.error('[CategoryPagePriceRange]', error);
                        });
                }

                function buildCatTree(apiCategories) {
                    var tree = { all: { title: 'Category', items: [] } };

                    function walk(cats, parentKey) {
                        cats.forEach(function (cat) {
                            if (!tree[String(parentKey)]) {
                                tree[String(parentKey)] = { title: 'Category', items: [] };
                            }

                            tree[String(parentKey)].items.push({ id: cat.id, label: cat.name });

                            if (cat.children && cat.children.length > 0) {
                                tree[String(cat.id)] = { title: cat.name, items: [] };
                                walk(cat.children, cat.id);
                            }
                        });
                    }

                    walk(apiCategories, 'all');

                    return tree;
                }

                function applyResponsiveGrid() {
                    var grid = document.getElementById('category-page-product-grid');
                    var skeleton = document.getElementById('category-page-product-skeleton-grid');

                    if (window.innerWidth <= 768) {
                        if (grid) {
                            grid.style.gridTemplateColumns = 'repeat(2,1fr)';
                            grid.style.gap = '10px';
                        }

                        if (skeleton) {
                            skeleton.style.gridTemplateColumns = 'repeat(2,1fr)';
                            skeleton.style.gap = '10px';
                        }
                    } else {
                        if (grid) {
                            grid.style.gridTemplateColumns = 'repeat(auto-fill,minmax(220px,1fr))';
                            grid.style.gap = '24px';
                        }

                        if (skeleton) {
                            skeleton.style.gridTemplateColumns = 'repeat(auto-fill,minmax(220px,1fr))';
                            skeleton.style.gap = '24px';
                        }
                    }
                }

                function buildProductQuery() {
                    var params = new URLSearchParams();
                    params.append('limit', '8');
                    params.append('page', String(state.page));
                    params.append('category_id', String(CATEGORY_ID));

                    if (state.sort) {
                        params.append('sort', state.sort);
                    }

                    if (state.filters.price) {
                        params.append('price', state.filters.price);
                    }

                    if (state.filters.color) {
                        params.append('color', state.filters.color);
                    }

                    if (state.filters.size) {
                        params.append('size', state.filters.size);
                    }

                    if (state.filters.category_id) {
                        params.set('category_id', state.filters.category_id);
                    }

                    return params.toString();
                }

                function loadProducts(replace) {
                    var key = cacheKey();
                    var grid = document.getElementById('category-page-product-grid');
                    var loading = document.getElementById('product-loading');
                    var empty = document.getElementById('product-empty');
                    var spinner = document.getElementById('infinite-scroll-spinner');

                    if (replace && cache[key]) {
                        loading.style.display = 'none';
                        empty.style.display = 'none';
                        grid.innerHTML = '';

                        cache[key].page1.forEach(function (product) {
                            grid.insertAdjacentHTML('beforeend', buildCard(product));
                        });

                        state.page = 2;
                        state.lastPage = cache[key].lastPage;
                        spinner.style.display = state.page - 1 < state.lastPage ? 'flex' : 'none';
                        applyResponsiveGrid();
                        return;
                    }

                    fetchProducts(replace);
                }

                function fetchProducts(replace) {
                    if (state.loading) {
                        return;
                    }

                    var loading = document.getElementById('product-loading');
                    var empty = document.getElementById('product-empty');
                    var grid = document.getElementById('category-page-product-grid');
                    var spinner = document.getElementById('infinite-scroll-spinner');

                    state.loading = true;

                    if (replace) {
                        loading.style.display = 'block';
                        empty.style.display = 'none';
                        spinner.style.display = 'none';
                    } else {
                        spinner.style.display = 'flex';
                    }

                    fetch(PRODUCTS_API_URL + '?' + buildProductQuery(), {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            loading.style.display = 'none';
                            state.loading = false;
                            state.lastPage = data.meta && data.meta.last_page ? data.meta.last_page : 1;

                            var products = data.data || [];

                            if (replace) {
                                grid.innerHTML = '';
                            }

                            if (replace && products.length === 0) {
                                empty.style.display = 'block';
                                empty.textContent = EMPTY_TEXT;
                                spinner.style.display = 'none';
                                return;
                            }

                            empty.style.display = 'none';

                            if (replace && state.page === 1) {
                                cache[cacheKey()] = {
                                    page1: products,
                                    lastPage: state.lastPage,
                                };
                            }

                            state.page++;

                            products.forEach(function (product) {
                                grid.insertAdjacentHTML('beforeend', buildCard(product));
                            });

                            syncWishlistStatesFromApi();

                            spinner.style.display = state.page - 1 < state.lastPage ? 'flex' : 'none';
                            applyResponsiveGrid();
                        })
                        .catch(function (error) {
                            loading.style.display = 'none';
                            state.loading = false;
                            console.error('[CategoryPageGrid]', error);
                        })
                        .finally(function () {
                            spinner.style.display = 'none';
                        });
                }

                window.loadMoreProducts = function () {
                    if (state.page - 1 >= state.lastPage || state.loading) {
                        return;
                    }

                    fetchProducts(false);
                };

                window.pcApplySort = function (value) {
                    state.sort = value;
                    state.page = 1;
                    state.lastPage = 1;
                    state.loading = false;
                    document.getElementById('category-page-product-grid').innerHTML = '';
                    loadProducts(true);
                };

                function renderCatSkeleton() {
                    var listEl = document.getElementById('pc-cat-list');
                    var section = document.getElementById('pc-cat-filter-section');

                    if (!listEl || !section) {
                        return;
                    }

                    section.style.display = 'block';
                    listEl.innerHTML = [1, 2, 3].map(function () {
                        return '<div style="height:40px;border-radius:8px;background:linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%);background-size:200% 100%;animation:pc-shimmer 1.4s infinite;margin-bottom:6px;"></div>';
                    }).join('');

                    if (!document.getElementById('pc-shimmer-style')) {
                        var style = document.createElement('style');
                        style.id = 'pc-shimmer-style';
                        style.textContent = '@keyframes pc-shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}';
                        document.head.appendChild(style);
                    }
                }

                function renderCatFilterSection() {
                    if (!window.CAT_TREE) {
                        return;
                    }

                    var key = String(state.activeParentId || CATEGORY_ID);
                    var data = window.CAT_TREE[key];
                    var listEl = document.getElementById('pc-cat-list');
                    var titleEl = document.getElementById('pc-cat-filter-title');
                    var section = document.getElementById('pc-cat-filter-section');

                    if (!listEl || !section) {
                        return;
                    }

                    if (!data || !data.items.length) {
                        section.style.display = 'none';
                        return;
                    }

                    section.style.display = 'block';

                    if (titleEl) {
                        titleEl.textContent = data.title;
                    }

                    listEl.innerHTML = '';

                    data.items.forEach(function (item) {
                        var isActive = !!selectedCats[item.id];
                        var row = document.createElement('div');
                        row.setAttribute('id', 'pc-cat-row-' + item.id);
                        row.setAttribute('data-cat-id', String(item.id));
                        row.setAttribute('onclick', 'pcToggleCat(this,' + item.id + ')');
                        row.style.cssText = 'display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:8px;cursor:pointer;border:1.5px solid ' + (isActive ? '#111' : 'transparent') + ';background:' + (isActive ? '#fafafa' : 'transparent') + ';';
                        row.innerHTML = '<div id="pc-cb-cat-' + item.id + '" style="width:16px;height:16px;border-radius:4px;border:1.5px solid ' + (isActive ? '#111' : '#d1d5db') + ';background:' + (isActive ? '#D63044' : '#fff') + ';flex-shrink:0;display:flex;align-items:center;justify-content:center;">'
                            + (isActive ? '<svg width="9" height="9" viewBox="0 0 12 10" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 5 4.5 9 11 1"></polyline></svg>' : '')
                            + '</div>'
                            + '<span style="font-family:Montserrat,sans-serif;font-size:13px;color:' + (isActive ? '#111' : '#4b5563') + ';font-weight:' + (isActive ? '500' : '400') + ';">' + esc(item.label) + '</span>';
                        listEl.appendChild(row);
                    });
                }

                function fetchCategoryTree() {
                    if (window.CAT_TREE) {
                        renderCatFilterSection();
                        return Promise.resolve(window.CAT_TREE);
                    }

                    if (categoryTreePromise) {
                        return categoryTreePromise;
                    }

                    renderCatSkeleton();

                    categoryTreePromise = fetch('/api/categories/tree', {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            window.CAT_TREE = buildCatTree(data.data || data);
                            renderCatFilterSection();
                            return window.CAT_TREE;
                        })
                        .catch(function (error) {
                            console.error('[CategoryPageFilterDrawer]', error);
                            var section = document.getElementById('pc-cat-filter-section');

                            if (section) {
                                section.style.display = 'none';
                            }
                        });

                    return categoryTreePromise;
                }

                /* ── Color Options (dynamic) ── */
                var colorOptionsPromise = null;
                var colorOptions = [];

                var PC_FALLBACK_COLOR_MAP = {
                    black: '#111111', white: '#f9fafb', blue: '#3b82f6', red: '#ef4444',
                    pink: '#ec4899', purple: '#a855f7', green: '#22c55e', yellow: '#facc15',
                    orange: '#f97316', grey: '#9ca3af', gray: '#9ca3af', brown: '#92400e'
                };

                function resolveSwatchHex(option) {
                    var swatch = String(option.swatch_value || '').trim();
                    if (swatch && (swatch.startsWith('#') || swatch.startsWith('rgb') || swatch.startsWith('hsl') || swatch.startsWith('linear-gradient'))) {
                        return swatch;
                    }
                    var name = String(option.name || '').toLowerCase().replace(/[^a-z]/g, '');
                    return PC_FALLBACK_COLOR_MAP[name] || PC_FALLBACK_COLOR_MAP[swatch.toLowerCase()] || '#cccccc';
                }

                function renderColorOptions() {
                    var container = document.getElementById('pc-color-body');
                    if (!container) return;

                    if (!colorOptions.length) {
                        container.innerHTML = '<div style="grid-column:1/-1;padding:8px 0 4px;font-family:Montserrat,sans-serif;font-size:12px;color:#9ca3af;">No colors available</div>';
                        return;
                    }

                    container.innerHTML = colorOptions.map(function (option) {
                        var id = option.id;
                        var label = option.name;
                        var hex = resolveSwatchHex(option);
                        var isWhite = hex === '#f9fafb' || hex.toLowerCase() === '#ffffff' || hex.toLowerCase() === 'white';
                        var isActive = !!selectedColors[id];
                        var swatchBorder = isWhite ? 'border:1.5px solid #d1d5db;' : '';
                        return '<div id="pc-color-row-' + id + '" onclick="pcToggleColor(this,\'' + id + '\')"'
                            + ' style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;border:1.5px solid ' + (isActive ? '#e5e7eb' : 'transparent') + ';background:' + (isActive ? '#fafafa' : 'transparent') + ';" >'
                            + '<div id="pc-cb-color-' + id + '" style="width:16px;height:16px;border-radius:4px;border:1.5px solid ' + (isActive ? '#D63044' : '#d1d5db') + ';flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;background:' + (isActive ? '#D63044' : '') + ';">' + (isActive ? '<svg width="9" height="9" viewBox="0 0 12 10" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 5 4.5 9 11 1"/></svg>' : '') + '</div>'
                            + '<div style="width:18px;height:18px;border-radius:50%;flex-shrink:0;background:' + hex + ';' + swatchBorder + '"></div>'
                            + '<span style="font-family:Montserrat,sans-serif;font-size:12px;color:#374151;font-weight:' + (isActive ? '500' : '400') + ';">' + label + '</span>'
                            + '</div>';
                    }).join('');
                }

                function fetchColorOptions() {
                    if (colorOptionsPromise) {
                        renderColorOptions();
                        return colorOptionsPromise;
                    }

                    var container = document.getElementById('pc-color-body');
                    if (container) {
                        container.innerHTML = '<div style="grid-column:1/-1;padding:8px 0 4px;font-family:Montserrat,sans-serif;font-size:12px;color:#9ca3af;">Loading colors...</div>';
                    }

                    colorOptionsPromise = fetch(FILTER_ATTRIBUTES_API_URL, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(function (data) {
                        var attrs = data.data || [];
                        var colorAttr = attrs.find(function (a) { return a.code === 'color'; });
                        if (!colorAttr) return null;
                        return fetch(FILTER_ATTRIBUTE_OPTIONS_URL.replace('__ATTRIBUTE_ID__', colorAttr.id) + '?per_page=200', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                    })
                    .then(function (r) {
                        if (!r) return { data: [] };
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (json) {
                        colorOptions = json.data || [];
                        renderColorOptions();
                    })
                    .catch(function (err) {
                        console.error('[ColorFilter]', err);
                        colorOptionsPromise = null;
                        var container = document.getElementById('pc-color-body');
                        if (container) container.innerHTML = '';
                    });

                    return colorOptionsPromise;
                }

                window.pcOpenDrawer = function () {
                    document.getElementById('pc-drawer-overlay').style.display = 'block';
                    document.getElementById('pc-filter-drawer').style.transform = 'translateX(0)';
                    document.body.style.overflow = 'hidden';
                    setTimeout(function () {
                        pcInitSlider();
                        pcRenderSlider();
                    }, 50);
                    fetchCategoryTree();
                    fetchSizeOptions();
                    renderSizeTrigger();
                    fetchColorOptions();
                };

                window.pcCloseDrawer = function () {
                    pcCloseSizeSelector();
                    document.getElementById('pc-drawer-overlay').style.display = 'none';
                    document.getElementById('pc-filter-drawer').style.transform = 'translateX(-100%)';
                    document.body.style.overflow = '';
                };

                function fetchJson(url) {
                    return fetch(url, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }

                        return response.json();
                    });
                }

                function uniqueSizeOptions(options) {
                    var seen = {};

                    return options.filter(function (option) {
                        var key = String(option.id);

                        if (seen[key]) {
                            return false;
                        }

                        seen[key] = true;

                        return true;
                    });
                }

                function renderSizeTrigger() {
                    var label = document.getElementById('pc-size-trigger-label');
                    var summary = document.getElementById('pc-size-trigger-summary');
                    var count = Object.keys(selectedSizes).length;

                    if (label) {
                        label.textContent = count ? count + (count === 1 ? ' size selected' : ' sizes selected') : 'Select Size';
                    }

                    if (summary) {
                        if (count) {
                            summary.style.display = 'block';
                            summary.textContent = count + (count === 1 ? ' size selected' : ' sizes selected');
                        } else {
                            summary.style.display = 'none';
                            summary.textContent = '';
                        }
                    }
                }

                function filteredSizeOptions() {
                    if (!sizeSearchTerm) {
                        return sizeOptions;
                    }

                    return sizeOptions.filter(function (option) {
                        return String(option.label || '').toLowerCase().indexOf(sizeSearchTerm) !== -1;
                    });
                }

                function renderSizeOptions() {
                    var grid = document.getElementById('pc-size-selector-grid');

                    if (!grid) {
                        return;
                    }

                    if (isLoadingSizeOptions) {
                        grid.innerHTML = '<div style="grid-column:1 / -1;padding:32px 0;text-align:center;font-family:Montserrat,sans-serif;font-size:13px;color:#6b7280;">Loading sizes...</div>';
                        return;
                    }

                    var options = filteredSizeOptions();

                    if (!options.length) {
                        grid.innerHTML = '<div style="grid-column:1 / -1;padding:32px 0;text-align:center;font-family:Montserrat,sans-serif;font-size:13px;color:#6b7280;">No sizes found</div>';
                        return;
                    }

                    grid.innerHTML = options.map(function (option) {
                        var isActive = !!selectedSizes[option.id];

                        return '<button type="button" onclick="pcToggleSize(\'' + esc(option.id) + '\')" style="min-height:56px;padding:12px 10px;border:1.5px solid ' + (isActive ? '#111' : '#e5e7eb') + ';border-radius:10px;background:' + (isActive ? '#D63044' : '#fff') + ';font-family:Montserrat,sans-serif;font-size:13px;font-weight:500;color:' + (isActive ? '#fff' : '#111') + ';cursor:pointer;line-height:1.3;word-break:break-word;overflow-wrap:break-word;">' + esc(option.label) + '</button>';
                    }).join('');
                }

                function applySizeModalLayout() {
                    var modal = document.getElementById('pc-size-selector-modal');
                    var scroller = document.getElementById('pc-size-selector-scroll');

                    if (!modal || !scroller) {
                        return;
                    }

                    if (window.innerWidth <= 768) {
                        modal.style.left = '0';
                        modal.style.right = '0';
                        modal.style.bottom = '0';
                        modal.style.top = 'auto';
                        modal.style.width = '100%';
                        modal.style.maxWidth = '100%';
                        modal.style.transform = 'none';
                        modal.style.borderRadius = '24px 24px 0 0';
                        modal.style.maxHeight = '78vh';
                        scroller.style.maxHeight = '52vh';
                    } else {
                        modal.style.left = '50%';
                        modal.style.right = 'auto';
                        modal.style.bottom = 'auto';
                        modal.style.top = '50%';
                        modal.style.width = 'min(720px, calc(100vw - 48px))';
                        modal.style.maxWidth = '720px';
                        modal.style.transform = 'translate(-50%, -50%)';
                        modal.style.borderRadius = '24px';
                        modal.style.maxHeight = '80vh';
                        scroller.style.maxHeight = '420px';
                    }
                }

                function normalizeSizeOptionsFromApi(items) {
                    return uniqueSizeOptions((items || []).map(function (item) {
                        return {
                            id: String(item.id),
                            label: item.name || item.label || String(item.id),
                        };
                    }));
                }

                function normalizeSizeLabel(label) {
                    return String(label || '').trim().toUpperCase().replace(/\s+/g, ' ');
                }

                function sortSizeOptions(options) {
                    var preferredOrder = {
                        '1-2': 1,
                        '2': 2,
                        '2-3': 3,
                        '3': 4,
                        '3-4': 5,
                        '4': 6,
                        '4-5': 7,
                        '5': 8,
                        '5-6': 9,
                        '6': 10,
                        '6-7': 11,
                        '6-8': 12,
                        '7': 13,
                        '7-8': 14,
                        '8': 15,
                        '8-9': 16,
                        '9': 17,
                        '9-10': 18,
                        '10': 19,
                        '10-11': 20,
                        '11': 21,
                        '11-12': 22,
                        '12': 23,
                        '12-13': 24,
                        '13': 25,
                        '13-14': 26,
                        '14': 27,
                        '14-15': 28,
                        '16': 29,
                        '20': 30,
                        '22': 31,
                        '24': 32,
                        '26': 33,
                        '28': 34,
                        '30': 35,
                        '32': 36,
                        '34': 37,
                        '36': 38,
                        '38': 39,
                        '40': 40,
                        '42': 41,
                        '44': 42,
                        '46': 43,
                        '48': 44,
                        '50': 45,
                        '52': 46,
                        '54': 47,
                        '56': 48,
                        'FREE': 49,
                        'L': 50,
                        'M': 51,
                        'S': 52,
                        'SEMI-STITCHED': 53,
                        'UNSTITCHED': 54,
                        'XL': 55,
                        'XXL': 56,
                    };

                    return (options || []).slice().sort(function (a, b) {
                        var labelA = normalizeSizeLabel(a.label);
                        var labelB = normalizeSizeLabel(b.label);
                        var rankA = Object.prototype.hasOwnProperty.call(preferredOrder, labelA) ? preferredOrder[labelA] : Number.MAX_SAFE_INTEGER;
                        var rankB = Object.prototype.hasOwnProperty.call(preferredOrder, labelB) ? preferredOrder[labelB] : Number.MAX_SAFE_INTEGER;

                        if (rankA !== rankB) {
                            return rankA - rankB;
                        }

                        return labelA.localeCompare(labelB, undefined, { numeric: true, sensitivity: 'base' });
                    });
                }

                function fetchSizeOptions() {
                    if (sizeOptionsPromise) {
                        return sizeOptionsPromise;
                    }

                    isLoadingSizeOptions = true;
                    renderSizeOptions();

                    sizeOptionsPromise = Promise.resolve(sortSizeOptions(uniqueSizeOptions(sizeOptions)))
                        .then(function (options) {
                            sizeOptions = options;

                            return sizeOptions;
                        })
                        .finally(function () {
                            isLoadingSizeOptions = false;
                            renderSizeTrigger();
                            renderSizeOptions();
                        });

                    return sizeOptionsPromise;
                }

                window.pcOpenSizeSelector = function () {
                    var overlay = document.getElementById('pc-size-selector-overlay');
                    var modal = document.getElementById('pc-size-selector-modal');
                    var input = document.getElementById('pc-size-search');

                    sizeSearchTerm = '';

                    if (input) {
                        input.value = '';
                    }

                    applySizeModalLayout();
                    renderSizeOptions();

                    if (overlay) {
                        overlay.style.display = 'block';
                    }

                    if (modal) {
                        modal.style.display = 'block';
                    }

                    fetchSizeOptions().finally(function () {
                        applySizeModalLayout();
                        renderSizeOptions();

                        setTimeout(function () {
                            if (input) {
                                input.focus();
                            }
                        }, 50);
                    });
                };

                window.pcCloseSizeSelector = function () {
                    var overlay = document.getElementById('pc-size-selector-overlay');
                    var modal = document.getElementById('pc-size-selector-modal');

                    if (overlay) {
                        overlay.style.display = 'none';
                    }

                    if (modal) {
                        modal.style.display = 'none';
                    }
                };

                window.pcFilterSizeOptions = function (value) {
                    sizeSearchTerm = String(value || '').toLowerCase();
                    renderSizeOptions();
                };

                window.pcToggleCat = function (row, catId) {
                    var isActive = !!selectedCats[catId];
                    var checkbox = row.querySelector('div');
                    var label = row.querySelector('span');

                    if (isActive) {
                        delete selectedCats[catId];
                        row.style.border = '1.5px solid transparent';
                        row.style.background = 'transparent';

                        if (checkbox) {
                            checkbox.style.background = '#fff';
                            checkbox.style.borderColor = '#d1d5db';
                            checkbox.innerHTML = '';
                        }

                        if (label) {
                            label.style.color = '#4b5563';
                            label.style.fontWeight = '400';
                        }
                    } else {
                        selectedCats[catId] = true;
                        row.style.border = '1.5px solid #111';
                        row.style.background = '#fafafa';

                        if (checkbox) {
                            checkbox.style.background = '#D63044';
                            checkbox.style.borderColor = '#111';
                            checkbox.innerHTML = '<svg width="9" height="9" viewBox="0 0 12 10" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 5 4.5 9 11 1"></polyline></svg>';
                        }

                        if (label) {
                            label.style.color = '#111';
                            label.style.fontWeight = '500';
                        }
                    }
                };

                window.pcToggleSection = function (bodyId, arrowId) {
                    var body = document.getElementById(bodyId);
                    var arrow = document.getElementById(arrowId);

                    if (!body) {
                        return;
                    }

                    var isOpen = body.style.display !== 'none';
                    body.style.display = isOpen ? 'none' : '';

                    if (arrow) {
                        arrow.style.transform = isOpen ? 'rotate(-90deg)' : 'rotate(0deg)';
                    }
                };

                window.pcToggleSize = function (id) {
                    if (selectedSizes[id]) {
                        delete selectedSizes[id];
                    } else {
                        selectedSizes[id] = true;
                    }

                    renderSizeTrigger();
                    renderSizeOptions();
                };

                window.pcToggleColor = function (row, id) {
                    var checkbox = document.getElementById('pc-cb-color-' + id);
                    var label = row.querySelector('span');

                    if (selectedColors[id]) {
                        delete selectedColors[id];
                        row.style.borderColor = 'transparent';
                        row.style.background = 'transparent';

                        if (checkbox) {
                            checkbox.style.background = '';
                            checkbox.style.borderColor = '#d1d5db';
                            checkbox.innerHTML = '';
                        }

                        if (label) {
                            label.style.fontWeight = '400';
                        }
                    } else {
                        selectedColors[id] = true;
                        row.style.borderColor = '#e5e7eb';
                        row.style.background = '#fafafa';

                        if (checkbox) {
                            checkbox.style.background = '#D63044';
                            checkbox.style.borderColor = '#111';
                            checkbox.innerHTML = '<svg width="9" height="9" viewBox="0 0 12 10" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 5 4.5 9 11 1"></polyline></svg>';
                        }

                        if (label) {
                            label.style.fontWeight = '500';
                        }
                    }
                };

                function pcRenderSlider() {
                    var rangeSpan = PRICE_MAX - PRICE_MIN || 1;
                    var minPercent = (sliderVal.min - PRICE_MIN) / rangeSpan * 100;
                    var maxPercent = (sliderVal.max - PRICE_MIN) / rangeSpan * 100;
                    var fill = document.getElementById('price-track-fill');
                    var thumbMin = document.getElementById('pc-thumb-min');
                    var thumbMax = document.getElementById('pc-thumb-max');

                    if (fill) {
                        fill.style.left = minPercent + '%';
                        fill.style.width = maxPercent - minPercent + '%';
                    }

                    if (thumbMin) {
                        thumbMin.style.left = minPercent + '%';
                    }

                    if (thumbMax) {
                        thumbMax.style.left = maxPercent + '%';
                    }

                    var displayMin = document.getElementById('price-display-min');
                    var displayMax = document.getElementById('price-display-max');

                    if (displayMin) {
                        displayMin.textContent = formatPriceValue(sliderVal.min);
                    }

                    if (displayMax) {
                        displayMax.textContent = formatPriceValue(sliderVal.max);
                    }
                }

                function pcInitSlider() {
                    var track = document.getElementById('pc-slider-track');
                    var thumbMin = document.getElementById('pc-thumb-min');
                    var thumbMax = document.getElementById('pc-thumb-max');

                    if (!track || !thumbMin || !thumbMax || track.dataset.initialized === 'true') {
                        return;
                    }

                    track.dataset.initialized = 'true';

                    function getPercent(clientX) {
                        var rect = track.getBoundingClientRect();
                        var percent = (clientX - rect.left) / rect.width;
                        return Math.max(0, Math.min(1, percent));
                    }

                    function snapToStep(value, step) {
                        if (step <= 0) {
                            return normalizePriceValue(value);
                        }

                        return normalizePriceValue(Math.round((value - PRICE_MIN) / step) * step + PRICE_MIN);
                    }

                    function attachDrag(thumb, which) {
                        function onMove(event) {
                            var clientX = event.touches ? event.touches[0].clientX : event.clientX;
                            var percent = getPercent(clientX);
                            var rawValue = PRICE_MIN + percent * (PRICE_MAX - PRICE_MIN);
                            var snappedValue = Math.max(PRICE_MIN, Math.min(PRICE_MAX, snapToStep(rawValue, PRICE_STEP)));

                            if (which === 'min') {
                                sliderVal.min = PRICE_STEP > 0
                                    ? Math.min(snappedValue, sliderVal.max - PRICE_STEP)
                                    : PRICE_MIN;
                            } else {
                                sliderVal.max = PRICE_STEP > 0
                                    ? Math.max(snappedValue, sliderVal.min + PRICE_STEP)
                                    : PRICE_MAX;
                            }

                            pcRenderSlider();
                        }

                        function onUp() {
                            document.removeEventListener('mousemove', onMove);
                            document.removeEventListener('mouseup', onUp);
                            document.removeEventListener('touchmove', onMove);
                            document.removeEventListener('touchend', onUp);
                            thumb.style.cursor = 'grab';
                        }

                        thumb.addEventListener('mousedown', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            thumb.style.cursor = 'grabbing';
                            document.addEventListener('mousemove', onMove);
                            document.addEventListener('mouseup', onUp);
                        });

                        thumb.addEventListener('touchstart', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            document.addEventListener('touchmove', onMove, { passive: false });
                            document.addEventListener('touchend', onUp);
                        }, { passive: false });
                    }

                    attachDrag(thumbMin, 'min');
                    attachDrag(thumbMax, 'max');
                    pcRenderSlider();
                }

                function updateFilterBadge() {
                    var badge = document.getElementById('filter-count-badge');
                    var count = Object.keys(state.filters).length;

                    if (!badge) {
                        return;
                    }

                    if (count > 0) {
                        badge.style.display = 'inline-block';
                        badge.textContent = count;
                    } else {
                        badge.style.display = 'none';
                    }
                }

                window.pcApplyFilters = function () {
                    state.filters = {};

                    var catIds = Object.keys(selectedCats);
                    if (catIds.length) {
                        state.filters.category_id = catIds.join(',');
                    }

                    var sizeIds = Object.keys(selectedSizes);
                    if (sizeIds.length) {
                        state.filters.size = sizeIds.join(',');
                    }

                    var colorIds = Object.keys(selectedColors);
                    if (colorIds.length) {
                        state.filters.color = colorIds.join(',');
                    }

                    if (sliderVal.min > PRICE_MIN || sliderVal.max < PRICE_MAX) {
                        state.filters.price = sliderVal.min + ',' + sliderVal.max;
                    }

                    updateFilterBadge();
                    pcCloseDrawer();
                    state.page = 1;
                    state.lastPage = 1;
                    state.loading = false;
                    document.getElementById('category-page-product-grid').innerHTML = '';
                    loadProducts(true);
                };

                window.pcClearFilters = function () {
                    state.filters = {};
                    selectedSizes = {};
                    selectedColors = {};
                    selectedCats = {};
                    sizeSearchTerm = '';

                    var sizeInput = document.getElementById('pc-size-search');
                    if (sizeInput) {
                        sizeInput.value = '';
                    }

                    renderSizeTrigger();
                    renderSizeOptions();

                    document.querySelectorAll('[id^="pc-color-row-"]').forEach(function (row) {
                        row.style.borderColor = 'transparent';
                        row.style.background = 'transparent';

                        var checkbox = row.querySelector('[id^="pc-cb-color-"]');
                        if (checkbox) {
                            checkbox.style.background = '';
                            checkbox.style.borderColor = '#d1d5db';
                            checkbox.innerHTML = '';
                        }

                        var label = row.querySelector('span');
                        if (label) {
                            label.style.fontWeight = '400';
                        }
                    });
                    renderColorOptions();

                    renderCatFilterSection();
                    sliderVal.min = PRICE_MIN;
                    sliderVal.max = PRICE_MAX;
                    pcRenderSlider();
                    updateFilterBadge();
                };

                function buildCard(product) {
                    var isMobile = window.innerWidth <= 768;
                    var primaryImage = product.base_image && product.base_image.medium_image_url ? product.base_image.medium_image_url : '';
                    var secondaryImage = product.images && product.images[1] && product.images[1].medium_image_url ? product.images[1].medium_image_url : primaryImage;
                    var url = '/' + product.url_key;
                    var price = product.min_price || '';
                    var isSaleable = !!product.is_saleable;
                    var cardId = 'pc-' + product.id;
                    
                    var badge = product.is_new ? '<div style="position:absolute;top:10px;left:10px;z-index:4;background:#ef4444;color:#fff;font-size:10px;font-weight:600;padding:3px 8px;border-radius:9999px;font-family:Montserrat,sans-serif;letter-spacing:.3px;">New</div>' : '';
                    var wishlistButton = buildWishlistButton(product, isMobile);
                    var images = primaryImage
                        ? '<div style="position:relative;width:100%;aspect-ratio:2/3;overflow:hidden;">'
                            + '<img data-role="img1" src="' + primaryImage + '" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:opacity 1s ease,transform 1s ease;opacity:1;transform:scale(1);">'
                            + '<img data-role="img2" src="' + secondaryImage + '" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:opacity 1s ease,transform 1s ease;opacity:0;transform:scale(1);">'
                            + wishlistButton
                            + '</div>'
                        : '<div style="width:100%;aspect-ratio:2/3;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:12px;font-family:Montserrat,sans-serif;">No image</div>';

                    var cta;
                    var isConfigurable = product.type === 'configurable';

                    if (isMobile) {
                        var cartIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
                        var eyeIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                        
                        // For configurable products, always show view icon and navigate to product page
                        var showCartButton = isSaleable && !isConfigurable;
                        
                        cta = '<button data-role="cta" '
                            + 'onclick="event.stopPropagation();event.preventDefault();' + (showCartButton ? 'pcAddToCartById(this,' + product.id + ')' : 'pcGoTo(event,\'' + url + '\')') + '" '
                            + 'ontouchend="event.stopPropagation();event.preventDefault();' + (showCartButton ? 'pcAddToCartById(this,' + product.id + ')' : 'pcGoTo(event,\'' + url + '\')') + '" '
                            + 'style="position:absolute;bottom:10px;right:10px;z-index:4;width:44px;height:44px;background:#111;color:#fff;border:none;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:auto;-webkit-tap-highlight-color:transparent;">'
                            + (showCartButton ? cartIcon : eyeIcon)
                            + '</button>';
                    } else {
                        // For configurable products, always show "View Product" instead of "Add to cart"
                        var label = (isSaleable && !isConfigurable) ? 'Add to cart' : 'View product';
                        var hoverIcon = (isSaleable && !isConfigurable)
                            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>'
                            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                        cta = '<div style="position:absolute;bottom:12px;left:0;right:0;display:flex;justify-content:center;pointer-events:auto;">'
                            + '<button data-role="cta" onclick="event.stopPropagation();event.preventDefault();' + ((isSaleable && !isConfigurable) ? 'pcAddToCart(event,' + product.id + ')' : 'pcGoTo(event,\'' + url + '\')') + '" '
                            + 'style="display:inline-flex;align-items:center;justify-content:center;height:44px;padding:0 28px;background:#111;color:#fff;border:none;outline:none;border-radius:5px;cursor:pointer;transform:translateY(0);transition:transform .3s ease;overflow:hidden;position:relative;min-width:140px;pointer-events:auto;-webkit-tap-highlight-color:transparent;">'
                            + '<span data-role="btn-text" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;font-family:Montserrat,sans-serif;letter-spacing:.2px;transition:transform .28s ease,opacity .28s ease;transform:translateY(0);opacity:1;z-index:1;">' + label + '</span>'
                            + '<span data-role="btn-icon" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#111;transition:transform .28s ease,opacity .28s ease;transform:translateY(100%);opacity:1;z-index:2;">' + hoverIcon + '</span>'
                            + '</button></div>';
                    }

                    setTimeout(function () {
                        attachCardHover(cardId);
                    }, 0);

                    if (isMobile) {
                        return '<div id="' + cardId + '" data-product-id="' + product.id + '" onclick="pcGoTo(event,\'' + url + '\')" style="cursor:pointer;font-family:Montserrat,sans-serif;">'
                            + '<div style="position:relative;border-radius:6px;overflow:hidden;background:#f9f9f9;">' + badge + images + cta + '</div>'
                            + '<div style="padding:6px 2px 0;">'
                            + '<p style="font-size:11px;font-weight:400;color:#111827;margin:0 0 3px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">' + esc(product.name) + '</p>'
                            + '<p style="font-size:12px;font-weight:600;color:#111;margin:0;">' + price + '</p>'
                            + '</div></div>';
                    }

                    return '<div id="' + cardId + '" data-product-id="' + product.id + '" onclick="pcGoTo(event,\'' + url + '\')" style="cursor:pointer;font-family:Montserrat,sans-serif;">'
                        + '<div style="position:relative;border-radius:8px;overflow:hidden;background:#f9f9f9;">' + badge + images + cta + '</div>'
                        + '<div style="padding:10px 2px 0;">'
                        + '<p style="font-size:13px;font-weight:400;color:#111827;margin:0 0 4px;line-height:1.4;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + esc(product.name) + '</p>'
                        + '<p style="font-size:13px;color:#6b7280;margin:0;">' + price + '</p>'
                        + '</div></div>';
                }

                function buildWishlistButton(product, isMobile) {
                    return '';
                }

                function heartIcon(isActive) {
                    if (isActive) {
                        return '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19.5 13.572 12 21l-7.5-7.428A5 5 0 0 1 12 6.162a5 5 0 0 1 7.5 7.41Z"></path></svg>';
                    }

                    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19.5 13.572 12 21l-7.5-7.428A5 5 0 0 1 12 6.162a5 5 0 0 1 7.5 7.41Z"></path></svg>';
                }

                function attachCardHover(cardId) {
                    var card = document.getElementById(cardId);

                    if (!card || card.dataset.hoverBound === 'true') {
                        return;
                    }

                    card.dataset.hoverBound = 'true';

                    var img1 = card.querySelector('[data-role="img1"]');
                    var img2 = card.querySelector('[data-role="img2"]');
                    var btn = card.querySelector('[data-role="cta"]');
                    var btnText = card.querySelector('[data-role="btn-text"]');
                    var btnIcon = card.querySelector('[data-role="btn-icon"]');

                    card.addEventListener('mouseenter', function () {
                        if (img1) {
                            img1.style.opacity = '0';
                            img1.style.transform = 'scale(1.05)';
                        }

                        if (img2) {
                            img2.style.opacity = '1';
                            img2.style.transform = 'scale(1.05)';
                        }

                        if (btn) {
                            btn.style.transform = 'translateY(-6px)';
                        }
                    });

                    card.addEventListener('mouseleave', function () {
                        if (img1) {
                            img1.style.opacity = '1';
                            img1.style.transform = 'scale(1)';
                        }

                        if (img2) {
                            img2.style.opacity = '0';
                            img2.style.transform = 'scale(1)';
                        }

                        if (btn) {
                            btn.style.transform = 'translateY(0)';
                        }

                        if (btnText) {
                            btnText.style.transform = 'translateY(0)';
                            btnText.style.opacity = '1';
                        }

                        if (btnIcon) {
                            btnIcon.style.transform = 'translateY(100%)';
                        }
                    });

                    if (btn) {
                        btn.addEventListener('mouseenter', function (event) {
                            event.stopPropagation();

                            if (btnText) {
                                btnText.style.transform = 'translateY(-100%)';
                                btnText.style.opacity = '0';
                            }

                            if (btnIcon) {
                                btnIcon.style.transform = 'translateY(0)';
                            }
                        });

                        btn.addEventListener('mouseleave', function (event) {
                            event.stopPropagation();

                            if (btnText) {
                                btnText.style.transform = 'translateY(0)';
                                btnText.style.opacity = '1';
                            }

                            if (btnIcon) {
                                btnIcon.style.transform = 'translateY(100%)';
                            }
                        });
                    }
                }

                window.pcGoTo = function (event, url) {
                    if (event.target.closest('[data-role="cta"]') || event.target.closest('[data-role="wishlist"]')) {
                        return;
                    }

                    window.location.href = url;
                };

                window.pcToggleWishlist = function (button, productId) {
                    if (!wishlistEnabled) {
                        return;
                    }

                    if (!isCustomer) {
                        window.location.href = LOGIN_URL;
                        return;
                    }

                    button.disabled = true;
                    button.style.opacity = '0.65';

                    fetch(WISHLIST_STORE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ product_id: productId }),
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        })
                        .then(function () {
                            var isActive = button.dataset.active === 'true';
                            var nextState = !isActive;
                            button.dataset.active = nextState ? 'true' : 'false';
                            button.style.color = nextState ? '#dc2626' : '#111';
                            button.innerHTML = heartIcon(nextState);
                        })
                        .catch(function (error) {
                            console.error('Wishlist error:', error);
                        })
                        .finally(function () {
                            button.disabled = false;
                            button.style.opacity = '1';
                        });
                };

                window.pcAddToCart = function (event, id) {
                    if (event && event.preventDefault) {
                        event.preventDefault();
                    }

                    var button = event && (event.currentTarget || event.target);
                    if (button && button.tagName !== 'BUTTON') {
                        button = button.closest('button') || button;
                    }

                    var isMobileButton = button && !button.querySelector('[data-role="btn-text"]');
                    var buttonText = button ? button.querySelector('[data-role="btn-text"]') : null;

                    if (button) {
                        button.disabled = true;
                        button.style.opacity = '0.6';
                    }

                    if (buttonText) {
                        buttonText.textContent = 'Adding...';
                    }

                    fetch(CART_STORE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ product_id: id, quantity: 1 }),
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        })
                        .then(function () {
                            if (button) {
                                button.style.background = '#22c55e';
                                button.style.opacity = '1';
                            }

                            if (buttonText) {
                                buttonText.textContent = 'Added!';
                            }

                            if (isMobileButton && button) {
                                button.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                            }

                            window.dispatchEvent(new CustomEvent('cart-updated'));

                            setTimeout(function () {
                                if (button) {
                                    button.style.background = '#111';
                                    button.style.opacity = '1';
                                    button.disabled = false;
                                }

                                if (buttonText) {
                                    buttonText.textContent = 'Add to cart';
                                }

                                if (isMobileButton && button) {
                                    button.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
                                }
                            }, 1500);
                        })
                        .catch(function (error) {
                            console.error('Add to cart error:', error);

                            if (button) {
                                button.style.background = '#ef4444';
                                button.style.opacity = '1';
                            }

                            if (buttonText) {
                                buttonText.textContent = 'Error';
                            }

                            setTimeout(function () {
                                if (button) {
                                    button.style.background = '#111';
                                    button.disabled = false;
                                }

                                if (buttonText) {
                                    buttonText.textContent = 'Add to cart';
                                }

                                if (isMobileButton && button) {
                                    button.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
                                }
                            }, 2000);
                        });
                };

                window.pcAddToCartById = function (button, id) {
                    if (!button) {
                        return;
                    }

                    button.disabled = true;
                    button.style.opacity = '0.6';

                    var cartSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
                    var checkSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

                    fetch(CART_STORE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ product_id: id, quantity: 1 }),
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        })
                        .then(function () {
                            button.style.background = '#22c55e';
                            button.style.opacity = '1';
                            button.innerHTML = checkSvg;
                            window.dispatchEvent(new CustomEvent('cart-updated'));

                            setTimeout(function () {
                                button.style.background = '#111';
                                button.innerHTML = cartSvg;
                                button.disabled = false;
                            }, 1500);
                        })
                        .catch(function (error) {
                            console.error('Add to cart error:', error);
                            button.style.background = '#ef4444';
                            button.style.opacity = '1';

                            setTimeout(function () {
                                button.style.background = '#111';
                                button.innerHTML = cartSvg;
                                button.disabled = false;
                            }, 2000);
                        });
                };

                function esc(value) {
                    if (!value) {
                        return '';
                    }

                    return String(value)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;');
                }

                function syncWishlistStatesFromApi() {
                    if (!isCustomer || !wishlistEnabled) {
                        return;
                    }

                    fetch(WISHLIST_INDEX_URL, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            var ids = {};
                            (data.data || []).forEach(function (item) {
                                if (item.product && item.product.id) {
                                    ids[String(item.product.id)] = true;
                                }
                            });

                            document.querySelectorAll('[data-role="wishlist"]').forEach(function (button) {
                                var card = button.closest('[data-product-id]');
                                if (!card) {
                                    return;
                                }

                                var productId = String(card.getAttribute('data-product-id'));
                                var active = !!ids[productId];
                                button.dataset.active = active ? 'true' : 'false';
                                button.style.color = active ? '#dc2626' : '#111';
                                button.innerHTML = heartIcon(active);
                            });
                        })
                        .catch(function (error) {
                            console.error('Wishlist sync error:', error);
                        });
                }

                function boot() {
                    var sortSelect = document.getElementById('sort-select');
                    if (sortSelect) {
                        sortSelect.value = state.sort;
                    }

                    fetchSizeOptions();
                    renderSizeTrigger();
                    applySizeModalLayout();

                    fetchPriceRange().finally(function () {
                        pcInitSlider();
                        pcRenderSlider();
                        loadProducts(true);

                        // Set up IntersectionObserver for infinite scroll
                        var sentinel = document.getElementById('infinite-scroll-sentinel');
                        if (sentinel) {
                            window._scrollObserver = new IntersectionObserver(function (entries) {
                                if (entries[0].isIntersecting && !state.loading && state.page - 1 < state.lastPage) {
                                    loadMoreProducts();
                                }
                            }, { rootMargin: '200px' });
                            window._scrollObserver.observe(sentinel);
                        }
                    });
                    renderCatFilterSection();
                    updateFilterBadge();
                    syncWishlistStatesFromApi();
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () {
                        setTimeout(boot, 100);
                    });
                } else {
                    setTimeout(boot, 100);
                }

                window.addEventListener('resize', function () {
                    applyResponsiveGrid();
                    applySizeModalLayout();
                });
            })();
        </script>
    @endPushOnce
</x-shop::layouts>