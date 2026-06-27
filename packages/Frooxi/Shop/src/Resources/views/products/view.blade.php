@inject ('reviewHelper', 'Frooxi\Product\Helpers\Review')
@inject ('productViewHelper', 'Frooxi\Product\Helpers\View')

@php
    $avgRatings = $reviewHelper->getAverageRating($product);

    $percentageRatings = $reviewHelper->getPercentageRating($product);

    $customAttributeValues = $productViewHelper->getAdditionalData($product);

    $attributeData = collect($customAttributeValues)->filter(fn ($item) => ! empty($item['value']));

    $shareUrl = route('shop.product_or_category.index', $product->url_key);
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>

    <meta name="keywords" content="{{ $product->meta_keywords }}"/>

    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {!! app('Frooxi\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
    @endif

    <?php $productBaseImage = product_image()->getProductBaseImage($product); ?>

    <meta name="twitter:card" content="summary_large_image" />

    <meta name="twitter:title" content="{{ $product->name }}" />

    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta name="twitter:image:alt" content="" />

    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:type" content="og:product" />

    <meta property="og:title" content="{{ $product->name }}" />

    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endPush

<style>
    .product-price .price-label { display: none; }
    .product-price .final-price,
    .product-price .special-price { font-weight: 500; font-size: 22px; }
    .product-price .regular-price { color: #999; text-decoration: line-through; font-size: 15px; }

    .pdp-actions {
        margin-top: 12px;
        display: flex;
        width: min(100%, 100%);
        flex-direction: column;
        gap: 12px;
    }

    .pdp-primary-action-row,
    .pdp-secondary-action-row {
        display: grid;
        width: 100%;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .pdp-action-button {
        display: inline-flex;
        min-height: 54px;
        width: 100%;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border: none;
        border-radius: 999px;
        padding: 0 20px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.08em;
        line-height: 1;
        text-transform: uppercase;
        color: #fff;
        transition: opacity 0.2s ease;
    }

    .pdp-action-button:hover {
        opacity: 0.92;
    }

    .pdp-action-button:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pdp-action-button svg {
        height: 16px;
        width: 16px;
        flex-shrink: 0;
    }

    .pdp-add-to-bag {
        background: #d63384;
    }

    .pdp-buy-now {
        background: #7b2d42;
    }

    .pdp-review-button {
        background: #1a2b4a;
    }

    .pdp-contact-button {
        background: #111111;
    }

    .pdp-quantity-changer {
        display: inline-flex;
        width: fit-content;
        max-width: max-content;
        align-self: flex-start;
        min-width: 150px;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        border: 1px solid #d7dde5;
        border-radius: 12px;
        padding: 14px 22px;
        color: #546274;
        background: #fff;
    }

    .pdp-quantity-changer .icon-minus,
    .pdp-quantity-changer .icon-plus {
        font-size: 22px;
        color: #667085;
    }

    .pdp-quantity-changer p {
        width: auto;
        min-width: 18px;
        font-size: 18px;
        font-weight: 500;
        color: #111827;
    }

    .pdp-option-heading {
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #374151;
    }

    .pdp-size-options,
    .pdp-color-options {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .pdp-size-option {
        display: inline-flex;
        min-height: 40px;
        min-width: 72px;
        align-items: center;
        justify-content: center;
        border: 1px solid #cfd5dc;
        border-radius: 12px;
        padding: 0 18px;
        font-size: 15px;
        font-weight: 500;
        color: #374151;
        background: #fff;
        transition: all 0.2s ease;
    }

    .pdp-size-option.is-selected {
        border-color: #cbd5e1;
        background: #eef3f8;
        color: #111827;
    }

    .pdp-color-option {
        display: inline-flex;
        height: 34px;
        width: 34px;
        border: 1px solid #d1d5db;
        border-radius: 999px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.5);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .pdp-color-option.is-selected {
        box-shadow: 0 0 0 3px #111827;
        transform: scale(1.04);
    }

    .pdp-review-panel {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .pdp-review-back-button {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 8px;
        border: none;
        background: transparent;
        padding: 0;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #111827;
        cursor: pointer;
    }

    .pdp-review-back-button svg {
        height: 18px;
        width: 18px;
        flex-shrink: 0;
    }

    .pdp-review-panel-content {
        width: 100%;
    }

    @media (max-width: 1180px) {
        .product-top-section {
            flex-direction: column !important;
            gap: 0 !important;
            padding: 0 16px !important;
        }

        .product-col-left,
        .product-col-right {
            position: static !important;
            max-width: 100% !important;
            flex: unset !important;
            width: 100% !important;
        }
    }

    @media (max-width: 640px) {
        .pdp-action-button {
            min-height: 50px;
            padding: 0 14px;
            font-size: 12px;
            letter-spacing: 0.06em;
        }

        .pdp-primary-action-row,
        .pdp-secondary-action-row {
            gap: 10px;
        }

        .pdp-size-option {
            min-width: 68px;
            min-height: 38px;
            padding: 0 14px;
            font-size: 14px;
        }

        .pdp-color-option {
            height: 30px;
            width: 30px;
        }
    }
</style>

<!-- Page Layout -->
<x-shop::layouts :has-feature="false">
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
    </x-slot>

    {!! view_render_event('frooxi.shop.products.view.before', ['product' => $product]) !!}

    <!-- Product Information Vue Component -->
    <v-product>
        <x-shop::shimmer.products.view />
    </v-product>

    <!-- Mobile Tab Section (hidden on desktop) -->
    <div class="mt-6 1180:hidden" style="padding: 0 16px;">
        <div style="display: flex; align-items: center; border-bottom: 1px solid #e5e5e5; padding-bottom: 0; flex-wrap: wrap; gap: 10px 0;">
            <button
                id="pdp-tab-btn-details"
                data-tab="details"
                onclick="switchProductTab('details')"
                style="font-size: 13px; font-weight: 300; letter-spacing: 0.05em; font-family: Montserrat, sans-serif; text-transform: uppercase; color: #1a1a1a; text-decoration: underline; text-underline-offset: 6px; text-decoration-thickness: 2px; background: none; border: none; cursor: pointer; padding: 10px 0; outline: none;"
            >DETAILS</button>
            <span style="color: #ccc; margin: 0 10px; font-size: 13px;">|</span>
            <button
                id="pdp-tab-btn-delivery"
                data-tab="delivery"
                onclick="switchProductTab('delivery')"
                style="font-size: 13px; font-weight: 300; letter-spacing: 0.05em; font-family: Montserrat, sans-serif; text-transform: uppercase; color: #888; text-decoration: none; background: none; border: none; cursor: pointer; padding: 10px 0; outline: none;"
            >DELIVERY</button>
            <span style="color: #ccc; margin: 0 10px; font-size: 13px;">|</span>
            <button
                id="pdp-tab-btn-care"
                data-tab="care"
                onclick="switchProductTab('care')"
                style="font-size: 13px; font-weight: 300; letter-spacing: 0.05em; font-family: Montserrat, sans-serif; text-transform: uppercase; color: #888; text-decoration: none; background: none; border: none; cursor: pointer; padding: 10px 0; outline: none;"
            >INSTRUCTIONS</button>
        </div>

        <div id="pdp-tab-content-details" data-tab-panel="details" style="padding: 16px 0; font-size: 14px; line-height: 1.7; color: #555;">
            {!! $product->description !!}
        </div>

        <div id="pdp-tab-content-delivery" data-tab-panel="delivery" style="display: none; padding: 16px 0; font-size: 14px; line-height: 1.7; color: #555;">
            @if (! empty($product->delivery_timeline))
                {!! nl2br(e($product->delivery_timeline)) !!}
            @else
                <p style="color: #aaa;">No delivery information available.</p>
            @endif
        </div>

        <div id="pdp-tab-content-care" data-tab-panel="care" style="display: none; padding: 16px 0; font-size: 14px; line-height: 1.7; color: #555;">
            @if (! empty($product->care_instructions))
                {!! nl2br(e($product->care_instructions)) !!}
            @else
                <p style="color: #aaa;">No care instructions available.</p>
            @endif
        </div>
    </div>

    <script>
        function toggleDesktopAccordion(headerEl) {
            var contentEl = headerEl.nextElementSibling;
            var icon = headerEl.querySelector('span');
            if (contentEl.style.maxHeight && contentEl.style.maxHeight !== '0px') {
                contentEl.style.maxHeight = '0';
                icon.textContent = '+';
            } else {
                contentEl.style.maxHeight = contentEl.scrollHeight + 'px';
                icon.textContent = '\u2212';
            }
        }

        function switchProductTab(tabName) {
            var tabs = ['details', 'delivery', 'care'];
            tabs.forEach(function(tab) {
                var btn = document.getElementById('pdp-tab-btn-' + tab);
                var panel = document.getElementById('pdp-tab-content-' + tab);
                if (!btn || !panel) return;
                if (tab === tabName) {
                    btn.style.color = '#1a1a1a';
                    btn.style.textDecoration = 'underline';
                    btn.style.textUnderlineOffset = '6px';
                    btn.style.textDecorationThickness = '2px';
                    panel.style.display = 'block';
                } else {
                    btn.style.color = '#888';
                    btn.style.textDecoration = 'none';
                    panel.style.display = 'none';
                }
            });
        }
    </script>

    <v-product-associations />

    {!! view_render_event('frooxi.shop.products.view.after', ['product' => $product]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-product-template"
        >
            <x-shop::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    ref="formData"
                    @submit="handleSubmit($event, addToCart)"
                >
                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <input
                        type="hidden"
                        name="is_buy_now"
                        v-model="is_buy_now"
                    >

                    <!-- Two-Column Hero Section -->
                    <div
                        class="product-top-section"
                        style="display: flex; align-items: flex-start; gap: 32px; padding: 0 20px; max-width: 100%; margin-top: 16px;"
                    >
                        <!-- Left Column: Gallery -->
                        <div
                            class="product-col-left"
                            ref="colLeft"
                            style="flex: 0 0 calc(55% - 16px); min-width: 0; max-width: calc(55% - 16px);"
                        >
                            @include('shop::products.view.gallery')
                        </div>

                        <!-- Right Column: Product Info -->
                        <div
                            class="product-col-right"
                            ref="colRight"
                            style="flex: 0 0 calc(45% - 16px); min-width: 0; max-width: calc(45% - 16px);"
                        >
                            <template v-if="isReviewSectionVisible">
                                <div class="pdp-review-panel">
                                    <button
                                        type="button"
                                        class="pdp-review-back-button"
                                        @click="closeReviewSection()"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M15 18l-6-6 6-6"></path>
                                        </svg>

                                        Back
                                    </button>

                                    <div class="pdp-review-panel-content">
                                        @include('shop::products.view.reviews', ['isEmbeddedReview' => true])
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                {!! view_render_event('frooxi.shop.products.name.before', ['product' => $product]) !!}

                            <!-- Product Name -->
                            <h1
                                class="break-words"
                                style="font-size: 22px; font-weight: 400; line-height: 1.3; letter-spacing: 0.02em; color: #1a1a1a;"
                                v-pre
                            >
                                {{ $product->name }}
                            </h1>

                            {!! view_render_event('frooxi.shop.products.name.after', ['product' => $product]) !!}

                            <!-- Pricing -->
                            {!! view_render_event('frooxi.shop.products.price.before', ['product' => $product]) !!}

                            <p
                                class="product-price flex flex-wrap items-center gap-2"
                                style="font-size: 22px; margin-top: 8px;"
                            >
                                {!! $product->getTypeInstance()->getPriceHtml() !!}
                            </p>

                            {!! view_render_event('frooxi.shop.products.price.after', ['product' => $product]) !!}

                            <!-- Short Description -->
                            @if (trim(strip_tags($product->short_description ?? '')) !== '')
                                <pre style="font-size: 14px; color: #666; line-height: 1.6; white-space: pre-wrap; word-wrap: break-word; font-family: inherit; margin: 0; margin-top: 12px;" v-pre>{!! $product->short_description !!}</pre>
                            @endif



                            <!-- Divider -->
                            <div style="border-top: 1px solid #e5e5e5; margin-top: 24px; margin-bottom: 12px;"></div>

                            <!-- Product Type Includes -->
                            @includeIf('shop::products.view.types.simple')

                            @includeIf('shop::products.view.types.configurable')

                            @includeIf('shop::products.view.types.grouped')

                            @includeIf('shop::products.view.types.bundle')

                            @includeIf('shop::products.view.types.downloadable')

                            @includeIf('shop::products.view.types.booking')

                            <!-- Quantity + Add To Bag + Buy Now -->
                            <div class="pdp-actions">
                                {!! view_render_event('frooxi.shop.products.view.quantity.before', ['product' => $product]) !!}

                                @if ($product->getTypeInstance()->showQuantityBox())
                                    <x-shop::quantity-changer
                                        name="quantity"
                                        value="1"
                                        class="pdp-quantity-changer"
                                        style="width: fit-content; max-width: max-content; align-self: flex-start;"
                                    />
                                @endif

                                {!! view_render_event('frooxi.shop.products.view.quantity.after', ['product' => $product]) !!}

                                <div class="pdp-primary-action-row">
                                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                        {!! view_render_event('frooxi.shop.products.view.add_to_cart.before', ['product' => $product]) !!}

                                        <button
                                            type="submit"
                                            class="pdp-action-button pdp-add-to-bag"
                                            @click="is_buy_now=0;"
                                            :disabled="! Boolean({{ $product->isSaleable(1) ? 'true' : 'false' }}) || isStoring.addToCart"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M7 8V6a5 5 0 0 1 10 0v2"></path>
                                                <path d="M4 8h16l-1 11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 8z"></path>
                                            </svg>

                                            <span v-if="isStoring.addToCart">...</span>
                                            <span v-else>ADD TO BAG</span>
                                        </button>

                                        {!! view_render_event('frooxi.shop.products.view.add_to_cart.after', ['product' => $product]) !!}

                                        {!! view_render_event('frooxi.shop.products.view.buy_now.before', ['product' => $product]) !!}

                                        <button
                                            type="submit"
                                            class="pdp-action-button pdp-buy-now"
                                            @click="is_buy_now=1;"
                                            :disabled="! Boolean({{ $product->isSaleable(1) ? 'true' : 'false' }}) || isStoring.buyNow"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M13 2L6 13h5l-1 9 8-12h-5l0-8z"></path>
                                            </svg>

                                            <span v-if="isStoring.buyNow">...</span>
                                            <span v-else>BUY NOW</span>
                                        </button>

                                        {!! view_render_event('frooxi.shop.products.view.buy_now.after', ['product' => $product]) !!}
                                    @else
                                        <a
                                            href="tel:+8801880932952"
                                            class="pdp-action-button pdp-contact-button"
                                            style="grid-column: 1 / -1; text-decoration: none;"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                            </svg>

                                            @lang('shop::app.components.layouts.footer.contact-us')
                                        </a>
                                    @endif
                                </div>

                                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                    <div class="pdp-secondary-action-row">
                                        <button
                                            type="button"
                                            class="pdp-action-button pdp-review-button"
                                            @click="scrollToReview()"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                            </svg>

                                            Customer Review
                                        </button>

                                        <a
                                            href="tel:+8801880932952"
                                            class="pdp-action-button pdp-contact-button"
                                            style="text-decoration: none;"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                            </svg>

                                            @lang('shop::app.components.layouts.footer.contact-us')
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Wishlist (text link) -->
                            {!! view_render_event('frooxi.shop.products.view.additional_actions.before', ['product' => $product]) !!}

                            <div style="margin-top: 16px; display: flex; align-items: center; gap: 24px;">
                                <button
                                    type="button"
                                    style="background: none; border: none; cursor: pointer; font-size: 13px; color: #666; text-decoration: underline; padding: 0;"
                                    @click="addToWishlist()"
                                >
                                    <span v-text="isWishlist ? 'Added to Wishlist' : 'Add to Wishlist'"></span>
                                </button>
                            </div>

                            <!-- Share Section -->
                            <div style="margin-top: 24px; display: flex; align-items: center; gap: 16px;">
                                <span style="font-size: 13px; color: #999; text-transform: uppercase; letter-spacing: 0.05em;">Share</span>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#1a1a1a"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.514c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($product->name) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on X">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#1a1a1a"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . $shareUrl) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#1a1a1a"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Desktop Accordion (right column, hidden on mobile) -->
                            <div class="max-1180:hidden" style="margin-top: 24px; border-top: 1px solid #e5e5e5;">
                                <!-- Accordion Item 1: Product Details -->
                                <div style="border-bottom: 1px solid #e5e5e5;">
                                    <div onclick="toggleDesktopAccordion(this)" style="display: flex; cursor: pointer; align-items: center; justify-content: space-between; padding: 16px 0; user-select: none;">
                                        <p style="font-size: 14px; font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase; color: #1a1a1a;">Product Details</p>
                                        <span style="font-size: 18px; color: #1a1a1a; transition: transform 0.3s;">+</span>
                                    </div>
                                    <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                                        <div style="font-size: 14px; line-height: 1.7; color: #666; padding: 0 0 16px 0;">
                                            {!! $product->description !!}
                                        </div>
                                    </div>
                                </div>



                                <!-- Accordion Item 3: Delivery & Returns -->
                                <div style="border-bottom: 1px solid #e5e5e5;">
                                    <div onclick="toggleDesktopAccordion(this)" style="display: flex; cursor: pointer; align-items: center; justify-content: space-between; padding: 16px 0; user-select: none;">
                                        <p style="font-size: 14px; font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase; color: #1a1a1a;">Delivery &amp; Returns</p>
                                        <span style="font-size: 18px; color: #1a1a1a; transition: transform 0.3s;">+</span>
                                    </div>
                                    <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                                        <div style="font-size: 14px; line-height: 1.7; color: #666; padding: 0 0 16px 0;">
                                            @if (! empty($product->delivery_timeline))
                                                {!! nl2br(e($product->delivery_timeline)) !!}
                                            @else
                                                <p style="color: #aaa;">No delivery information available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Accordion Item 4: Care Instructions -->
                                <div style="border-bottom: 1px solid #e5e5e5;">
                                    <div onclick="toggleDesktopAccordion(this)" style="display: flex; cursor: pointer; align-items: center; justify-content: space-between; padding: 16px 0; user-select: none;">
                                        <p style="font-size: 14px; font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase; color: #1a1a1a;">Care Instructions</p>
                                        <span style="font-size: 18px; color: #1a1a1a; transition: transform 0.3s;">+</span>
                                    </div>
                                    <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                                        <div style="font-size: 14px; line-height: 1.7; color: #666; padding: 0 0 16px 0;">
                                            @if (! empty($product->care_instructions))
                                                {!! nl2br(e($product->care_instructions)) !!}
                                            @else
                                                <p style="color: #aaa;">No care instructions available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('frooxi.shop.products.view.additional_actions.after', ['product' => $product]) !!}
                            </template>
                        </div>
                    </div>
                </form>
            </x-shop::form>

            <!-- Contact Us Modal -->
            <x-shop::modal ref="contactUsModal">
                <x-slot:header>
                    <h2 class="text-lg font-semibold max-md:text-base">
                        @lang('shop::app.products.view.contact-us.title')
                    </h2>
                </x-slot>

                <x-slot:content>
                    <x-shop::form :action="route('shop.home.contact_us.send_mail')">
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.products.view.contact-us.name')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="text"
                                name="name"
                                rules="required"
                                :value="old('name')"
                                :label="trans('shop::app.products.view.contact-us.name')"
                                :placeholder="trans('shop::app.products.view.contact-us.name')"
                                :aria-label="trans('shop::app.products.view.contact-us.name')"
                                aria-required="true"
                            />

                            <x-shop::form.control-group.error control-name="name" />
                        </x-shop::form.control-group>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.products.view.contact-us.email')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="email"
                                name="email"
                                rules="required|email"
                                :value="old('email')"
                                :label="trans('shop::app.products.view.contact-us.email')"
                                :placeholder="trans('shop::app.products.view.contact-us.email')"
                                :aria-label="trans('shop::app.products.view.contact-us.email')"
                                aria-required="true"
                            />

                            <x-shop::form.control-group.error control-name="email" />
                        </x-shop::form.control-group>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label>
                                @lang('shop::app.products.view.contact-us.phone-number')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="text"
                                name="contact"
                                rules="phone"
                                :value="old('contact')"
                                :label="trans('shop::app.products.view.contact-us.phone-number')"
                                :placeholder="trans('shop::app.products.view.contact-us.phone-number')"
                                :aria-label="trans('shop::app.products.view.contact-us.phone-number')"
                            />

                            <x-shop::form.control-group.error control-name="contact" />
                        </x-shop::form.control-group>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.products.view.contact-us.desc')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="textarea"
                                name="message"
                                rules="required"
                                :label="trans('shop::app.products.view.contact-us.message')"
                                :placeholder="trans('shop::app.products.view.contact-us.describe-here')"
                                :aria-label="trans('shop::app.products.view.contact-us.message')"
                                aria-required="true"
                                rows="6"
                            />

                            <x-shop::form.control-group.error control-name="message" />
                        </x-shop::form.control-group>

                        @if (core()->getConfigData('customer.captcha.credentials.status'))
                            <x-shop::form.control-group class="mt-5">
                                {!! \Frooxi\Customer\Facades\Captcha::render() !!}

                                <x-shop::form.control-group.error control-name="recaptcha_token" />
                            </x-shop::form.control-group>
                        @endif

                        <div class="mt-6 flex justify-end">
                            <button
                                type="submit"
                                class="primary-button rounded-2xl px-8 py-3 max-sm:rounded-lg max-sm:px-6 max-sm:py-2"
                            >
                                @lang('shop::app.products.view.contact-us.submit')
                            </button>
                        </div>
                    </x-shop::form>
                </x-slot>
            </x-shop::modal>
        </script>

        <script type="module">
            app.component('v-product', {
                template: '#v-product-template',

                data() {
                    return {
                        isWishlist: false,

                        isCustomer: '{{ auth()->guard('customer')->check() }}',

                        is_buy_now: 0,

                        isReviewSectionVisible: false,

                        isStoring: {
                            addToCart: false,

                            buyNow: false,
                        },
                    }
                },

                mounted() {
                    this.checkWishlistStatus();
                    this.$nextTick(() => {
                        this.initStickyColumns();
                    });
                },

                methods: {
                    initStickyColumns() {
                        const headerOffset = 80;
                        this.applyStickyPositioning(headerOffset);

                        window.addEventListener('resize', () => this.applyStickyPositioning(headerOffset));

                        const images = this.$el.querySelectorAll('img');
                        images.forEach(img => {
                            if (!img.complete) {
                                img.addEventListener('load', () => this.applyStickyPositioning(headerOffset));
                            }
                        });

                        if (typeof ResizeObserver !== 'undefined') {
                            const ro = new ResizeObserver(() => this.applyStickyPositioning(headerOffset));
                            if (this.$refs.colLeft) ro.observe(this.$refs.colLeft);
                            if (this.$refs.colRight) ro.observe(this.$refs.colRight);
                        }
                    },

                    applyStickyPositioning(headerOffset) {
                        const left = this.$refs.colLeft;
                        const right = this.$refs.colRight;
                        if (!left || !right) return;

                        const vh = window.innerHeight;
                        const leftH = left.scrollHeight;
                        const rightH = right.scrollHeight;

                        left.style.position = 'sticky';
                        right.style.position = 'sticky';

                        if (leftH <= vh - headerOffset) {
                            left.style.top = headerOffset + 'px';
                        } else {
                            left.style.top = (vh - leftH) + 'px';
                        }

                        if (rightH <= vh - headerOffset) {
                            right.style.top = headerOffset + 'px';
                        } else {
                            right.style.top = (vh - rightH) + 'px';
                        }
                    },

                    addToCart(params) {
                        const operation = this.is_buy_now ? 'buyNow' : 'addToCart';

                        this.isStoring[operation] = true;

                        let formData = new FormData(this.$refs.formData);

                        this.ensureQuantity(formData);

                        this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(response => {
                                if (response.data.message) {
                                    this.$emitter.emit('update-mini-cart', response.data.data);

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    if (response.data.redirect) {
                                        window.location.href= response.data.redirect;
                                    }
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isStoring[operation] = false;
                            })
                            .catch(error => {
                                this.isStoring[operation] = false;

                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });
                            });
                    },

                    checkWishlistStatus() {
                        if (this.isCustomer) {
                            /**
                             * Fetches the wishlist items for the customer and checks whether the current
                             * product exists in the wishlist. If found, `isWishlist` is set to true;
                             * otherwise, it is set to false.
                             *
                             * This approach is used due to Full Page Cache (FPC) limitations. We cannot
                             * use a replacer here because `product_id` is dynamic, and the replacer
                             * cannot reliably detect it.
                             */
                            this.$axios.get('{{ route('shop.api.customers.account.wishlist.index') }}')
                                .then(response => {
                                    const wishlistItems = response.data.data || [];

                                    this.isWishlist = Boolean(wishlistItems.find(item => item.product.id == "{{ $product->id }}")?.product?.is_wishlist);
                                })
                                .catch(error => {});
                        }
                    },

                    addToWishlist() {
                        if (this.isCustomer) {
                            this.$axios.post('{{ route('shop.api.customers.account.wishlist.store') }}', {
                                    product_id: "{{ $product->id }}"
                                })
                                .then(response => {
                                    this.isWishlist = ! this.isWishlist;

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                    },

                    updateQty(quantity, id) {
                        this.isLoading = true;

                        let qty = {};

                        qty[id] = quantity;

                        this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty })
                            .then(response => {
                                if (response.data.message) {
                                    this.cart = response.data.data;
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isLoading = false;
                            }).catch(error => this.isLoading = false);
                    },

                    setStorageValue(key, value) {
                        localStorage.setItem(key, JSON.stringify(value));
                    },

                    getStorageValue(key) {
                        let value = localStorage.getItem(key);

                        if (value) {
                            value = JSON.parse(value);
                        }

                        return value;
                    },

                    scrollToReview() {
                        this.openReviewSection();
                    },

                    openReviewSection() {
                        if (this.isReviewSectionVisible) {
                            this.scrollReviewSectionIntoView();

                            return;
                        }

                        this.isReviewSectionVisible = true;

                        this.$nextTick(() => {
                            this.applyStickyPositioning(80);
                            this.scrollReviewSectionIntoView();
                        });
                    },

                    closeReviewSection() {
                        this.isReviewSectionVisible = false;

                        this.$nextTick(() => {
                            this.applyStickyPositioning(80);
                            this.scrollReviewSectionIntoView();
                        });
                    },

                    scrollReviewSectionIntoView() {
                        if (this.$refs.colRight) {
                            this.$refs.colRight.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start',
                            });
                        }
                    },

                    ensureQuantity(formData) {
                        if (! formData.has('quantity')) {
                            formData.append('quantity', 1);
                        }
                    },
                },
            });
        </script>

        <script
            type="text/x-template"
            id="v-product-associations-template"
        >
            <div ref="carouselWrapper">
                <template v-if="isVisible">
                    <!-- Featured Products -->
                    <x-shop::products.carousel
                        :title="trans('shop::app.products.view.related-product-title')"
                        :src="route('shop.api.products.related.index', ['id' => $product->id])"
                    />

                    <!-- Up-sell Products -->
                    <x-shop::products.carousel
                        :title="trans('shop::app.products.view.up-sell-title')"
                        :src="route('shop.api.products.up-sell.index', ['id' => $product->id])"
                    />
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-product-associations', {
                template: '#v-product-associations-template',

                data() {
                    return {
                        isVisible: false,
                    };
                },

                mounted() {
                    const observer = new IntersectionObserver(
                        (entries) => {
                            entries.forEach((entry) => {
                                if (entry.isIntersecting) {
                                    this.isVisible = true;
                                    observer.unobserve(entry.target); // Stop observing
                                }
                            });
                        },
                        { threshold: 0.1 }
                    );

                    observer.observe(this.$refs.carouselWrapper);
                }
            });
        </script>

        @if (core()->getConfigData('customer.captcha.credentials.status'))
            {!! \Frooxi\Customer\Facades\Captcha::renderJS() !!}
        @endif
    @endPushOnce
</x-shop::layouts>
