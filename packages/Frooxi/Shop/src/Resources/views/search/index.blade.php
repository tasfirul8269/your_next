<?php
    $searchTitle = $suggestion ?? $query;
    $title = $searchTitle ? trans('shop::app.search.title', ['query' => $searchTitle]) : trans('shop::app.search.results');
    $searchInstead = $suggestion ? $query : null;
?>
<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="description"
        content="{{ $title }}"
    />

    <meta
        name="keywords"
        content="{{ $title }}"
    />
@endPush

<x-shop::layouts :has-feature="false">
    <!-- Page Title -->
    <x-slot:title>
        {{ $title }}
    </x-slot>

    <style>
        @keyframes shimmerPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .4; }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        #sp-product-grid::-webkit-scrollbar,
        #sp-skeleton-grid::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div style="width:100%;background:#fff;padding:36px 16px 72px;">
        <div style="max-width:1400px;margin:0 auto;">

            <!-- Page heading -->
            <div style="padding:12px 0 36px;text-align:center;">
                <p style="margin:0 0 8px;font-family:Montserrat,sans-serif;font-size:11px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:#9ca3af;">
                    Search Results
                </p>

                <h1 style="margin:0;font-family:Montserrat,sans-serif;font-size:clamp(26px,4vw,42px);font-weight:500;line-height:1.05;letter-spacing:.08em;text-transform:uppercase;color:#111;">
                    {{ $searchTitle ?: trans('shop::app.search.results') }}
                </h1>

                @if ($searchInstead)
                    <form
                        action="{{ route('shop.search.index', ['suggest' => false]) }}"
                        style="margin-top:18px;display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:10px;"
                        role="search"
                    >
                        <input type="hidden" name="query" value="{{ $searchInstead }}">
                        <input type="hidden" name="suggest" value="0">

                        <p style="font-family:Montserrat,sans-serif;font-size:13px;color:#6b7280;margin:0;">
                            {{ trans('shop::app.search.suggest') }}
                        </p>

                        <button
                            type="submit"
                            style="display:inline-flex;align-items:center;border:1.5px solid #111;border-radius:9999px;padding:8px 20px;font-family:Montserrat,sans-serif;font-size:13px;font-weight:500;color:#111;background:none;cursor:pointer;"
                        >
                            {{ $searchInstead }}
                        </button>
                    </form>
                @endif
            </div>

            <!-- Toolbar: Filter + Sort -->
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
                <button
                    id="sp-filter-btn"
                    onclick="spOpenDrawer()"
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
                    <span id="sp-filter-count-badge" style="display:none;background:#e30612;color:#fff;font-size:10px;font-weight:600;border-radius:9999px;padding:1px 7px;line-height:1.6;"></span>
                </button>

                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-family:Montserrat,sans-serif;font-size:13px;color:#9ca3af;">Sort by:</span>

                    <div style="position:relative;display:inline-block;">
                        <select
                            id="sp-sort-select"
                            onchange="spApplySort(this.value)"
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

            <!-- Skeleton -->
            <div id="sp-loading" style="display:block;">
                <div id="sp-skeleton-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px;">
                    @for ($i = 0; $i < 8; $i++)
                        <div>
                            <div style="width:100%;aspect-ratio:2/3;border-radius:8px;background:#e5e7eb;animation:shimmerPulse 1.5s infinite;"></div>
                            <div style="margin-top:12px;height:14px;width:70%;border-radius:4px;background:#e5e7eb;animation:shimmerPulse 1.5s infinite;"></div>
                            <div style="margin-top:7px;height:13px;width:40%;border-radius:4px;background:#e5e7eb;animation:shimmerPulse 1.5s infinite;"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Empty state -->
            <div id="sp-empty" style="display:none;text-align:center;padding:64px 16px;color:#9ca3af;font-family:Montserrat,sans-serif;font-size:15px;">
                @lang('shop::app.categories.view.empty')
            </div>

            <!-- Product grid -->
            <div id="sp-product-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px;"></div>

            <!-- Infinite scroll sentinel + spinner -->
            <div id="sp-infinite-scroll-sentinel" style="height:1px;"></div>
            <div id="sp-infinite-scroll-spinner" style="display:none;justify-content:center;padding:24px 0;">
                <svg style="width:32px;height:32px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#e30612" stroke-width="2">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Drawer overlay -->
    <div
        id="sp-drawer-overlay"
        onclick="spCloseDrawer()"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9990;backdrop-filter:blur(2px);"
    ></div>

    <!-- Filter drawer -->
    <div
        id="sp-filter-drawer"
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
                    onclick="spClearFilters()"
                    style="font-family:Montserrat,sans-serif;font-size:12px;color:#9ca3af;background:none;border:none;cursor:pointer;letter-spacing:.2px;"
                    onmouseover="this.style.color='#111'"
                    onmouseout="this.style.color='#9ca3af'"
                >
                    Clear all
                </button>

                <button
                    onclick="spCloseDrawer()"
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
            <!-- Price -->
            <div style="border-bottom:1px solid #f5f5f5;">
                <div
                    onclick="spToggleSection('sp-price-body','sp-price-arrow')"
                    style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:20px 28px 16px;"
                >
                    <span style="font-family:Montserrat,sans-serif;font-size:11px;font-weight:700;color:#111;letter-spacing:1.2px;text-transform:uppercase;">Price</span>
                    <svg id="sp-price-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s;flex-shrink:0;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div id="sp-price-body" style="padding:0 28px 28px;">
                    <div id="sp-slider-track" style="position:relative;height:2px;background:#e30612;margin:16px 0 20px;cursor:pointer;">
                        <div id="sp-price-track-fill" style="position:absolute;top:0;height:100%;background:#e30612;left:0%;width:100%;"></div>
                        <div id="sp-thumb-min" style="position:absolute;top:50%;width:22px;height:22px;background:#e30612;border-radius:50%;transform:translate(-50%,-50%);cursor:grab;left:0%;z-index:2;"></div>
                        <div id="sp-thumb-max" style="position:absolute;top:50%;width:22px;height:22px;background:#e30612;border-radius:50%;transform:translate(-50%,-50%);cursor:grab;left:100%;z-index:2;"></div>
                    </div>

                    <div style="font-family:Montserrat,sans-serif;font-size:13px;color:#9ca3af;display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                        <span>Price:</span>
                        <span style="font-weight:400;">৳</span>
                        <span id="sp-price-display-min" style="color:#111;font-weight:500;">--</span>
                        <span>-</span>
                        <span style="font-weight:400;">৳</span>
                        <span id="sp-price-display-max" style="color:#111;font-weight:500;">--</span>
                    </div>
                </div>
            </div>

            <!-- Size -->
            <div style="border-bottom:1px solid #f5f5f5;">
                <div
                    onclick="spToggleSection('sp-size-body','sp-size-arrow')"
                    style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:20px 28px;"
                >
                    <span style="font-family:Montserrat,sans-serif;font-size:11px;font-weight:700;color:#111;letter-spacing:1.2px;text-transform:uppercase;">Size</span>
                    <svg id="sp-size-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div id="sp-size-body" style="padding:4px 28px 20px;display:block;">
                    <button
                        id="sp-size-trigger"
                        type="button"
                        onclick="spOpenSizeSelector()"
                        style="width:100%;min-height:48px;padding:0 16px;border:1.5px solid #e5e7eb;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;font-family:Montserrat,sans-serif;font-size:13px;font-weight:500;color:#111;cursor:pointer;text-align:left;"
                    >
                        <span id="sp-size-trigger-label">Select Size</span>

                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <div
                        id="sp-size-trigger-summary"
                        style="display:none;margin-top:8px;font-family:Montserrat,sans-serif;font-size:11px;font-weight:500;color:#6b7280;letter-spacing:.2px;"
                    ></div>
                </div>
            </div>

        </div>

        <div style="padding:16px 28px;border-top:1px solid #f3f4f6;">
            <button
                onclick="spApplyFilters()"
                style="width:100%;height:50px;background:#e30612;color:#fff;border:none;border-radius:10px;font-family:Montserrat,sans-serif;font-size:13px;font-weight:600;cursor:pointer;letter-spacing:.5px;"
                onmouseover="this.style.background='#c00510'"
                onmouseout="this.style.background='#e30612'"
            >
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Size selector overlay -->
    <div
        id="sp-size-selector-overlay"
        onclick="spCloseSizeSelector()"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10000;"
    ></div>

    <!-- Size selector modal -->
    <div
        id="sp-size-selector-modal"
        style="display:none;position:fixed;background:#fff;z-index:10001;box-shadow:0 24px 80px rgba(15,23,42,.2);overflow:hidden;"
    >
        <div style="display:flex;align-items:center;justify-content:space-between;padding:24px 24px 16px;border-bottom:1px solid #f3f4f6;gap:16px;">
            <span style="font-family:Montserrat,sans-serif;font-size:20px;font-weight:500;color:#111;">Select Size</span>

            <button
                type="button"
                onclick="spCloseSizeSelector()"
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
                    id="sp-size-search"
                    type="text"
                    placeholder="Search Size"
                    oninput="spFilterSizeOptions(this.value)"
                    style="width:100%;height:48px;border:1.5px solid #e5e7eb;border-radius:10px;padding:0 16px 0 46px;font-family:Montserrat,sans-serif;font-size:14px;color:#111;outline:none;background:#fff;"
                >
            </div>

            <div id="sp-size-selector-scroll" style="margin-top:18px;overflow-y:auto;">
                <div id="sp-size-selector-grid" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;"></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (window.innerWidth <= 768) {
                var grid = document.getElementById('sp-product-grid');
                var skeleton = document.getElementById('sp-skeleton-grid');

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

    @pushOnce('scripts')
        <script>
            (function () {
                var SEARCH_QUERY     = @json($query ?? '');
                var SUGGEST_VALUE    = @json(request()->query('suggest'));
                var PRODUCTS_API_URL = @json(route('shop.api.products.index'));
                var PRICE_RANGE_URL  = @json(route('shop.api.categories.price_range'));
                var CART_STORE_URL   = '/api/checkout/cart';
                var EMPTY_TEXT       = @json(trans('shop::app.categories.view.empty'));
                var FILTER_ATTRIBUTES_API_URL    = @json(route('shop.api.categories.attributes'));
                var FILTER_ATTRIBUTE_OPTIONS_URL = @json(route('shop.api.categories.attribute_options', ['attribute_id' => '__ATTRIBUTE_ID__']));

                var state = {
                    page: 1,
                    lastPage: 1,
                    loading: false,
                    sort: '',
                    filters: {},
                };

                var selectedSizes  = {};
                var selectedColors = {};

                var sizeOptions = [
                    { id: '1-2', label: '1-2' }, { id: '2', label: '2' }, { id: '2-3', label: '2-3' },
                    { id: '3', label: '3' }, { id: '3-4', label: '3-4' }, { id: '4', label: '4' },
                    { id: '4-5', label: '4-5' }, { id: '5', label: '5' }, { id: '5-6', label: '5-6' },
                    { id: '6', label: '6' }, { id: '6-7', label: '6-7' }, { id: '6-8', label: '6-8' },
                    { id: '7', label: '7' }, { id: '7-8', label: '7-8' }, { id: '8', label: '8' },
                    { id: '8-9', label: '8-9' }, { id: '9', label: '9' }, { id: '9-10', label: '9-10' },
                    { id: '10', label: '10' }, { id: '10-11', label: '10-11' }, { id: '11', label: '11' },
                    { id: '11-12', label: '11-12' }, { id: '12', label: '12' }, { id: '12-13', label: '12-13' },
                    { id: '13', label: '13' }, { id: '13-14', label: '13-14' }, { id: '14', label: '14' },
                    { id: '14-15', label: '14-15' }, { id: '16', label: '16' }, { id: '20', label: '20' },
                    { id: '22', label: '22' }, { id: '24', label: '24' }, { id: '26', label: '26' },
                    { id: '28', label: '28' }, { id: '30', label: '30' }, { id: '32', label: '32' },
                    { id: '34', label: '34' }, { id: '36', label: '36' }, { id: '38', label: '38' },
                    { id: '40', label: '40' }, { id: '42', label: '42' }, { id: '44', label: '44' },
                    { id: '46', label: '46' }, { id: '48', label: '48' }, { id: '50', label: '50' },
                    { id: '52', label: '52' }, { id: '54', label: '54' }, { id: '56', label: '56' },
                    { id: 'Free', label: 'Free' }, { id: 'L', label: 'L' }, { id: 'M', label: 'M' },
                    { id: 'S', label: 'S' }, { id: 'semi-stitched', label: 'semi-stitched' },
                    { id: 'Unstitched', label: 'Unstitched' }, { id: 'XL', label: 'XL' }, { id: 'XXL', label: 'XXL' },
                ];

                var sizeOptionsPromise    = null;
                var sizeSearchTerm        = '';
                var isLoadingSizeOptions  = false;
                var PRICE_MIN             = 0;
                var PRICE_MAX             = 100;
                var PRICE_STEP            = 10;
                var sliderVal             = { min: 0, max: 100 };

                /* ── helpers ───────────────────────────────────────── */
                function esc(value) {
                    if (!value) { return ''; }

                    return String(value)
                        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                }

                function normalizePriceValue(value) {
                    return Math.round(Number(value || 0) * 100) / 100;
                }

                function formatPriceValue(value) {
                    var n = normalizePriceValue(value);

                    return Number.isInteger(n) ? n.toLocaleString() : n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                function updatePriceStep() {
                    var span = Math.max(PRICE_MAX - PRICE_MIN, 0);
                    PRICE_STEP = span <= 0 ? 0 : Math.min(10, span);
                }

                function setPriceBounds(minPrice, maxPrice) {
                    PRICE_MIN = normalizePriceValue(minPrice);
                    PRICE_MAX = Math.max(PRICE_MIN, normalizePriceValue(maxPrice));
                    updatePriceStep();
                    sliderVal.min = PRICE_MIN;
                    sliderVal.max = PRICE_MAX;
                    spRenderSlider();
                }

                /* ── responsive grid ───────────────────────────────── */
                function applyResponsiveGrid() {
                    var grid     = document.getElementById('sp-product-grid');
                    var skeleton = document.getElementById('sp-skeleton-grid');
                    var mobile   = window.innerWidth <= 768;
                    var cols     = mobile ? 'repeat(2,1fr)' : 'repeat(auto-fill,minmax(220px,1fr))';
                    var gap      = mobile ? '10px' : '24px';

                    if (grid)     { grid.style.gridTemplateColumns = cols; grid.style.gap = gap; }
                    if (skeleton) { skeleton.style.gridTemplateColumns = cols; skeleton.style.gap = gap; }
                }

                /* ── product query ─────────────────────────────────── */
                function buildProductQuery() {
                    var params = new URLSearchParams();
                    params.append('limit', '8');
                    params.append('page', String(state.page));

                    if (SEARCH_QUERY) {
                        params.append('query', SEARCH_QUERY);
                    }

                    if (SUGGEST_VALUE !== null && SUGGEST_VALUE !== undefined && SUGGEST_VALUE !== '') {
                        params.append('suggest', SUGGEST_VALUE);
                    }

                    if (state.sort)          { params.append('sort',  state.sort); }
                    if (state.filters.price) { params.append('price', state.filters.price); }
                    if (state.filters.color) { params.append('color', state.filters.color); }
                    if (state.filters.size)  { params.append('size',  state.filters.size); }

                    return params.toString();
                }

                /* ── fetch products ────────────────────────────────── */
                function loadProducts(replace) {
                    if (state.loading) { return; }

                    var loading  = document.getElementById('sp-loading');
                    var empty    = document.getElementById('sp-empty');
                    var grid     = document.getElementById('sp-product-grid');
                    var spinner  = document.getElementById('sp-infinite-scroll-spinner');

                    state.loading = true;

                    if (replace) {
                        loading.style.display = 'block';
                        empty.style.display = 'none';
                        spinner.style.display = 'none';
                    } else {
                        spinner.style.display = 'flex';
                    }

                    fetch(PRODUCTS_API_URL + '?' + buildProductQuery(), {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    })
                        .then(function (res) {
                            if (!res.ok) { throw new Error('HTTP ' + res.status); }

                            return res.json();
                        })
                        .then(function (data) {
                            loading.style.display = 'none';
                            state.loading = false;
                            state.lastPage = data.meta && data.meta.last_page ? data.meta.last_page : 1;

                            var products = data.data || [];

                            if (replace) { grid.innerHTML = ''; }

                            if (replace && products.length === 0) {
                                empty.style.display = 'block';
                                empty.textContent = EMPTY_TEXT;
                                spinner.style.display = 'none';
                                return;
                            }

                            empty.style.display = 'none';
                            state.page++;

                            products.forEach(function (product) {
                                grid.insertAdjacentHTML('beforeend', buildCard(product));
                            });

                            spinner.style.display = state.page - 1 < state.lastPage ? 'flex' : 'none';
                            applyResponsiveGrid();
                        })
                        .catch(function (error) {
                            loading.style.display = 'none';
                            state.loading = false;
                            console.error('[SearchPageGrid]', error);
                        })
                        .finally(function () {
                            spinner.style.display = 'none';
                        });
                }

                window.spLoadMoreProducts = function () {
                    if (state.page - 1 >= state.lastPage || state.loading) { return; }

                    loadProducts(false);
                };

                window.spApplySort = function (value) {
                    state.sort = value;
                    state.page = 1;
                    state.lastPage = 1;
                    state.loading = false;
                    document.getElementById('sp-product-grid').innerHTML = '';
                    loadProducts(true);
                };

                /* ── product card ──────────────────────────────────── */
                function buildCard(product) {
                    var isMobile       = window.innerWidth <= 768;
                    var primaryImage   = product.base_image && product.base_image.medium_image_url ? product.base_image.medium_image_url : '';
                    var secondaryImage = product.images && product.images[1] && product.images[1].medium_image_url ? product.images[1].medium_image_url : primaryImage;
                    var url            = '/' + product.url_key;
                    var price          = product.min_price || '';
                    var isSaleable     = !!product.is_saleable;
                    var cardId         = 'sp-' + product.id;
                    
                    var badge          = product.is_new ? '<div style="position:absolute;top:10px;left:10px;z-index:4;background:#ef4444;color:#fff;font-size:10px;font-weight:600;padding:3px 8px;border-radius:9999px;font-family:Montserrat,sans-serif;letter-spacing:.3px;">New</div>' : '';

                    var images = primaryImage
                        ? '<div style="position:relative;width:100%;aspect-ratio:2/3;overflow:hidden;">'
                            + '<img data-role="img1" src="' + primaryImage + '" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:opacity 1s ease,transform 1s ease;opacity:1;transform:scale(1);">'
                            + '<img data-role="img2" src="' + secondaryImage + '" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:opacity 1s ease,transform 1s ease;opacity:0;transform:scale(1);">'
                            + '</div>'
                        : '<div style="width:100%;aspect-ratio:2/3;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:12px;font-family:Montserrat,sans-serif;">No image</div>';

                    var cta;
                    var isConfigurable = product.type === 'configurable';

                    if (isMobile) {
                        var cartIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
                        var eyeIcon  = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                        
                        // For configurable products, always show view icon and navigate to product page
                        var showCartButton = isSaleable && !isConfigurable;
                        
                        cta = '<button data-role="cta" '
                            + 'onclick="event.stopPropagation();event.preventDefault();' + (showCartButton ? 'spAddToCartById(this,' + product.id + ')' : 'spGoTo(event,\'' + url + '\')') + '" '
                            + 'ontouchend="event.stopPropagation();event.preventDefault();' + (showCartButton ? 'spAddToCartById(this,' + product.id + ')' : 'spGoTo(event,\'' + url + '\')') + '" '
                            + 'style="position:absolute;bottom:10px;right:10px;z-index:4;width:44px;height:44px;background:#111;color:#fff;border:none;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:auto;-webkit-tap-highlight-color:transparent;">'
                            + (showCartButton ? cartIcon : eyeIcon)
                            + '</button>';
                    } else {
                        // For configurable products, always show "View Product" instead of "Add to cart"
                        var label    = (isSaleable && !isConfigurable) ? 'Add to cart' : 'View product';
                        var hoverIcon = (isSaleable && !isConfigurable)
                            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>'
                            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                        cta = '<div style="position:absolute;bottom:12px;left:0;right:0;display:flex;justify-content:center;pointer-events:auto;">'
                            + '<button data-role="cta" onclick="event.stopPropagation();event.preventDefault();' + ((isSaleable && !isConfigurable) ? 'spAddToCart(event,' + product.id + ')' : 'spGoTo(event,\'' + url + '\')') + '" '
                            + 'style="display:inline-flex;align-items:center;justify-content:center;height:44px;padding:0 28px;background:#111;color:#fff;border:none;outline:none;border-radius:5px;cursor:pointer;transform:translateY(0);transition:transform .3s ease;overflow:hidden;position:relative;min-width:140px;pointer-events:auto;-webkit-tap-highlight-color:transparent;">'
                            + '<span data-role="btn-text" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;font-family:Montserrat,sans-serif;letter-spacing:.2px;transition:transform .28s ease,opacity .28s ease;transform:translateY(0);opacity:1;z-index:1;">' + label + '</span>'
                            + '<span data-role="btn-icon" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#111;transition:transform .28s ease,opacity .28s ease;transform:translateY(100%);opacity:1;z-index:2;">' + hoverIcon + '</span>'
                            + '</button></div>';
                    }

                    setTimeout(function () { attachCardHover(cardId); }, 0);

                    if (isMobile) {
                        return '<div id="' + cardId + '" data-product-id="' + product.id + '" onclick="spGoTo(event,\'' + url + '\')" style="cursor:pointer;font-family:Montserrat,sans-serif;">'
                            + '<div style="position:relative;border-radius:6px;overflow:hidden;background:#f9f9f9;">' + badge + images + cta + '</div>'
                            + '<div style="padding:6px 2px 0;">'
                            + '<p style="font-size:11px;font-weight:400;color:#111827;margin:0 0 3px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">' + esc(product.name) + '</p>'
                            + '<p style="font-size:12px;font-weight:600;color:#111;margin:0;">' + price + '</p>'
                            + '</div></div>';
                    }

                    return '<div id="' + cardId + '" data-product-id="' + product.id + '" onclick="spGoTo(event,\'' + url + '\')" style="cursor:pointer;font-family:Montserrat,sans-serif;">'
                        + '<div style="position:relative;border-radius:8px;overflow:hidden;background:#f9f9f9;">' + badge + images + cta + '</div>'
                        + '<div style="padding:10px 2px 0;">'
                        + '<p style="font-size:13px;font-weight:400;color:#111827;margin:0 0 4px;line-height:1.4;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + esc(product.name) + '</p>'
                        + '<p style="font-size:13px;color:#6b7280;margin:0;">' + price + '</p>'
                        + '</div></div>';
                }

                function attachCardHover(cardId) {
                    var card = document.getElementById(cardId);
                    if (!card || card.dataset.hoverBound === 'true') { return; }

                    card.dataset.hoverBound = 'true';

                    var img1    = card.querySelector('[data-role="img1"]');
                    var img2    = card.querySelector('[data-role="img2"]');
                    var btn     = card.querySelector('[data-role="cta"]');
                    var btnText = card.querySelector('[data-role="btn-text"]');
                    var btnIcon = card.querySelector('[data-role="btn-icon"]');

                    card.addEventListener('mouseenter', function () {
                        if (img1) { img1.style.opacity = '0'; img1.style.transform = 'scale(1.05)'; }
                        if (img2) { img2.style.opacity = '1'; img2.style.transform = 'scale(1.05)'; }
                        if (btn)  { btn.style.transform = 'translateY(-6px)'; }
                    });

                    card.addEventListener('mouseleave', function () {
                        if (img1) { img1.style.opacity = '1'; img1.style.transform = 'scale(1)'; }
                        if (img2) { img2.style.opacity = '0'; img2.style.transform = 'scale(1)'; }
                        if (btn)  { btn.style.transform = 'translateY(0)'; }
                        if (btnText) { btnText.style.transform = 'translateY(0)'; btnText.style.opacity = '1'; }
                        if (btnIcon) { btnIcon.style.transform = 'translateY(100%)'; }
                    });

                    if (btn) {
                        btn.addEventListener('mouseenter', function (e) {
                            e.stopPropagation();
                            if (btnText) { btnText.style.transform = 'translateY(-100%)'; btnText.style.opacity = '0'; }
                            if (btnIcon) { btnIcon.style.transform = 'translateY(0)'; }
                        });

                        btn.addEventListener('mouseleave', function (e) {
                            e.stopPropagation();
                            if (btnText) { btnText.style.transform = 'translateY(0)'; btnText.style.opacity = '1'; }
                            if (btnIcon) { btnIcon.style.transform = 'translateY(100%)'; }
                        });
                    }
                }

                window.spGoTo = function (event, url) {
                    if (event.target.closest('[data-role="cta"]')) { return; }

                    window.location.href = url;
                };

                /* ── add to cart ───────────────────────────────────── */
                window.spAddToCart = function (event, id) {
                    if (event && event.preventDefault) { event.preventDefault(); }

                    var button   = event && (event.currentTarget || event.target);
                    if (button && button.tagName !== 'BUTTON') { button = button.closest('button') || button; }

                    var buttonText = button ? button.querySelector('[data-role="btn-text"]') : null;

                    if (button) { button.disabled = true; button.style.opacity = '0.6'; }
                    if (buttonText) { buttonText.textContent = 'Adding...'; }

                    fetch(CART_STORE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json', Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ product_id: id, quantity: 1 }),
                    })
                        .then(function (res) { if (!res.ok) { throw new Error('HTTP ' + res.status); } return res.json(); })
                        .then(function () {
                            if (button) { button.style.background = '#22c55e'; button.style.opacity = '1'; }
                            if (buttonText) { buttonText.textContent = 'Added!'; }
                            window.dispatchEvent(new CustomEvent('cart-updated'));

                            setTimeout(function () {
                                if (button) { button.style.background = '#111'; button.style.opacity = '1'; button.disabled = false; }
                                if (buttonText) { buttonText.textContent = 'Add to cart'; }
                            }, 1500);
                        })
                        .catch(function () {
                            if (button) { button.style.background = '#ef4444'; button.style.opacity = '1'; }
                            if (buttonText) { buttonText.textContent = 'Error'; }

                            setTimeout(function () {
                                if (button) { button.style.background = '#111'; button.disabled = false; }
                                if (buttonText) { buttonText.textContent = 'Add to cart'; }
                            }, 2000);
                        });
                };

                window.spAddToCartById = function (button, id) {
                    if (!button) { return; }

                    button.disabled = true;
                    button.style.opacity = '0.6';

                    var cartSvg  = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
                    var checkSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

                    fetch(CART_STORE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json', Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ product_id: id, quantity: 1 }),
                    })
                        .then(function (res) { if (!res.ok) { throw new Error('HTTP ' + res.status); } return res.json(); })
                        .then(function () {
                            button.style.background = '#22c55e'; button.style.opacity = '1'; button.innerHTML = checkSvg;
                            window.dispatchEvent(new CustomEvent('cart-updated'));

                            setTimeout(function () { button.style.background = '#111'; button.innerHTML = cartSvg; button.disabled = false; }, 1500);
                        })
                        .catch(function () {
                            button.style.background = '#ef4444'; button.style.opacity = '1';

                            setTimeout(function () { button.style.background = '#111'; button.innerHTML = cartSvg; button.disabled = false; }, 2000);
                        });
                };

                /* ── drawer ────────────────────────────────────────── */
                /* ── Color Options (dynamic) ── */
                var colorOptionsPromise = null;
                var colorOptions = [];

                var SP_FALLBACK_COLOR_MAP = {
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
                    return SP_FALLBACK_COLOR_MAP[name] || SP_FALLBACK_COLOR_MAP[swatch.toLowerCase()] || '#cccccc';
                }

                function spRenderColorOptions() {
                    var container = document.getElementById('sp-color-body');
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
                        return '<div id="sp-color-row-' + id + '" onclick="spToggleColor(this,\'' + id + '\')"'
                            + ' style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;border:1.5px solid ' + (isActive ? '#e5e7eb' : 'transparent') + ';background:' + (isActive ? '#fafafa' : 'transparent') + ';" >'
                            + '<div id="sp-cb-color-' + id + '" style="width:16px;height:16px;border-radius:4px;border:1.5px solid ' + (isActive ? '#e30612' : '#d1d5db') + ';flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;background:' + (isActive ? '#e30612' : '') + ';">' + (isActive ? '<svg width="9" height="9" viewBox="0 0 12 10" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 5 4.5 9 11 1"/></svg>' : '') + '</div>'
                            + '<div style="width:18px;height:18px;border-radius:50%;flex-shrink:0;background:' + hex + ';' + swatchBorder + '"></div>'
                            + '<span style="font-family:Montserrat,sans-serif;font-size:12px;color:#374151;font-weight:' + (isActive ? '500' : '400') + ';">' + label + '</span>'
                            + '</div>';
                    }).join('');
                }

                function spFetchColorOptions() {
                    if (colorOptionsPromise) {
                        spRenderColorOptions();
                        return colorOptionsPromise;
                    }

                    var container = document.getElementById('sp-color-body');
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
                        spRenderColorOptions();
                    })
                    .catch(function (err) {
                        console.error('[ColorFilter]', err);
                        colorOptionsPromise = null;
                        var container = document.getElementById('sp-color-body');
                        if (container) container.innerHTML = '';
                    });

                    return colorOptionsPromise;
                }

                window.spOpenDrawer = function () {
                    document.getElementById('sp-drawer-overlay').style.display = 'block';
                    document.getElementById('sp-filter-drawer').style.transform = 'translateX(0)';
                    document.body.style.overflow = 'hidden';
                    setTimeout(function () { spInitSlider(); spRenderSlider(); }, 50);
                    spFetchSizeOptions();
                    spRenderSizeTrigger();
                    spFetchColorOptions();
                };

                window.spCloseDrawer = function () {
                    spCloseSizeSelector();
                    document.getElementById('sp-drawer-overlay').style.display = 'none';
                    document.getElementById('sp-filter-drawer').style.transform = 'translateX(-100%)';
                    document.body.style.overflow = '';
                };

                window.spToggleSection = function (bodyId, arrowId) {
                    var body = document.getElementById(bodyId);
                    var arrow = document.getElementById(arrowId);
                    if (!body) { return; }
                    var isOpen = body.style.display !== 'none';
                    body.style.display = isOpen ? 'none' : '';
                    if (arrow) { arrow.style.transform = isOpen ? 'rotate(-90deg)' : 'rotate(0deg)'; }
                };

                /* ── price slider ──────────────────────────────────── */
                function spRenderSlider() {
                    var rangeSpan    = PRICE_MAX - PRICE_MIN || 1;
                    var minPercent   = (sliderVal.min - PRICE_MIN) / rangeSpan * 100;
                    var maxPercent   = (sliderVal.max - PRICE_MIN) / rangeSpan * 100;
                    var fill         = document.getElementById('sp-price-track-fill');
                    var thumbMin     = document.getElementById('sp-thumb-min');
                    var thumbMax     = document.getElementById('sp-thumb-max');
                    var displayMin   = document.getElementById('sp-price-display-min');
                    var displayMax   = document.getElementById('sp-price-display-max');

                    if (fill)       { fill.style.left = minPercent + '%'; fill.style.width = maxPercent - minPercent + '%'; }
                    if (thumbMin)   { thumbMin.style.left = minPercent + '%'; }
                    if (thumbMax)   { thumbMax.style.left = maxPercent + '%'; }
                    if (displayMin) { displayMin.textContent = formatPriceValue(sliderVal.min); }
                    if (displayMax) { displayMax.textContent = formatPriceValue(sliderVal.max); }
                }

                window.spRenderSlider = spRenderSlider;

                function spInitSlider() {
                    var track    = document.getElementById('sp-slider-track');
                    var thumbMin = document.getElementById('sp-thumb-min');
                    var thumbMax = document.getElementById('sp-thumb-max');

                    if (!track || !thumbMin || !thumbMax || track.dataset.initialized === 'true') { return; }

                    track.dataset.initialized = 'true';

                    function getPercent(clientX) {
                        var rect = track.getBoundingClientRect();

                        return Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
                    }

                    function snap(value, step) {
                        if (step <= 0) { return normalizePriceValue(value); }

                        return normalizePriceValue(Math.round((value - PRICE_MIN) / step) * step + PRICE_MIN);
                    }

                    function attachDrag(thumb, which) {
                        function onMove(e) {
                            var clientX = e.touches ? e.touches[0].clientX : e.clientX;
                            var raw     = PRICE_MIN + getPercent(clientX) * (PRICE_MAX - PRICE_MIN);
                            var snapped = Math.max(PRICE_MIN, Math.min(PRICE_MAX, snap(raw, PRICE_STEP)));

                            if (which === 'min') {
                                sliderVal.min = PRICE_STEP > 0 ? Math.min(snapped, sliderVal.max - PRICE_STEP) : PRICE_MIN;
                            } else {
                                sliderVal.max = PRICE_STEP > 0 ? Math.max(snapped, sliderVal.min + PRICE_STEP) : PRICE_MAX;
                            }

                            spRenderSlider();
                        }

                        function onUp() {
                            document.removeEventListener('mousemove', onMove);
                            document.removeEventListener('mouseup', onUp);
                            document.removeEventListener('touchmove', onMove);
                            document.removeEventListener('touchend', onUp);
                            thumb.style.cursor = 'grab';
                        }

                        thumb.addEventListener('mousedown', function (e) {
                            e.preventDefault(); e.stopPropagation(); thumb.style.cursor = 'grabbing';
                            document.addEventListener('mousemove', onMove);
                            document.addEventListener('mouseup', onUp);
                        });

                        thumb.addEventListener('touchstart', function (e) {
                            e.preventDefault(); e.stopPropagation();
                            document.addEventListener('touchmove', onMove, { passive: false });
                            document.addEventListener('touchend', onUp);
                        }, { passive: false });
                    }

                    attachDrag(thumbMin, 'min');
                    attachDrag(thumbMax, 'max');
                    spRenderSlider();
                }

                window.spInitSlider = spInitSlider;

                /* ── size selector ─────────────────────────────────── */
                function spRenderSizeTrigger() {
                    var label   = document.getElementById('sp-size-trigger-label');
                    var summary = document.getElementById('sp-size-trigger-summary');
                    var count   = Object.keys(selectedSizes).length;

                    if (label)   { label.textContent = count ? count + (count === 1 ? ' size selected' : ' sizes selected') : 'Select Size'; }
                    if (summary) {
                        if (count) { summary.style.display = 'block'; summary.textContent = count + (count === 1 ? ' size selected' : ' sizes selected'); }
                        else       { summary.style.display = 'none'; summary.textContent = ''; }
                    }
                }

                function spRenderSizeOptions() {
                    var grid = document.getElementById('sp-size-selector-grid');
                    if (!grid) { return; }

                    if (isLoadingSizeOptions) {
                        grid.innerHTML = '<div style="grid-column:1 / -1;padding:32px 0;text-align:center;font-family:Montserrat,sans-serif;font-size:13px;color:#6b7280;">Loading sizes...</div>';
                        return;
                    }

                    var options = sizeSearchTerm
                        ? sizeOptions.filter(function (o) { return String(o.label || '').toLowerCase().indexOf(sizeSearchTerm) !== -1; })
                        : sizeOptions;

                    if (!options.length) {
                        grid.innerHTML = '<div style="grid-column:1 / -1;padding:32px 0;text-align:center;font-family:Montserrat,sans-serif;font-size:13px;color:#6b7280;">No sizes found</div>';
                        return;
                    }

                    grid.innerHTML = options.map(function (o) {
                        var active = !!selectedSizes[o.id];

                        return '<button type="button" onclick="spToggleSize(\'' + esc(o.id) + '\')" style="min-height:56px;padding:12px 10px;border:1.5px solid ' + (active ? '#111' : '#e5e7eb') + ';border-radius:10px;background:' + (active ? '#e30612' : '#fff') + ';font-family:Montserrat,sans-serif;font-size:13px;font-weight:500;color:' + (active ? '#fff' : '#111') + ';cursor:pointer;line-height:1.3;word-break:break-word;overflow-wrap:break-word;">' + esc(o.label) + '</button>';
                    }).join('');
                }

                function spApplySizeModalLayout() {
                    var modal    = document.getElementById('sp-size-selector-modal');
                    var scroller = document.getElementById('sp-size-selector-scroll');
                    if (!modal || !scroller) { return; }

                    if (window.innerWidth <= 768) {
                        modal.style.cssText += ';left:0;right:0;bottom:0;top:auto;width:100%;max-width:100%;transform:none;border-radius:24px 24px 0 0;max-height:78vh;';
                        scroller.style.maxHeight = '52vh';
                    } else {
                        modal.style.cssText += ';left:50%;right:auto;bottom:auto;top:50%;width:min(720px,calc(100vw - 48px));max-width:720px;transform:translate(-50%,-50%);border-radius:24px;max-height:80vh;';
                        scroller.style.maxHeight = '420px';
                    }
                }

                function spFetchSizeOptions() {
                    if (sizeOptionsPromise) { return sizeOptionsPromise; }

                    isLoadingSizeOptions = true;
                    spRenderSizeOptions();
                    sizeOptionsPromise = Promise.resolve(sizeOptions).finally(function () {
                        isLoadingSizeOptions = false;
                        spRenderSizeTrigger();
                        spRenderSizeOptions();
                    });

                    return sizeOptionsPromise;
                }

                window.spOpenSizeSelector = function () {
                    var input = document.getElementById('sp-size-search');
                    sizeSearchTerm = '';
                    if (input) { input.value = ''; }
                    spApplySizeModalLayout();
                    spRenderSizeOptions();
                    document.getElementById('sp-size-selector-overlay').style.display = 'block';
                    document.getElementById('sp-size-selector-modal').style.display = 'block';
                    spFetchSizeOptions().finally(function () {
                        spApplySizeModalLayout();
                        spRenderSizeOptions();
                        setTimeout(function () { if (input) { input.focus(); } }, 50);
                    });
                };

                window.spCloseSizeSelector = function () {
                    document.getElementById('sp-size-selector-overlay').style.display = 'none';
                    document.getElementById('sp-size-selector-modal').style.display = 'none';
                };

                window.spFilterSizeOptions = function (value) {
                    sizeSearchTerm = String(value || '').toLowerCase();
                    spRenderSizeOptions();
                };

                window.spToggleSize = function (id) {
                    if (selectedSizes[id]) { delete selectedSizes[id]; } else { selectedSizes[id] = true; }

                    spRenderSizeTrigger();
                    spRenderSizeOptions();
                };

                /* ── color toggle ──────────────────────────────────── */
                window.spToggleColor = function (row, id) {
                    var checkbox = document.getElementById('sp-cb-color-' + id);
                    var label    = row.querySelector('span');

                    if (selectedColors[id]) {
                        delete selectedColors[id];
                        row.style.borderColor = 'transparent'; row.style.background = 'transparent';
                        if (checkbox) { checkbox.style.background = ''; checkbox.style.borderColor = '#d1d5db'; checkbox.innerHTML = ''; }
                        if (label)    { label.style.fontWeight = '400'; }
                    } else {
                        selectedColors[id] = true;
                        row.style.borderColor = '#e5e7eb'; row.style.background = '#fafafa';
                        if (checkbox) { checkbox.style.background = '#e30612'; checkbox.style.borderColor = '#111'; checkbox.innerHTML = '<svg width="9" height="9" viewBox="0 0 12 10" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 5 4.5 9 11 1"></polyline></svg>'; }
                        if (label)    { label.style.fontWeight = '500'; }
                    }
                };

                /* ── apply / clear filters ─────────────────────────── */
                function updateFilterBadge() {
                    var badge = document.getElementById('sp-filter-count-badge');
                    var count = Object.keys(state.filters).length;
                    if (!badge) { return; }

                    if (count > 0) { badge.style.display = 'inline-block'; badge.textContent = count; }
                    else           { badge.style.display = 'none'; }
                }

                window.spApplyFilters = function () {
                    state.filters = {};

                    var sizeIds  = Object.keys(selectedSizes);
                    if (sizeIds.length)  { state.filters.size  = sizeIds.join(','); }

                    var colorIds = Object.keys(selectedColors);
                    if (colorIds.length) { state.filters.color = colorIds.join(','); }

                    if (sliderVal.min > PRICE_MIN || sliderVal.max < PRICE_MAX) {
                        state.filters.price = sliderVal.min + ',' + sliderVal.max;
                    }

                    updateFilterBadge();
                    spCloseDrawer();
                    state.page = 1; state.lastPage = 1; state.loading = false;
                    document.getElementById('sp-product-grid').innerHTML = '';
                    loadProducts(true);
                };

                window.spClearFilters = function () {
                    state.filters   = {};
                    selectedSizes   = {};
                    selectedColors  = {};
                    sizeSearchTerm  = '';

                    var sizeInput = document.getElementById('sp-size-search');
                    if (sizeInput) { sizeInput.value = ''; }

                    spRenderSizeTrigger();
                    spRenderSizeOptions();

                    document.querySelectorAll('[id^="sp-color-row-"]').forEach(function (row) {
                        row.style.borderColor = 'transparent'; row.style.background = 'transparent';
                        var checkbox = row.querySelector('[id^="sp-cb-color-"]');
                        if (checkbox) { checkbox.style.background = ''; checkbox.style.borderColor = '#d1d5db'; checkbox.innerHTML = ''; }
                        var lbl = row.querySelector('span');
                        if (lbl) { lbl.style.fontWeight = '400'; }
                    });
                    spRenderColorOptions();

                    sliderVal.min = PRICE_MIN;
                    sliderVal.max = PRICE_MAX;
                    spRenderSlider();
                    updateFilterBadge();
                };

                /* ── fetch price range ─────────────────────────────── */
                function fetchPriceRange() {
                    return fetch(PRICE_RANGE_URL, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (res) { if (!res.ok) { throw new Error('HTTP ' + res.status); } return res.json(); })
                        .then(function (data) {
                            var min = data.data && data.data.min_price != null ? data.data.min_price : 0;
                            var max = data.data && data.data.max_price != null ? data.data.max_price : min;
                            setPriceBounds(min, max);
                        })
                        .catch(function (err) { console.error('[SearchPagePriceRange]', err); });
                }

                /* ── boot ──────────────────────────────────────────── */
                function boot() {
                    applyResponsiveGrid();
                    spFetchSizeOptions();
                    spRenderSizeTrigger();
                    spApplySizeModalLayout();

                    fetchPriceRange().finally(function () {
                        spInitSlider();
                        spRenderSlider();
                        loadProducts(true);

                        // Set up IntersectionObserver for infinite scroll
                        var sentinel = document.getElementById('sp-infinite-scroll-sentinel');
                        if (sentinel) {
                            window._spScrollObserver = new IntersectionObserver(function (entries) {
                                if (entries[0].isIntersecting && !state.loading && state.page - 1 < state.lastPage) {
                                    spLoadMoreProducts();
                                }
                            }, { rootMargin: '200px' });
                            window._spScrollObserver.observe(sentinel);
                        }
                    });

                    updateFilterBadge();
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () { setTimeout(boot, 100); });
                } else {
                    setTimeout(boot, 100);
                }

                window.addEventListener('resize', function () {
                    applyResponsiveGrid();
                    spApplySizeModalLayout();
                });
            })();
        </script>
    @endPushOnce
</x-shop::layouts>
