<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.customers.login-form.page-title')"/>
    <meta name="keywords" content="@lang('shop::app.customers.login-form.page-title')"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
    </x-slot>

    {{-- Outer flex wrapper: full viewport height, two columns --}}
    <div id="auth-outer" style="display:flex;min-height:100vh;font-family:'Montserrat',sans-serif;">

        {{-- ── LEFT PANEL ─────────────────────────────────────── --}}
        <div id="auth-left" style="position:relative;flex:0 0 48%;background:#111;overflow:hidden;display:flex;flex-direction:column;justify-content:flex-end;padding:56px 52px;">

            {{-- Background gradient --}}
            <div style="position:absolute;inset:0;background:linear-gradient(160deg,#1a1a1a 0%,#0a0a0a 100%);"></div>

            {{-- Diagonal dot pattern --}}
            <div style="position:absolute;inset:0;opacity:.06;background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:28px 28px;"></div>

            {{-- Content --}}
            <div style="position:relative;z-index:2;">
                <span style="display:inline-block;font-size:10px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:#a38c5a;margin-bottom:20px;">
                    Welcome back
                </span>

                <h2 style="font-size:clamp(28px,3.2vw,44px);font-weight:500;line-height:1.08;letter-spacing:.05em;text-transform:uppercase;color:#fff;margin:0 0 20px;">
                    Style starts<br>with you.
                </h2>

                <p style="font-size:13px;line-height:1.8;color:rgba(255,255,255,.5);max-width:340px;margin:0 0 40px;">
                    Sign in to explore the latest collections, track your orders, and enjoy a personalised shopping experience.
                </p>

                <div style="width:40px;height:1.5px;background:#a38c5a;"></div>
            </div>
        </div>

        {{-- ── RIGHT PANEL ──────────────────────────────────────── --}}
        <div id="auth-right" style="flex:1;background:#fff;display:flex;flex-direction:column;justify-content:center;padding:64px 72px;overflow-y:auto;">

            {{-- Logo --}}
            <a href="{{ route('shop.home.index') }}" style="display:block;margin-bottom:48px;" aria-label="{{ config('app.name') }}">
                <img
                    src="{{ asset('themes/shop/logo_black.png') }}"
                    alt="{{ config('app.name') }}"
                    style="height:44px;width:auto;"
                >
            </a>

            <p style="font-size:10px;font-weight:700;letter-spacing:.24em;text-transform:uppercase;color:#9ca3af;margin:0 0 14px;">
                Customer Portal
            </p>

            <h1 style="font-size:clamp(26px,2.8vw,36px);font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:#111;margin:0 0 8px;">
                @lang('shop::app.customers.login-form.page-title')
            </h1>

            <p style="font-size:13px;color:#6b7280;margin:0 0 36px;">
                @lang('shop::app.customers.login-form.form-login-text')
            </p>

            {!! view_render_event('frooxi.shop.customers.login.before') !!}

            <x-shop::form :action="route('shop.customer.session.create')">
                {!! view_render_event('frooxi.shop.customers.login_form_controls.before') !!}

                {{-- Phone --}}
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:8px;">
                        Phone Number
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>

                    <x-shop::form.control-group>
                        <x-shop::form.control-group.control
                            type="text"
                            class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-5 !py-4 !text-sm !font-normal"
                            name="phone"
                            rules="required"
                            value=""
                            :label="'Phone Number'"
                            placeholder="+880 1XXXXXXXXX"
                            :aria-label="'Phone Number'"
                            aria-required="true"
                        />
                        <x-shop::form.control-group.error control-name="phone" />
                    </x-shop::form.control-group>
                </div>

                {{-- Password --}}
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:8px;">
                        @lang('shop::app.customers.login-form.password')
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>

                    <x-shop::form.control-group>
                        <x-shop::form.control-group.control
                            type="password"
                            class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-5 !py-4 !text-sm !font-normal"
                            id="password"
                            name="password"
                            rules="required|min:6"
                            value=""
                            :label="trans('shop::app.customers.login-form.password')"
                            :placeholder="trans('shop::app.customers.login-form.password')"
                            :aria-label="trans('shop::app.customers.login-form.password')"
                            aria-required="true"
                        />
                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>
                </div>

                {{-- Show password + Forgot --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:8px;">
                    <div style="display:flex;align-items:center;gap:1.5px;">
                        <input
                            type="checkbox"
                            id="show-password"
                            class="peer hidden"
                            onchange="switchVisibility()"
                        />
                        <label
                            class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue peer-checked:text-navyBlue"
                            for="show-password"
                        ></label>
                        <label
                            style="cursor:pointer;font-size:13px;color:#6b7280;"
                            for="show-password"
                        >
                            @lang('shop::app.customers.login-form.show-password')
                        </label>
                    </div>

                    <a
                        href="{{ route('shop.customers.forgot_password.create') }}"
                        style="font-size:12px;color:#111;text-decoration:none;font-weight:500;letter-spacing:.04em;"
                    >
                        @lang('shop::app.customers.login-form.forgot-pass')
                    </a>
                </div>

                {{-- Captcha --}}
                @if (core()->getConfigData('customer.captcha.credentials.status'))
                    <x-shop::form.control-group class="mb-5">
                        {!! \Frooxi\Customer\Facades\Captcha::render() !!}
                        <x-shop::form.control-group.error control-name="recaptcha_token" />
                    </x-shop::form.control-group>
                @endif

                {{-- Submit --}}
                <button
                    type="submit"
                    style="width:100%;height:52px;background:#D63044;color:#fff;border:none;border-radius:8px;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;cursor:pointer;margin-bottom:24px;"
                    onmouseover="this.style.background='#c00510'"
                    onmouseout="this.style.background='#D63044'"
                >
                    @lang('shop::app.customers.login-form.button-title')
                </button>

                {!! view_render_event('frooxi.shop.customers.login_form_controls.after') !!}
            </x-shop::form>

            {!! view_render_event('frooxi.shop.customers.login.after') !!}

            <p style="font-size:13px;color:#6b7280;text-align:center;margin:0;">
                @lang('shop::app.customers.login-form.new-customer')
                <a href="{{ route('shop.customers.register.index') }}" style="color:#111;font-weight:600;text-decoration:none;">
                    @lang('shop::app.customers.login-form.create-your-account')
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        {!! \Frooxi\Customer\Facades\Captcha::renderJS() !!}

        <script>
            function switchVisibility() {
                let passwordField = document.getElementById('password');
                passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
            }

            function authResponsive() {
                var left  = document.getElementById('auth-left');
                var right = document.getElementById('auth-right');
                var isMob = window.innerWidth < 768;

                if (left)  left.style.display  = isMob ? 'none' : 'flex';
                if (right) {
                    right.style.padding          = isMob ? '40px 24px 56px' : '64px 72px';
                    right.style.justifyContent   = isMob ? 'flex-start' : 'center';
                }
            }

            authResponsive();
            window.addEventListener('resize', authResponsive);
        </script>
    @endpush
</x-shop::layouts>
