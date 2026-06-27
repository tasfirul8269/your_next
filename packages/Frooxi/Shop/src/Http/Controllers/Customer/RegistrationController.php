<?php

namespace Frooxi\Shop\Http\Controllers\Customer;

use Cookie;
use Frooxi\Customer\Repositories\CustomerGroupRepository;
use Frooxi\Customer\Repositories\CustomerRepository;
use Frooxi\Customer\Services\OtpService;
use Frooxi\Shop\Http\Controllers\Controller;
use Frooxi\Shop\Http\Requests\Customer\RegistrationRequest;
use Frooxi\Shop\Mail\Customer\RegistrationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerGroupRepository $customerGroupRepository,
        protected OtpService $otpService,
    ) {}

    /**
     * Opens up the user's sign up form.
     *
     * @return View
     */
    public function index()
    {
        return view('shop::customers.sign-up');
    }

    /**
     * Method to store user's sign up form data to DB.
     *
     * @return Response
     */
    public function store(RegistrationRequest $registrationRequest)
    {
        $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');

        // Prepare customer data but DON'T create account yet
        $data = array_merge($registrationRequest->only([
            'first_name',
            'last_name',
            'phone',
            'password_confirmation',
        ]), [
            'password' => bcrypt(request()->input('password')),
            'api_token' => Str::random(80),
            'is_verified' => 0,
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => $customerGroup])->id,
            'channel_id' => core()->getCurrentChannel()->id,
            'token' => md5(uniqid(rand(), true)),
        ]);

        // Generate OTP
        $otpData = $this->otpService->generateOtp($data['phone']);

        if (!$otpData) {
            session()->flash('error', 'We could not generate the verification code. Please try again later.');

            return redirect()->back()->withInput();
        }

        // Send OTP via SMS BEFORE creating account
        if (!$this->otpService->sendOtp($data['phone'], $otpData['plain'])) {
            session()->flash('error', 'We could not send the verification code. Please try again later.');

            return redirect()->back()->withInput();
        }

        // SMS sent successfully - store data in session for OTP verification
        session()->put('pending_registration', [
            'customer_data' => $data,
            'otp_hashed' => $otpData['hashed'],
            'otp_expires_at' => $otpData['expires_at'],
        ]);

        Event::dispatch('customer.registration.before');

        return redirect()->route('shop.customers.verify-otp');
    }

    /**
     * Method to verify account.
     *
     * @param  string  $token
     * @return Response
     */
    public function verifyAccount($token)
    {
        $customer = $this->customerRepository->findOneByField('token', $token);

        if ($customer) {
            $this->customerRepository->update([
                'is_verified' => 1,
                'token' => null,
            ], $customer->id);

            if ((bool) core()->getConfigData('emails.general.notifications.emails.general.notifications.registration')) {
                Mail::queue(new RegistrationNotification($customer));
            }

            $this->customerRepository->syncNewRegisteredCustomerInformation($customer);

            session()->flash('success', trans('shop::app.customers.signup-form.verified'));
        } else {
            session()->flash('warning', trans('shop::app.customers.signup-form.verify-failed'));
        }

        return redirect()->route('shop.customer.session.index');
    }

    /**
     * Show the OTP verification form.
     *
     * @return RedirectResponse|View
     */
    public function showOtpForm()
    {
        $pendingRegistration = session('pending_registration');
    
        if (!$pendingRegistration) {
            return redirect()->route('shop.customers.register.index');
        }
    
        $phone = $pendingRegistration['customer_data']['phone'];
        $maskedPhone = $this->maskPhone($phone);
    
        return view('shop::customers.verify-otp', compact('maskedPhone'));
    }

    /**
     * Verify the OTP submitted by the customer.
     *
     * @return RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);
    
        $pendingRegistration = session('pending_registration');
    
        if (!$pendingRegistration) {
            session()->flash('error', 'Registration session expired. Please register again.');
            return redirect()->route('shop.customers.register.index');
        }
    
        $otpHashed = $pendingRegistration['otp_hashed'];
        $otpExpiresAt = $pendingRegistration['otp_expires_at'];
        $customerData = $pendingRegistration['customer_data'];
    
        // Check if OTP expired
        if ($otpExpiresAt && now()->greaterThan($otpExpiresAt)) {
            session()->forget('pending_registration');
            session()->flash('error', 'OTP has expired. Please register again.');
            return redirect()->route('shop.customers.register.index');
        }
    
        // Verify OTP
        if (!Hash::check($request->otp, $otpHashed)) {
            session()->flash('error', 'Invalid OTP. Please try again.');
            return redirect()->back();
        }
    
        // OTP verified - now create the customer account
        Event::dispatch('customer.registration.before');
    
        $customer = $this->customerRepository->create($customerData);
    
        Event::dispatch('customer.create.after', $customer);
        Event::dispatch('customer.registration.after', $customer);
    
        // Clear pending registration session
        session()->forget('pending_registration');
    
        // Auto-login the customer
        auth()->guard('customer')->login($customer);
    
        session()->flash('success', 'Your account has been created successfully!');
    
        return redirect()->route('shop.customers.account.profile.index');
    }

    /**
     * Resend OTP to the customer's phone.
     *
     * @return RedirectResponse
     */
    public function resendOtp()
    {
        $pendingRegistration = session('pending_registration');
    
        if (!$pendingRegistration) {
            session()->flash('error', 'Registration session expired. Please register again.');
            return redirect()->route('shop.customers.register.index');
        }
    
        $phone = $pendingRegistration['customer_data']['phone'];
    
        // Generate new OTP
        $otpData = $this->otpService->generateOtp($phone);
    
        if (!$otpData) {
            session()->flash('error', 'Could not generate OTP. Please try again.');
            return redirect()->back();
        }
    
        // Send new OTP
        if (!$this->otpService->sendOtp($phone, $otpData['plain'])) {
            session()->flash('error', 'Could not send OTP. Please try again.');
            return redirect()->back();
        }
    
        // Update session with new OTP
        session()->put('pending_registration', [
            'customer_data' => $pendingRegistration['customer_data'],
            'otp_hashed' => $otpData['hashed'],
            'otp_expires_at' => $otpData['expires_at'],
        ]);
    
        session()->flash('success', 'A new OTP has been sent to your phone.');
    
        return redirect()->back();
    }

    /**
     * Mask a phone number for display.
     */
    private function maskPhone(string $phone): string
    {
        $length = strlen($phone);

        if ($length <= 4) {
            return $phone;
        }

        return substr($phone, 0, 4).str_repeat('*', $length - 7).substr($phone, -3);
    }
}
