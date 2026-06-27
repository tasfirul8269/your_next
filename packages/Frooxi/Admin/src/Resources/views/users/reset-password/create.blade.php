<x-admin::layouts.anonymous>
    <x-slot:title>
        @lang('admin::app.users.reset-password.title')
    </x-slot>

    <div id="adm-outer" style="display:flex;min-height:100vh;font-family:'Montserrat',sans-serif;">

        {{-- ── LEFT PANEL ─────────────────────────────────────── --}}
        <div id="adm-left" style="position:relative;flex:0 0 46%;background:#0d0d0d;overflow:hidden;display:flex;flex-direction:column;justify-content:space-between;padding:52px 48px;">
            <div style="position:absolute;inset:0;background:linear-gradient(150deg,#1a1a1a 0%,#080808 100%);"></div>
            <div style="position:absolute;inset:0;opacity:.05;background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:28px 28px;"></div>

            <div style="position:relative;z-index:2;">
                <img src="{{ asset('themes/shop/logo_black.png') }}" alt="{{ config('app.name') }}" style="height:38px;width:auto;">
            </div>

            <div style="position:relative;z-index:2;">
                <span style="display:inline-block;font-size:9px;font-weight:700;letter-spacing:.26em;text-transform:uppercase;color:#a38c5a;margin-bottom:18px;">Account Recovery</span>
                <h2 style="font-size:clamp(26px,3vw,42px);font-weight:500;line-height:1.1;letter-spacing:.05em;text-transform:uppercase;color:#fff;margin:0 0 18px;">
                    Set a new<br>password.
                </h2>
                <p style="font-size:13px;line-height:1.8;color:rgba(255,255,255,.4);max-width:320px;margin:0 0 36px;">
                    Choose a strong password to keep your admin account secure.
                </p>
                <div style="width:36px;height:1.5px;background:#a38c5a;"></div>
            </div>
        </div>

        {{-- ── RIGHT PANEL ──────────────────────────────────────── --}}
        <div id="adm-right" style="flex:1;background:#fafafa;display:flex;flex-direction:column;justify-content:center;padding:64px 72px;overflow-y:auto;">

            <p style="font-size:9px;font-weight:700;letter-spacing:.26em;text-transform:uppercase;color:#9ca3af;margin:0 0 12px;">Administrator</p>

            <h1 style="font-size:clamp(22px,2.5vw,32px);font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:#111;margin:0 0 6px;">
                @lang('admin::app.users.reset-password.title')
            </h1>

            <p style="font-size:13px;color:#6b7280;margin:0 0 36px;">
                Enter your email and your new password below.
            </p>

            <x-admin::form :action="route('admin.reset_password.store')">

                <x-admin::form.control-group.control
                    type="hidden"
                    name="token"
                    :value="$token"
                />

                {{-- Email --}}
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#374151;margin-bottom:8px;">
                        @lang('admin::app.users.reset-password.email')
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.control
                            type="email"
                            class="!rounded-lg !border-zinc-200 !bg-white !px-5 !py-4 !text-sm !font-normal"
                            id="email"
                            name="email"
                            rules="required|email"
                            :label="trans('admin::app.users.reset-password.email')"
                            :placeholder="trans('admin::app.users.reset-password.email')"
                        />
                        <x-admin::form.control-group.error control-name="email" />
                    </x-admin::form.control-group>
                </div>

                {{-- Password --}}
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#374151;margin-bottom:8px;">
                        @lang('admin::app.users.reset-password.password')
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.control
                            type="password"
                            class="!rounded-lg !border-zinc-200 !bg-white !px-5 !py-4 !text-sm !font-normal"
                            id="password"
                            name="password"
                            rules="required|min:6"
                            :label="trans('admin::app.users.reset-password.password')"
                            :placeholder="trans('admin::app.users.reset-password.password')"
                            ref="password"
                        />
                        <x-admin::form.control-group.error control-name="password" />
                    </x-admin::form.control-group>
                </div>

                {{-- Confirm Password --}}
                <div style="margin-bottom:28px;">
                    <label style="display:block;font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#374151;margin-bottom:8px;">
                        @lang('admin::app.users.reset-password.confirm-password')
                        <span style="color:#ef4444;margin-left:2px;">*</span>
                    </label>
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.control
                            type="password"
                            class="!rounded-lg !border-zinc-200 !bg-white !px-5 !py-4 !text-sm !font-normal"
                            id="password_confirmation"
                            name="password_confirmation"
                            rules="confirmed:@password"
                            :label="trans('admin::app.users.reset-password.confirm-password')"
                            :placeholder="trans('admin::app.users.reset-password.confirm-password')"
                            ref="password"
                        />
                        <x-admin::form.control-group.error control-name="password_confirmation" />
                    </x-admin::form.control-group>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    style="width:100%;height:52px;background:#111;color:#fff;border:none;border-radius:8px;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;cursor:pointer;margin-bottom:20px;"
                    onmouseover="this.style.background='#333'"
                    onmouseout="this.style.background='#111'"
                >
                    @lang('admin::app.users.reset-password.submit-btn')
                </button>

                <p style="font-size:13px;color:#6b7280;text-align:center;margin:0;">
                    <a href="{{ route('admin.session.create') }}" style="color:#111;font-weight:600;text-decoration:none;letter-spacing:.04em;">
                        @lang('admin::app.users.reset-password.back-link-title')
                    </a>
                </p>

            </x-admin::form>

            <p style="font-size:11px;color:#d1d5db;text-align:center;margin-top:36px;">
                {!! trans('admin::app.users.reset-password.powered-by-description', [
                    'frooxi' => '<a style="color:#a38c5a;text-decoration:none;font-weight:600;" href="https://frooxi.com/en/">Frooxi</a>',
                ]) !!}
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            function admResponsive() {
                var left  = document.getElementById('adm-left');
                var right = document.getElementById('adm-right');
                var mob   = window.innerWidth < 768;
                if (left)  left.style.display  = mob ? 'none' : 'flex';
                if (right) {
                    right.style.padding        = mob ? '40px 24px 56px' : '64px 72px';
                    right.style.justifyContent = mob ? 'flex-start' : 'center';
                }
            }
            admResponsive();
            window.addEventListener('resize', admResponsive);
        </script>
    @endpush
</x-admin::layouts.anonymous>
