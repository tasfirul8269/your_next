<?php

namespace Frooxi\Shop\Http\Controllers\Customer;

use Frooxi\Shop\Http\Controllers\Controller;
use Frooxi\Shop\Http\Requests\Customer\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class SessionController extends Controller
{
    /**
     * Display the resource.
     *
     * @return RedirectResponse|View
     */
    public function index()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('shop.home.index');
        }

        return view('shop::customers.sign-in');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(LoginRequest $loginRequest)
    {
        $credentials = $loginRequest->only(['phone', 'password']);

        $credentials['channel_id'] = core()->getCurrentChannel()->id;

        if (! auth()->guard('customer')->attempt($credentials)) {
            session()->flash('error', trans('shop::app.customers.login-form.invalid-credentials'));

            return redirect()->back();
        }

        if (! auth()->guard('customer')->user()->status) {
            auth()->guard('customer')->logout();

            session()->flash('warning', trans('shop::app.customers.login-form.not-activated'));

            return redirect()->back();
        }

        if (! auth()->guard('customer')->user()->is_verified) {
            session()->flash('info', trans('shop::app.customers.login-form.verify-first'));

            Cookie::queue(Cookie::make('enable-resend', 'true', 1));

            Cookie::queue(Cookie::make('phone-for-resend', $loginRequest->get('phone'), 1));

            auth()->guard('customer')->logout();

            return redirect()->back();
        }

        /**
         * Event passed to prepare cart after login.
         */
        Event::dispatch('customer.after.login', auth()->guard()->user());

        if (core()->getConfigData('customer.settings.login_options.redirected_to_page') == 'account') {
            return redirect()->route('shop.customers.account.profile.index');
        }

        return redirect()->route('shop.home.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy()
    {
        $id = auth()->guard('customer')->user()->id;

        auth()->guard('customer')->logout();

        Event::dispatch('customer.after.logout', $id);

        return redirect()->route('shop.home.index');
    }
}
