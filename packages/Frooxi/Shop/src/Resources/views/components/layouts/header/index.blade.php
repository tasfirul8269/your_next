{!! view_render_event('frooxi.shop.layout.header.before') !!}

{{-- 
@if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1 )
    <div class="max-lg:hidden">
        <x-shop::layouts.header.desktop.top />
    </div>
@endif
--}}

<header 
    id="main-header-container"
    class="{{ request()->routeIs('shop.home.index') ? 'absolute top-0 left-0 right-0 is-homepage' : 'relative' }} z-[1000] w-full transition-all duration-300"
    style="{{ request()->routeIs('shop.home.index') ? 'background: linear-gradient(to bottom, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 100%) !important;' : 'background-color: #ffffff !important; box-shadow: 0 1px 0 rgba(0,0,0,0.08);' }}"
>
    <v-header-switcher>
        <!-- Desktop Header Shimmer -->
        <div class="flex flex-wrap max-lg:hidden">
            <div class="flex min-h-[78px] w-full justify-between px-[60px] max-1180:px-8 bg-transparent">
                <!-- Left Navigation Section -->
                <div class="flex items-center gap-x-10 max-[1180px]:gap-x-5">
                    <!-- Logo Shimmer -->
                    <span
                        class="shimmer block h-[29px] w-[131px] rounded bg-white/10"
                        role="presentation"
                    >
                    </span>

                    <!-- Categories Shimmer -->
                    <div class="flex items-center gap-5">
                        <span
                            class="shimmer h-6 w-20 rounded bg-white/10"
                            role="presentation"
                        >
                        </span>

                        <span
                            class="shimmer h-6 w-20 rounded bg-white/10"
                            role="presentation"
                        >
                        </span>

                        <span
                            class="shimmer h-6 w-20 rounded bg-white/10"
                            role="presentation"
                        >
                        </span>
                    </div>
                </div>

                <!-- Right Navigation Section -->
                <div class="flex items-center gap-x-9 max-[1100px]:gap-x-6 max-lg:gap-x-8">
                    <!-- Search Bar Shimmer -->
                    <div class="relative w-full max-w-[445px]">
                        <span
                            class="shimmer block h-[42px] w-[250px] rounded-lg px-11 py-3 bg-white/10"
                            role="presentation"
                        >
                        </span>
                    </div>

                    <!-- Right Navigation Icons Shimmer -->
                    <div class="mt-1.5 flex gap-x-8 max-[1100px]:gap-x-6 max-lg:gap-x-8">
                        <!-- Compare Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded bg-white/10"
                            role="presentation"
                        >
                        </span>

                        <!-- Cart Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded bg-white/10"
                            role="presentation"
                        >
                        </span>

                        <!-- Profile Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded bg-white/10"
                            role="presentation"
                        >
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Header Shimmer -->
        <div style="display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:16px;padding:24px 16px 16px;background:transparent;">
            <!-- Left: Hamburger Shimmer -->
            <span class="shimmer" style="display:block;height:24px;width:24px;border-radius:4px;background:rgba(255,255,255,0.1);" role="presentation"></span>

            <!-- Center: Logo Shimmer -->
            <div style="display:flex;justify-content:center;">
                <span class="shimmer" style="display:block;height:29px;width:100px;border-radius:4px;background:rgba(255,255,255,0.1);" role="presentation"></span>
            </div>

            <!-- Right: Search + Cart Shimmer -->
            <div style="display:flex;align-items:center;gap:16px;">
                <span class="shimmer" style="display:block;height:24px;width:24px;border-radius:4px;background:rgba(255,255,255,0.1);" role="presentation"></span>
                <span class="shimmer" style="display:block;height:24px;width:24px;border-radius:4px;background:rgba(255,255,255,0.1);" role="presentation"></span>
            </div>
        </div>
    </v-header-switcher>
</header>

{!! view_render_event('frooxi.shop.layout.header.after') !!}

@pushOnce('scripts')
    <script 
        type="text/x-template" 
        id="v-header-switcher-template"
    >
        <v-desktop-header v-if="isDesktop"></v-desktop-header>
        
        <v-mobile-header v-else></v-mobile-header>
    </script>

    <script type="module">
        app.component('v-header-switcher', {
            template: '#v-header-switcher-template',

            data() {
                return {
                    isDesktop: window.innerWidth >= 1024
                }
            },

            mounted() {
                this.media = window.matchMedia('(min-width: 1024px)');

                this.media.addEventListener('change', this.handleMedia);
            },

            beforeUnmount() {
                this.media.removeEventListener('change', this.handleMedia);
            },

            methods: {
                handleMedia(e) {
                    this.isDesktop = e.matches;
                }
            }
        });

        app.component('v-desktop-header', {
            template: '#v-desktop-header-template'
        });

        app.component('v-mobile-header', {
            template: '#v-mobile-header-template'
        });
    </script>

    <script 
        type="text/x-template" 
        id="v-desktop-header-template"
    >
        <x-shop::layouts.header.desktop />
    </script>

    <script 
        type="text/x-template" 
        id="v-mobile-header-template"
    >
        <x-shop::layouts.header.mobile />
    </script>
@endPushOnce
