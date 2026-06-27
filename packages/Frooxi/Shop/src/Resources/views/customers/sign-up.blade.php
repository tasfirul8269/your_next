<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.customers.signup-form.page-title')"/>
    <meta name="keywords" content="@lang('shop::app.customers.signup-form.page-title')"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    {{-- Outer flex wrapper --}}
    <div id="auth-outer" style="display:flex;min-height:100vh;font-family:'Montserrat',sans-serif;">

        {{-- ── LEFT PANEL ─────────────────────────────────────── --}}
        <div id="auth-left" style="position:relative;flex:0 0 42%;background:#111;overflow:hidden;display:flex;flex-direction:column;justify-content:flex-end;padding:56px 52px;">

            {{-- Background gradient --}}
            <div style="position:absolute;inset:0;background:linear-gradient(160deg,#1a1a1a 0%,#0a0a0a 100%);"></div>

            {{-- Diagonal dot pattern --}}
            <div style="position:absolute;inset:0;opacity:.06;background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:28px 28px;"></div>

            {{-- Content --}}
            <div style="position:relative;z-index:2;">
                <span style="display:inline-block;font-size:10px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:#a38c5a;margin-bottom:20px;">
                    New here
                </span>

                <h2 style="font-size:clamp(26px,3vw,40px);font-weight:500;line-height:1.1;letter-spacing:.05em;text-transform:uppercase;color:#fff;margin:0 0 20px;">
                    Join the<br>collection.
                </h2>

                <p style="font-size:13px;line-height:1.8;color:rgba(255,255,255,.5);max-width:320px;margin:0 0 40px;">
                    Create your account and unlock exclusive access to the latest arrivals, order history, and a seamless checkout experience.
                </p>

                <div style="width:40px;height:1.5px;background:#a38c5a;"></div>
            </div>
        </div>

        {{-- ── RIGHT PANEL ──────────────────────────────────────── --}}
        <div id="auth-right" style="flex:1;background:#fff;display:flex;flex-direction:column;padding:56px 64px 64px;overflow-y:auto;">

            {{-- Logo --}}
            <a href="{{ route('shop.home.index') }}" style="display:block;margin-bottom:40px;" aria-label="{{ config('app.name') }}">
                <img
                    src="{{ asset('themes/shop/logo_black.png') }}"
                    alt="{{ config('app.name') }}"
                    style="height:44px;width:auto;"
                >
            </a>

            <p style="font-size:10px;font-weight:700;letter-spacing:.24em;text-transform:uppercase;color:#9ca3af;margin:0 0 14px;">
                Create Account
            </p>

            <h1 style="font-size:clamp(22px,2.4vw,32px);font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:#111;margin:0 0 8px;">
                @lang('shop::app.customers.signup-form.page-title')
            </h1>

            <p style="font-size:13px;color:#6b7280;margin:0 0 28px;">
                @lang('shop::app.customers.signup-form.form-signup-text')
            </p>

            <x-shop::form :action="route('shop.customers.register.store')">
                {!! view_render_event('frooxi.shop.customers.signup_form_controls.before') !!}

                {{-- 2-col name grid --}}
                <div id="auth-name-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                    {{-- First Name --}}
                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:7px;">
                            @lang('shop::app.customers.signup-form.first-name')
                            <span style="color:#ef4444;margin-left:2px;">*</span>
                        </label>
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.control
                                type="text"
                                class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-4 !py-3 !text-sm !font-normal"
                                name="first_name"
                                rules="required"
                                :value="old('first_name')"
                                :label="trans('shop::app.customers.signup-form.first-name')"
                                :placeholder="trans('shop::app.customers.signup-form.first-name')"
                                :aria-label="trans('shop::app.customers.signup-form.first-name')"
                                aria-required="true"
                            />
                            <x-shop::form.control-group.error control-name="first_name" />
                        </x-shop::form.control-group>
                    </div>

                    {!! view_render_event('frooxi.shop.customers.signup_form.first_name.after') !!}

                    {{-- Last Name --}}
                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:7px;">
                            @lang('shop::app.customers.signup-form.last-name')
                            <span style="color:#ef4444;margin-left:2px;">*</span>
                        </label>
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.control
                                type="text"
                                class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-4 !py-3 !text-sm !font-normal"
                                name="last_name"
                                rules="required"
                                :value="old('last_name')"
                                :label="trans('shop::app.customers.signup-form.last-name')"
                                :placeholder="trans('shop::app.customers.signup-form.last-name')"
                                :aria-label="trans('shop::app.customers.signup-form.last-name')"
                                aria-required="true"
                            />
                            <x-shop::form.control-group.error control-name="last_name" />
                        </x-shop::form.control-group>
                    </div>

                    {!! view_render_event('frooxi.shop.customers.signup_form.last_name.after') !!}
                </div>

                {{-- Phone --}}
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:7px;">
                        Phone Number
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.control
                            type="text"
                            class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-4 !py-3 !text-sm !font-normal"
                            name="phone"
                            rules="required"
                            :value="old('phone')"
                            :label="'Phone Number'"
                            placeholder="+880 1XXXXXXXXX"
                            :aria-label="'Phone Number'"
                            aria-required="true"
                        />
                        <x-shop::form.control-group.error control-name="phone" />
                    </x-shop::form.control-group>
                </div>



                {{-- 2-col password grid --}}
                <div id="auth-pass-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                    {{-- Password --}}
                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:7px;">
                            @lang('shop::app.customers.signup-form.password')
                            <span style="color:#ef4444;margin-left:2px;">*</span>
                        </label>
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.control
                                type="password"
                                class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-4 !py-3 !text-sm !font-normal"
                                name="password"
                                rules="required|min:6"
                                :value="old('password')"
                                :label="trans('shop::app.customers.signup-form.password')"
                                :placeholder="trans('shop::app.customers.signup-form.password')"
                                ref="password"
                                :aria-label="trans('shop::app.customers.signup-form.password')"
                                aria-required="true"
                            />
                            <x-shop::form.control-group.error control-name="password" />
                        </x-shop::form.control-group>
                    </div>

                    {!! view_render_event('frooxi.shop.customers.signup_form.password.after') !!}

                    {{-- Confirm Password --}}
                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:7px;">
                            @lang('shop::app.customers.signup-form.confirm-pass')
                        </label>
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.control
                                type="password"
                                class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-4 !py-3 !text-sm !font-normal"
                                name="password_confirmation"
                                rules="confirmed:@password"
                                value=""
                                :label="trans('shop::app.customers.signup-form.password')"
                                :placeholder="trans('shop::app.customers.signup-form.confirm-pass')"
                                :aria-label="trans('shop::app.customers.signup-form.confirm-pass')"
                                aria-required="true"
                            />
                            <x-shop::form.control-group.error control-name="password_confirmation" />
                        </x-shop::form.control-group>
                    </div>

                    {!! view_render_event('frooxi.shop.customers.signup_form.password_confirmation.after') !!}
                </div>

                {{-- Captcha --}}
                @if (core()->getConfigData('customer.captcha.credentials.status'))
                    <x-shop::form.control-group class="mb-4">
                        {!! \Frooxi\Customer\Facades\Captcha::render() !!}
                        <x-shop::form.control-group.error control-name="recaptcha_token" />
                    </x-shop::form.control-group>
                @endif

                {!! view_render_event('frooxi.shop.customers.signup_form.newsletter_subscription.after') !!}

                {{-- Submit --}}
                <button
                    type="submit"
                    style="width:100%;height:52px;background:#D63044;color:#fff;border:none;border-radius:8px;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;cursor:pointer;margin-top:8px;margin-bottom:24px;"
                    onmouseover="this.style.background='#c00510'"
                    onmouseout="this.style.background='#D63044'"
                >
                    @lang('shop::app.customers.signup-form.button-title')
                </button>

                {!! view_render_event('frooxi.shop.customers.signup_form_controls.after') !!}
            </x-shop::form>

            <p style="font-size:13px;color:#6b7280;text-align:center;margin:0;">
                @lang('shop::app.customers.signup-form.account-exists')
                <a href="{{ route('shop.customer.session.index') }}" style="color:#111;font-weight:600;text-decoration:none;">
                    @lang('shop::app.customers.signup-form.sign-in-button')
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        {!! \Frooxi\Customer\Facades\Captcha::renderJS() !!}

        <script>
            function authResponsive() {
                var left      = document.getElementById('auth-left');
                var right     = document.getElementById('auth-right');
                var nameGrid  = document.getElementById('auth-name-grid');
                var passGrid  = document.getElementById('auth-pass-grid');
                var isMob     = window.innerWidth < 768;

                if (left)  left.style.display              = isMob ? 'none' : 'flex';
                if (right) right.style.padding             = isMob ? '40px 24px 60px' : '56px 64px 64px';
                if (nameGrid) nameGrid.style.gridTemplateColumns = isMob ? '1fr' : '1fr 1fr';
                if (passGrid) passGrid.style.gridTemplateColumns = isMob ? '1fr' : '1fr 1fr';
            }

            authResponsive();
            window.addEventListener('resize', authResponsive);
        </script>
    @endpush
</x-shop::layouts>
