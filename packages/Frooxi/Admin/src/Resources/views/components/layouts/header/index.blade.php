@php
    $admin = auth()->guard('admin')->user();
@endphp

<header class="sticky top-0 z-[10001] flex items-center justify-between border-b border-gray-100 bg-white px-4 py-3 sm:px-4">
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Hamburger Menu -->
        <i
            class="icon-menu cursor-pointer rounded-lg p-1.5 text-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 lg:hidden sm:text-2xl transition-colors"
            @click="$refs.sidebarMenuDrawer.open()"
        >
        </i>

        <!-- Logo -->
        <a href="{{ route('admin.dashboard.index') }}" class="flex-shrink-0">
            <img
                src="{{ asset('themes/shop/logo_black.png') }}"
                class="h-8 w-auto sm:h-9"
                id="logo-image"
                alt="{{ config('app.name') }}"
            />
        </a>
    </div>

    <div class="flex items-center gap-1.5 sm:gap-2">
        <!-- Visit Shop Link -->
        <a 
            href="{{ route('shop.home.index') }}" 
            target="_blank"
            class="hidden sm:flex items-center gap-1 rounded-lg px-2 py-1.5 text-sm text-gray-500 transition-colors hover:text-[#D4A84B]"
            title="@lang('admin::app.components.layouts.header.visit-shop')"
        >
            <span class="icon-store text-xl"></span>
        </a>

       <!-- REMOVED: Notification package deleted -->

        <!-- Admin profile -->
        <x-admin::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
            <x-slot:toggle>
                @if ($admin->image)
                    <button class="flex h-8 w-8 cursor-pointer overflow-hidden rounded-full hover:opacity-80 focus:opacity-80 sm:h-9 sm:w-9">
                        <img
                            src="{{ $admin->image_url }}"
                            class="h-full w-full object-cover"
                        />
                    </button>
                @else
                    <button class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-[#D4A84B] text-xs font-semibold leading-6 text-white transition-all hover:bg-[#B8923F] focus:bg-[#B8923F] focus:ring-2 focus:ring-[#D4A84B] focus:ring-offset-1 focus:outline-none sm:h-9 sm:w-9 sm:text-sm">
                        {{ substr($admin->name, 0, 1) }}
                    </button>
                @endif
            </x-slot>

            <!-- Admin Dropdown -->
            <x-slot:content class="!p-0 rounded-xl border border-gray-100 shadow-lg animate-fade-in">
                <div class="flex items-center gap-1.5 border-b border-gray-100 bg-gray-50 px-4 py-2 sm:px-5 sm:py-2.5">
                    <img
                        src="{{ asset('themes/shop/logo_black.png') }}"
                        class="sm:h-6 sm:w-6"
                        width="20"
                        height="20"
                    />

                    <!-- Version -->
                    <p class="text-xs text-gray-400 sm:text-sm">
                        @lang('admin::app.components.layouts.header.app-version', ['version' => 'v' . core()->version()])
                    </p>
                </div>

                <div class="grid gap-0.5 py-1.5">
                    <a
                        class="cursor-pointer px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#D4A84B] sm:px-5"
                        href="{{ route('admin.account.edit') }}"
                    >
                        @lang('admin::app.components.layouts.header.my-account')
                    </a>

                    <!--Admin logout-->
                    <x-admin::form
                        method="DELETE"
                        action="{{ route('admin.session.destroy') }}"
                        id="adminLogout"
                    >
                    </x-admin::form>

                    <a
                        class="cursor-pointer px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-[#D4A84B] sm:px-5"
                        href="{{ route('admin.session.destroy') }}"
                        onclick="event.preventDefault(); document.getElementById('adminLogout').submit();"
                    >
                        @lang('admin::app.components.layouts.header.logout')
                    </a>
                </div>
            </x-slot>
        </x-admin::dropdown>
    </div>
</header>

<!-- Menu Sidebar Drawer -->
<x-admin::drawer
    position="left"
    width="270px"
    ref="sidebarMenuDrawer"
>
    <!-- Drawer Header -->
    <x-slot:header>
        <div class="flex items-center justify-between">
            <img
                src="{{ asset('themes/shop/logo_black.png') }}"
                class="h-8 w-auto sm:h-10"
                alt="{{ config('app.name') }}"
            />
        </div>
    </x-slot>

    <!-- Drawer Content -->
    <x-slot:content class="p-3 sm:p-4">
        <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto">
            <nav class="grid w-full gap-1.5 sm:gap-2">
                <!-- Navigation Menu -->
                @foreach (menu()->getItems('admin') as $menuItem)
                    <div class="group/item relative">
                        <a
                            href="{{ $menuItem->getUrl() }}"
                            class="flex items-center gap-2 p-1.5 cursor-pointer hover:rounded-lg {{ $menuItem->isActive() == 'active' ? 'bg-blue-600 rounded-lg' : ' hover:bg-gray-100 hover:dark:bg-gray-950' }} peer sm:gap-2.5"
                        >
                            <span class="{{ $menuItem->getIcon() }} text-xl {{ $menuItem->isActive() ? 'text-white' : ''}} sm:text-2xl"></span>
                            
                            <p class="font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap text-sm group-[.sidebar-collapsed]/container:hidden {{ $menuItem->isActive() ? 'text-white' : ''}} sm:text-base">
                                {{ $menuItem->getName() }}
                            </p>
                        </a>

                        @if ($menuItem->haveChildren())
                            <div class="{{ $menuItem->isActive() ? ' !grid bg-gray-100 dark:bg-gray-950' : '' }} hidden min-w-[180px] ltr:pl-8 rtl:pr-8 pb-2 rounded-b-lg z-[100] sm:ltr:pl-10 sm:rtl:pr-10">
                                @foreach ($menuItem->getChildren() as $subMenuItem)
                                    <a
                                        href="{{ $subMenuItem->getUrl() }}"
                                        class="text-xs text-{{ $subMenuItem->isActive() ? 'blue':'gray' }}-600 dark:text-{{ $subMenuItem->isActive() ? 'blue':'gray' }}-300 whitespace-nowrap py-1 group-[.sidebar-collapsed]/container:px-4 group-[.sidebar-collapsed]/container:py-2 group-[.inactive]/item:px-4 group-[.inactive]/item:py-2 hover:text-blue-600 dark:hover:bg-gray-950 sm:text-sm sm:group-[.sidebar-collapsed]/container:px-5 sm:group-[.sidebar-collapsed]/container:py-2.5 sm:group-[.inactive]/item:px-5 sm:group-[.inactive]/item:py-2.5"
                                    >
                                        {{ $subMenuItem->getName() }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>
        </div>
    </x-slot>
</x-admin::drawer>

@pushOnce('scripts')
    {{-- REMOVED: Notification package deleted - Notification Vue Component --}}

    <script
        type="text/x-template"
        id="v-dark-template"
    >
        <div class="flex">
            <span
                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                :class="[isDarkMode ? 'icon-light' : 'icon-dark']"
                @click="toggle"
            ></span>
        </div>
    </script>

    <script type="module">
        app.component('v-dark', {
            template: '#v-dark-template',

            data() {
                return {
                    isDarkMode: {{ request()->cookie('dark_mode') ?? 0 }},

                    logo: "{{ asset('themes/shop/logo_black.png') }}",

                    dark_logo: "{{ asset('themes/shop/logo_black.png') }}",
                };
            },

            methods: {
                toggle() {
                    this.isDarkMode = parseInt(this.isDarkModeCookie()) ? 0 : 1;

                    var expiryDate = new Date();

                    expiryDate.setMonth(expiryDate.getMonth() + 1);

                    document.cookie = 'dark_mode=' + this.isDarkMode + '; path=/; expires=' + expiryDate.toGMTString();

                    document.documentElement.classList.toggle('dark', this.isDarkMode === 1);

                    if (this.isDarkMode) {
                        this.$emitter.emit('change-theme', 'dark');

                        document.getElementById('logo-image').src = this.dark_logo;
                    } else {
                        this.$emitter.emit('change-theme', 'light');

                        document.getElementById('logo-image').src = this.logo;
                    }
                },

                isDarkModeCookie() {
                    const cookies = document.cookie.split(';');

                    for (const cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');

                        if (name === 'dark_mode') {
                            return value;
                        }
                    }

                    return 0;
                },
            },
        });
    </script>
@endpushOnce