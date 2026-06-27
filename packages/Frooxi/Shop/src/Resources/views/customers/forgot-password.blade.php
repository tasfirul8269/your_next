<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.customers.forgot-password.title')"/>
    <meta name="keywords" content="@lang('shop::app.customers.forgot-password.title')"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        @lang('shop::app.customers.forgot-password.title')
    </x-slot>

    {{-- Outer flex wrapper --}}
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
                    Account recovery
                </span>

                <h2 style="font-size:clamp(28px,3.2vw,44px);font-weight:500;line-height:1.08;letter-spacing:.05em;text-transform:uppercase;color:#fff;margin:0 0 20px;">
                    Reset your<br>password.
                </h2>

                <p style="font-size:13px;line-height:1.8;color:rgba(255,255,255,.5);max-width:340px;margin:0 0 40px;">
                    Enter your email address and we'll send you a link to get back into your account.
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
                @lang('shop::app.customers.forgot-password.title')
            </h1>

            <p style="font-size:13px;color:#6b7280;margin:0 0 36px;">
                @lang('shop::app.customers.forgot-password.forgot-password-text')
            </p>

            {!! view_render_event('frooxi.shop.customers.forget_password.before') !!}

            <x-shop::form :action="route('shop.customers.forgot_password.store')">
                {!! view_render_event('frooxi.shop.customers.forget_password_form_controls.before') !!}

                {{-- Email --}}
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:8px;">
                        @lang('shop::app.customers.login-form.email')
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>

                    <x-shop::form.control-group>
                        <x-shop::form.control-group.control
                            type="email"
                            class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-5 !py-4 !text-sm !font-normal"
                            name="email"
                            rules="required|email"
                            value=""
                            :label="trans('shop::app.customers.login-form.email')"
                            placeholder="email@example.com"
                            :aria-label="trans('shop::app.customers.login-form.email')"
                            aria-required="true"
                        />
                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>
                </div>

                {!! view_render_event('frooxi.shop.customers.forget_password_form_controls.email.after') !!}

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
                    @lang('shop::app.customers.forgot-password.submit')
                </button>

                {!! view_render_event('frooxi.shop.customers.forget_password_form_controls.after') !!}
            </x-shop::form>

            {!! view_render_event('frooxi.shop.customers.forget_password.after') !!}

            <p style="font-size:13px;color:#6b7280;text-align:center;margin:0;">
                @lang('shop::app.customers.forgot-password.back')
                <a href="{{ route('shop.customer.session.index') }}" style="color:#111;font-weight:600;text-decoration:none;">
                    @lang('shop::app.customers.forgot-password.sign-in-button')
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        {!! \Frooxi\Customer\Facades\Captcha::renderJS() !!}

        <script>
            function authResponsive() {
                var left  = document.getElementById('auth-left');
                var right = document.getElementById('auth-right');
                var isMob = window.innerWidth < 768;

                if (left)  left.style.display        = isMob ? 'none' : 'flex';
                if (right) {
                    right.style.padding              = isMob ? '40px 24px 56px' : '64px 72px';
                    right.style.justifyContent       = isMob ? 'flex-start' : 'center';
                }
            }

            authResponsive();
            window.addEventListener('resize', authResponsive);
        </script>
    @endpush
</x-shop::layouts>
