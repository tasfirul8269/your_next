<x-admin::layouts.anonymous>
    <x-slot:title>
        @lang('admin::app.users.sessions.title')
    </x-slot>

    {{-- Minimal Clean Login Page - Homepage Style --}}
    <div class="flex min-h-screen bg-white" style="font-family:'Montserrat',sans-serif;">
        
        {{-- LEFT PANEL - Black Background --}}
        <div class="hidden lg:flex lg:w-1/2 bg-black flex-col justify-between p-12 xl:p-16">
            
            {{-- Top: Logo --}}
            <div>
                <img
                    src="{{ asset('themes/shop/logo_black.png') }}"
                    alt="{{ config('app.name') }}"
                    class="h-8 w-auto"
                >
            </div>

            {{-- Bottom: Branding --}}
            <div>
                <span style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#9ca3af;margin-bottom:24px;display:block;">
                    Admin Portal
                </span>

                <h2 style="font-size:clamp(32px,4vw,48px);font-weight:400;line-height:1.1;letter-spacing:0.06em;text-transform:uppercase;color:#fff;margin:0 0 24px;font-family:'DM Serif Display',Georgia,serif;">
                    Manage your<br>store.
                </h2>

                <p style="font-size:14px;line-height:1.8;color:#9ca3af;max-w-md;margin:0;">
                    Control inventory, orders, customers, and analytics — all in one place.
                </p>

                <div style="width:48px;height:2px;background:#fff;margin-top:32px;"></div>
            </div>
        </div>

        {{-- RIGHT PANEL - White Background --}}
        <div class="flex-1 flex flex-col justify-center px-8 sm:px-12 lg:px-16 xl:px-24 py-12">
            <div class="w-full max-w-md mx-auto">
                
                {{-- Mobile Logo --}}
                <div class="lg:hidden mb-12">
                    <img
                        src="{{ asset('themes/shop/logo_black.png') }}"
                        alt="{{ config('app.name') }}"
                        class="h-7 w-auto"
                    >
                </div>

                {{-- Header --}}
                <div class="mb-12">
                    <p style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#9ca3af;margin:0 0 16px;">
                        Administrator
                    </p>
                    <h1 style="font-size:clamp(28px,3vw,36px);font-weight:400;letter-spacing:0.06em;text-transform:uppercase;color:#111;margin:0 0 8px;font-family:'DM Serif Display',Georgia,serif;">
                        @lang('admin::app.users.sessions.title')
                    </h1>
                </div>

                {{-- Login Form --}}
                <x-admin::form :action="route('admin.session.store')">

                    {{-- Email Field --}}
                    <div style="margin-bottom:24px;">
                        <label style="font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#111;margin-bottom:8px;display:block;">
                            @lang('admin::app.users.sessions.email')
                            <span style="color:#ef4444;margin-left:2px;">*</span>
                        </label>
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.control
                                type="email"
                                class="!rounded-lg !border-gray-200 !bg-white !px-4 !py-3.5 !text-sm focus:!border-black focus:!ring-1 focus:!ring-black transition-all"
                                id="email"
                                name="email"
                                rules="required|email"
                                :label="trans('admin::app.users.sessions.email')"
                                :placeholder="trans('admin::app.users.sessions.email')"
                            />
                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>
                    </div>

                    {{-- Password Field --}}
                    <div style="margin-bottom:24px;position:relative;">
                        <label style="font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#111;margin-bottom:8px;display:block;">
                            @lang('admin::app.users.sessions.password')
                            <span style="color:#ef4444;margin-left:2px;">*</span>
                        </label>
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.control
                                type="password"
                                class="!rounded-lg !border-gray-200 !bg-white !px-4 !py-3.5 !text-sm !pr-12 focus:!border-black focus:!ring-1 focus:!ring-black transition-all"
                                id="password"
                                name="password"
                                rules="required|min:6"
                                :label="trans('admin::app.users.sessions.password')"
                                :placeholder="trans('admin::app.users.sessions.password')"
                            />
                            <span
                                class="icon-view absolute top-[42px] right-4 text-2xl cursor-pointer text-gray-400 hover:text-black transition-colors"
                                id="visibilityIcon"
                                onclick="switchVisibility()"
                                role="presentation"
                                tabindex="0"
                            ></span>
                            <x-admin::form.control-group.error control-name="password" />
                        </x-admin::form.control-group>
                    </div>

                    {{-- Forgot Password Link --}}
                    <div style="text-align:right;margin-bottom:32px;">
                        <a
                            href="{{ route('admin.forget_password.create') }}"
                            style="font-size:12px;font-weight:500;color:#111;text-decoration:none;letter-spacing:0.1em;text-transform:uppercase;"
                            onmouseover="this.style.color='#666'"
                            onmouseout="this.style.color='#111'"
                        >
                            @lang('admin::app.users.sessions.forget-password-link')
                        </a>
                    </div>

                    {{-- Submit Button - Black Pill --}}
                    <button
                        type="submit"
                        style="width:100%;padding:14px 44px;background:#000;color:#fff;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:500;border-radius:9999px;border:none;cursor:pointer;letter-spacing:0.2em;text-transform:uppercase;transition:all 0.2s;"
                        onmouseover="this.style.background='#333'"
                        onmouseout="this.style.background='#000'"
                        aria-label="{{ trans('admin::app.users.sessions.submit-btn') }}"
                    >
                        @lang('admin::app.users.sessions.submit-btn')
                    </button>

                </x-admin::form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function switchVisibility() {
                var f = document.getElementById('password');
                var i = document.getElementById('visibilityIcon');
                f.type = f.type === 'password' ? 'text' : 'password';
                i.classList.toggle('icon-view-close');
            }
        </script>
    @endpush
</x-admin::layouts.anonymous>
