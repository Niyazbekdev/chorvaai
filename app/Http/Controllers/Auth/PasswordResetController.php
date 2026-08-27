<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EskizService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(
        private OtpService   $otpService,
        private EskizService $eskizService,
    ) {}

    public function showPhoneForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate(['phone' => ['required', 'string']]);

        $digits = preg_replace('/\D/', '', $request->input('phone'));
        $phone  = '+998' . substr($digits, -9);
        $user   = User::where('phone', $phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => __('auth.not_registered')]);
        }

        $resend = $this->otpService->canResend($phone);
        if (!$resend['can']) {
            return back()->withErrors(['phone' => $resend['seconds'] . ' ' . __('auth.resend_wait')]);
        }

        $code = $this->otpService->generate($phone);
        $sent = $this->eskizService->send($phone, "ChorvaAI: parolni tiklash kodi: $code. 5 daqiqa amal qiladi.");

        $request->session()->put('pwd_reset_identifier', $phone);

        $flash = [];
        if (!$sent || config('app.env') !== 'production') {
            $flash['dev_otp'] = $code;
        }

        return redirect()->route('password.otp')->with($flash);
    }

    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('pwd_reset_identifier')) {
            return redirect()->route('password.request');
        }

        $identifier = $request->session()->get('pwd_reset_identifier');
        $resend     = $this->otpService->canResend($identifier);

        return view('auth.reset-password-otp', [
            'identifier'    => $identifier,
            'resendSeconds' => $resend['seconds'],
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $identifier = $request->session()->get('pwd_reset_identifier');

        if (!$identifier) {
            return redirect()->route('password.request');
        }

        $result = $this->otpService->verify($identifier, $request->code);

        if (!$result['ok']) {
            return back()->withErrors(['code' => $result['error']]);
        }

        $request->session()->forget('pwd_reset_identifier');
        $request->session()->put('pwd_reset_verified_identifier', $identifier);
        $request->session()->put('pwd_reset_expires', now()->addMinutes(15)->timestamp);

        return redirect()->route('password.reset');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $identifier = $request->session()->get('pwd_reset_identifier');

        if (!$identifier) {
            return redirect()->route('password.request');
        }

        $resend = $this->otpService->canResend($identifier);
        if (!$resend['can']) {
            return back()->withErrors(['code' => $resend['seconds'] . ' ' . __('auth.resend_wait')]);
        }

        $code = $this->otpService->generate($identifier);
        $sent = $this->eskizService->send($identifier, "ChorvaAI: parolni tiklash kodi: $code. 5 daqiqa amal qiladi.");

        $flash = ['status' => 'Kod qayta yuborildi.'];
        if (!$sent || config('app.env') !== 'production') {
            $flash['dev_otp'] = $code;
        }

        return back()->with($flash);
    }

    public function showNewPasswordForm(Request $request): View|RedirectResponse
    {
        if (!$this->hasValidResetSession($request)) {
            return redirect()->route('password.request')
                ->withErrors(['phone' => __('auth.session_expired')]);
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!$this->hasValidResetSession($request)) {
            return redirect()->route('password.request')
                ->withErrors(['phone' => __('auth.session_expired')]);
        }

        $identifier = $request->session()->get('pwd_reset_verified_identifier');
        $user       = User::where('phone', $identifier)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        $user->update(['password' => Hash::make($request->password)]);

        $request->session()->forget(['pwd_reset_verified_identifier', 'pwd_reset_expires']);

        return redirect()->route('login')->with('status', __('auth.password_updated'));
    }

    private function hasValidResetSession(Request $request): bool
    {
        return $request->session()->has('pwd_reset_verified_identifier')
            && $request->session()->has('pwd_reset_expires')
            && now()->timestamp < $request->session()->get('pwd_reset_expires');
    }
}
