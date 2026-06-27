@props([
    'hasHeader'  => true,
    'hasFeature' => false,
    'hasFooter'  => false,
])

@php
    $hideBottomNav = in_array(request()->route()?->getName(), [
        'shop.customer.session.index',
        'shop.customer.session.create',
        'shop.customers.register.index',
        'shop.customers.register.create',
        'shop.customers.register.store',
    ]);
@endphp

<!DOCTYPE html>

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
>
    <head>

        {!! view_render_event('frooxi.shop.layout.head.before') !!}

        <title>{{ $title ?? '' }}</title>

        <link rel="icon" href="{{ asset('themes/shop/favicon_white.png?v=2') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('themes/shop/favicon_white.png?v=2') }}">

        <meta charset="UTF-8">

        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        >
        <meta
            http-equiv="content-language"
            content="{{ app()->getLocale() }}"
        >

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <meta
            name="csrf-token"
            content="{{ csrf_token() }}"
        >
        <meta
            name="base-url"
            content="{{ url()->to('/') }}"
        >
        <meta
            name="currency"
            content="{{ core()->getCurrentCurrency()->toJson() }}"
        >
        <meta 
            name="generator" 
            content="Frooxi"
        >

        @stack('meta')

        @frooxiVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

        <link
            rel="preconnect"
            href="https://fonts.googleapis.com"
            crossorigin
        />

        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin
        />

        <link
            rel="preload" as="style"
            href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap"
        />

        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap"
        />

        @stack('styles')

        <style>
            :root {
                --primary-font: 'Montserrat', sans-serif;
            }

            body, 
            p, 
            h1, 
            h2, 
            h3, 
            h4, 
            h5, 
            h6, 
            span, 
            a, 
            button, 
            input, 
            select, 
            textarea {
                font-family: var(--primary-font) !important;
            }

            html {
                overflow-x: clip;
                max-width: 100%;
            }
            body {
                overflow-x: clip;
                max-width: 100%;
            }

            .nav-link {
                color: #FFFFFF !important;
                font-size: 12px !important;
                font-weight: 400 !important;
                text-transform: uppercase !important;
                font-family: 'Montserrat', sans-serif !important;
                letter-spacing: 1.2px !important;
                text-decoration: none !important;
            }

            @media (max-width: 1023px) {
                #main {
                    padding-bottom: {{ $hideBottomNav ? '0' : 'calc(65px + env(safe-area-inset-bottom))' }} !important;
                }

                footer, .footer-wrapper {
                    padding-bottom: {{ $hideBottomNav ? '0' : 'calc(65px + env(safe-area-inset-bottom))' }} !important;
                }
            }

            @media (min-width: 1024px) {
                #mobile-bottom-nav { display: none !important; }
                #main { padding-bottom: 0 !important; }
                footer, .footer-wrapper { padding-bottom: 0 !important; }
            }

            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

        @if(core()->getConfigData('general.content.speculation_rules.enabled'))
            <script type="speculationrules">
                @json(core()->getSpeculationRules(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            </script>
        @endif
        
        <!-- WhatsApp FAB Styles -->
        <style>
            .whatsapp-fab {
                position: fixed;
                bottom: 24px;
                right: 20px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background-color: #25D366;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 9999;
                transition: all 0.3s ease;
                cursor: pointer;
                padding: 0;
            }
        
            .whatsapp-fab:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
            }
        
            .whatsapp-fab img {
                width: 60px;
                height: 60px;
                object-fit: contain;
            }
        
            /* Mobile responsive */
            @media (max-width: 768px) {
                .whatsapp-fab {
                    bottom: 80px;
                    right: 15px;
                    width: 55px;
                    height: 55px;
                }
        
                .whatsapp-fab img {
                    width: 55px;
                    height: 55px;
                }
            }
        </style>

        {!! view_render_event('frooxi.shop.layout.head.after') !!}

    </head>

    <body>
        {!! view_render_event('frooxi.shop.layout.body.before') !!}

        <a
            href="#main"
            class="skip-to-main-content-link"
        >
            Skip to main content
        </a>

        <!-- Built With Frooxi -->
        <div id="app">
            <!-- Flash Message Blade Component -->
            <x-shop::flash-group />

            <!-- Confirm Modal Blade Component -->
            <x-shop::modal.confirm />

            <!-- Page Header Blade Component -->
            @if ($hasHeader)
                <x-shop::layouts.header />
            @endif

            @if(
                core()->getConfigData('general.gdpr.settings.enabled')
                && core()->getConfigData('general.gdpr.cookie.enabled')
            )
                <x-shop::layouts.cookie />
            @endif

            {!! view_render_event('frooxi.shop.layout.content.before') !!}

            <!-- Page Content Blade Component -->
            <main id="main">
                {{ $slot }}
            </main>

            {!! view_render_event('frooxi.shop.layout.content.after') !!}


            <!-- Page Services Blade Component -->
            @if ($hasFeature)
                <x-shop::layouts.services />
            @endif

            <!-- Page Footer Blade Component -->
            @if ($hasFooter)
                <x-shop::layouts.footer />
            @endif

            <!-- Mobile Bottom Navigation -->
            @if (! $hideBottomNav)
                <x-shop::layouts.bottom-nav />
            @endif

            <!-- WhatsApp Floating Action Button -->
            <a
                href="https://wa.me/8801880932952"
                target="_blank"
                rel="noopener noreferrer"
                class="whatsapp-fab"
                aria-label="Chat on WhatsApp"
            >
                <img src="{{ asset('themes/shop/ic_whatsapp.png') }}" alt="WhatsApp" />
            </a>
        </div>

        {!! view_render_event('frooxi.shop.layout.body.after') !!}

        @stack('scripts')

        {!! view_render_event('frooxi.shop.layout.vue-app-mount.before') !!}
        <script>
            /**
             * Load event, the purpose of using the event is to mount the application
             * after all of our `Vue` components which is present in blade file have
             * been registered in the app. No matter what `app.mount()` should be
             * called in the last.
             */
            window.addEventListener("load", function (event) {
                app.mount("#app");
            });
        </script>

        {!! view_render_event('frooxi.shop.layout.vue-app-mount.after') !!}

        <script type="text/javascript">
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>
    </body>
</html>
