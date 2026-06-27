<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Verify Phone Number
    </x-slot>

    {{-- Outer flex wrapper --}}
    <div id="auth-outer" style="display:flex;min-height:100vh;font-family:'Montserrat',sans-serif;">

        {{-- ── LEFT PANEL ─────────────────────────────────────── --}}
        <div id="auth-left" style="position:relative;flex:0 0 42%;background:#111;overflow:hidden;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:56px 52px;">

            {{-- Background gradient --}}
            <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);"></div>

            {{-- Diagonal dot pattern --}}
            <div style="position:absolute;inset:0;opacity:.06;background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:28px 28px;"></div>

            {{-- Content --}}
            <div style="position:relative;z-index:2;text-align:center;">
                <a href="{{ route('shop.home.index') }}" style="display:inline-block;margin-bottom:40px;" aria-label="{{ config('app.name') }}">
                    <img
                        src="{{ asset('themes/shop/logo_black.png') }}"
                        alt="{{ config('app.name') }}"
                        style="height:36px;width:auto;filter:brightness(0) invert(1);"
                    >
                </a>

                <h2 style="font-size:clamp(26px,3vw,40px);font-weight:500;line-height:1.1;letter-spacing:.05em;text-transform:uppercase;color:#fff;margin:0 0 20px;">
                    Almost There!
                </h2>

                <p style="font-size:13px;line-height:1.8;color:rgba(255,255,255,.5);max-width:320px;margin:0 auto;">
                    We've sent a verification code to your phone number. Enter it below to complete your registration.
                </p>
            </div>
        </div>

        {{-- ── RIGHT PANEL ──────────────────────────────────────── --}}
        <div id="auth-right" style="flex:1;background:#fff;display:flex;flex-direction:column;padding:56px 64px 64px;overflow-y:auto;">

            {{-- Mobile logo --}}
            <div id="auth-mobile-logo" style="display:none;margin-bottom:40px;text-align:center;">
                <a href="{{ route('shop.home.index') }}" aria-label="{{ config('app.name') }}">
                    <img
                        src="{{ asset('themes/shop/logo_black.png') }}"
                        alt="{{ config('app.name') }}"
                        style="height:44px;width:auto;"
                    >
                </a>
            </div>

            {{-- Desktop logo --}}
            <a href="{{ route('shop.home.index') }}" id="auth-desktop-logo" style="display:block;margin-bottom:40px;" aria-label="{{ config('app.name') }}">
                <img
                    src="{{ asset('themes/shop/logo_black.png') }}"
                    alt="{{ config('app.name') }}"
                    style="height:44px;width:auto;"
                >
            </a>

            <p style="font-size:10px;font-weight:700;letter-spacing:.24em;text-transform:uppercase;color:#9ca3af;margin:0 0 14px;">
                Verify Phone
            </p>

            <h1 style="font-size:clamp(22px,2.4vw,32px);font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:#111;margin:0 0 8px;">
                Verify Your Phone
            </h1>

            <p style="font-size:13px;color:#6b7280;margin:0 0 28px;">
                Enter the 6-digit code sent to <strong>{{ $maskedPhone }}</strong>
            </p>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div style="margin-bottom:16px;padding:14px 18px;border-radius:8px;background:#f0fdf4;color:#15803d;font-size:13px;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div style="margin-bottom:16px;padding:14px 18px;border-radius:8px;background:#fef2f2;color:#b91c1c;font-size:13px;">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('warning'))
                <div style="margin-bottom:16px;padding:14px 18px;border-radius:8px;background:#fffbeb;color:#a16207;font-size:13px;">
                    {{ session('warning') }}
                </div>
            @endif

            <x-shop::form :action="route('shop.customers.verify-otp.store')">
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#374151;margin-bottom:7px;">
                        Verification Code
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.control
                            type="text"
                            class="!rounded-lg !border-zinc-200 !bg-zinc-50 !px-4 !py-3 !text-sm !font-normal !text-center !tracking-[0.5em] !text-2xl"
                            name="otp"
                            rules="required|length:6"
                            :label="'OTP'"
                            placeholder="------"
                            maxlength="6"
                        />
                        <x-shop::form.control-group.error control-name="otp" />
                    </x-shop::form.control-group>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    style="width:100%;height:52px;background:#D63044;color:#fff;border:none;border-radius:8px;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;cursor:pointer;margin-bottom:24px;"
                    onmouseover="this.style.background='#c00510'"
                    onmouseout="this.style.background='#D63044'"
                >
                    Verify & Create Account
                </button>
            </x-shop::form>

            <div style="display:flex;align-items:center;justify-content:space-between;">
                <form action="{{ route('shop.customers.resend-otp') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        style="background:none;border:none;cursor:pointer;font-size:13px;font-weight:600;color:#0f3460;font-family:'Montserrat',sans-serif;padding:0;"
                        onmouseover="this.style.textDecoration='underline'"
                        onmouseout="this.style.textDecoration='none'"
                    >
                        Resend OTP
                    </button>
                </form>

                <a
                    href="{{ route('shop.customers.register.index') }}"
                    style="font-size:13px;color:#6b7280;text-decoration:none;font-family:'Montserrat',sans-serif;"
                    onmouseover="this.style.color='#374151'"
                    onmouseout="this.style.color='#6b7280'"
                >
                    Back to Registration
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function authResponsive() {
                var left       = document.getElementById('auth-left');
                var right      = document.getElementById('auth-right');
                var mobileLogo = document.getElementById('auth-mobile-logo');
                var desktopLogo = document.getElementById('auth-desktop-logo');
                var isMob      = window.innerWidth < 768;

                if (left)  left.style.display   = isMob ? 'none' : 'flex';
                if (right) right.style.padding  = isMob ? '40px 24px 60px' : '56px 64px 64px';
                if (mobileLogo) mobileLogo.style.display  = isMob ? 'block' : 'none';
                if (desktopLogo) desktopLogo.style.display = isMob ? 'none' : 'block';
            }

            authResponsive();
            window.addEventListener('resize', authResponsive);
        </script>
    @endpush
</x-shop::layouts>
