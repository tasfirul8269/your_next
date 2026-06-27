@php
    $channel = core()->getCurrentChannel();
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta name="title" content="{{ $channel->home_seo['meta_title'] ?? '' }}" />
    <meta name="description" content="{{ $channel->home_seo['meta_description'] ?? '' }}" />
    <meta name="keywords" content="{{ $channel->home_seo['meta_keywords'] ?? '' }}" />
@endPush

<x-shop::layouts
    :has-header="true"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        {{ $channel->home_seo['meta_title'] ?? '' }}
    </x-slot>

    <!-- Start of New Frontend Rebuild -->
    <div class="relative bg-black">

        <!-- Fullscreen Hero Carousel Section -->
        <div class="h-screen bg-neutral-900 mobile-hero-section" style="width:100%;max-width:100%;overflow:hidden;">
            @if ($heroSlides->count())
                <x-shop::carousel
                    :options="['images' => $heroSlides]"
                    aria-label="{{ trans('shop::app.home.index.image-carousel') }}"
                />
            @else
                <!-- Empty State Placeholder for Developers -->
                <div class="flex h-full w-full flex-col items-center justify-center text-center text-white p-10">
                    <h1 class="mb-4 text-4xl font-bold md:text-6xl">Your New Homepage</h1>
                    <p class="max-w-md text-lg text-gray-400">
                        Please add slides in
                        <span class="rounded bg-white/10 px-2 py-1 font-mono text-white">Admin &gt; Storefront &gt; Hero Carousel</span>
                        to populate this section.
                    </p>
                </div>
            @endif
        </div>

        <!-- Category Tabs Section -->
        <x-shop::categories.category-tabs :categories="$categories" />

    </div>

    @push('styles')
        <style>
            /* Adjust Mini Cart Appearance specifically for this header */
            .mini-cart-wrapper .icon-cart {
                font-size: 1.5rem !important;
                color: white !important;
            }
            .mini-cart-wrapper .bg-navyBlue {
                background-color: white !important;
                color: black !important;
            }

            /* Ensure Carousel fills full screen correctly */
            .sh-carousel-container {
                height: 100vh !important;
                width: 100vw !important;
                position: absolute;
                top: 0;
                left: 0;
            }

            /* Mobile hero section - 75vh */
            @media (max-width: 768px) {
                .mobile-hero-section {
                    height: 75vh !important;
                }
                .mobile-hero-section .hero-carousel {
                    height: 75vh !important;
                }
                .mobile-hero-section .sh-carousel-container {
                    height: 75vh !important;
                }
            }
        </style>
    @endpush

    {{-- Script logic moved to header component --}}
</x-shop::layouts>
