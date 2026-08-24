<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\EskizService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(
        private OtpService   $otpService,
        private EskizService $eskizService,
    ) {}

    // ── Step 1: identifier kiritish formasi ──────────────────────────────────
    public function showPhoneForm(): View
    {
        return view('auth.forgot-password');
    }

    // ── Step 2: OTP yuborish ─────────────────────────────────────────────────
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate(['identifier' => ['required', 'string']]);

        $raw = trim($request->input('identifier'));

        if (str_contains($raw, '@')) {
            // Email orqali tiklash
            $email = $raw;
            $user  = User::where('email', $email)->first();

            if (!$user) {
                return back()->withErrors(['identifier' => "Bu email manzil ro'yxatdan o'tmagan."]);
            }

            $resend = $this->otpService->canResendEmail($email);
            if (!$resend['can']) {
                return back()->withErrors(['identifier' => $resend['seconds'] . ' ' . __('auth.resend_wait')]);
            }

            $code = $this->otpService->generateForEmail($email);
            Mail::to($email)->send(new OtpMail($code));

            $request->session()->put('pwd_reset_identifier', $email);
            $request->session()->put('pwd_reset_type', 'email');

            return redirect()->route('password.otp')->with('dev_email_otp', $code);
        }

        // Telefon orqali tiklash
        $digits = preg_replace('/\D/', '', $raw);
        $phone  = '+998' . substr($digits, -9);
        $user   = User::where('phone', $phone)->first();

        if (!$user) {
            return back()->withErrors(['identifier' => __('auth.not_registered')]);
        }

        $resend = $this->otpService->canResend($phone);
        if (!$resend['can']) {
            return back()->withErrors(['identifier' => $resend['seconds'] . ' ' . __('auth.resend_wait')]);
        }

        $code = $this->otpService->generate($phone);
        $sent = $this->eskizService->send($phone, "ChorvaAI: parolni tiklash kodi: $code. 5 daqiqa amal qiladi.");

        $request->session()->put('pwd_reset_identifier', $phone);
        $request->session()->put('pwd_reset_type', 'phone');

        $flash = [];
        if (!$sent || config('app.env') !== 'production') {
            $flash['dev_otp'] = $code;
        }

        return redirect()->route('password.otp')->with($flash);
    }

    // ── Step 3: OTP kiritish formasi ─────────────────────────────────────────
    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('pwd_reset_identifier')) {
            return redirect()->route('password.request');
        }

        $identifier = $request->session()->get('pwd_reset_identifier');
        $type       = $request->session()->get('pwd_reset_type', 'phone');

        $resend = $type === 'email'
            ? $this->otpService->canResendEmail($identifier)
            : $this->otpService->canResend($identifier);

        return view('auth.reset-password-otp', [
            'identifier'    => $identifier,
            'type'          => $type,
            'resendSeconds' => $resend['seconds'],
        ]);
    }

    // ── Step 4: OTP tasdiqlash ────────────────────────────────────────────────
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $identifier = $request->session()->get('pwd_reset_identifier');
        $type       = $request->session()->get('pwd_reset_type', 'phone');

        if (!$identifier) {
            return redirect()->route('password.request');
        }

        $result = $type === 'email'
            ? $this->otpService->verifyEmail($identifier, $request->code)
            : $this->otpService->verify($identifier, $request->code);

        if (!$result['ok']) {
            return back()->withErrors(['code' => $result['error']]);
        }

        $request->session()->forget(['pwd_reset_identifier', 'pwd_reset_type']);
        $request->session()->put('pwd_reset_verified_identifier', $identifier);
        $request->session()->put('pwd_reset_verified_type', $type);
        $request->session()->put('pwd_reset_expires', now()->addMinutes(15)->timestamp);

        return redirect()->route('password.reset');
    }

    // ── OTP qayta yuborish ────────────────────────────────────────────────────
    public function resendOtp(Request $request): RedirectResponse
    {
        $identifier = $request->session()->get('pwd_reset_identifier');
        $type       = $request->session()->get('pwd_reset_type', 'phone');

        if (!$identifier) {
            return redirect()->route('password.request');
        }

        if ($type === 'email') {
            $resend = $this->otpService->canResendEmail($identifier);
            if (!$resend['can']) {
                return back()->withErrors(['code' => $resend['seconds'] . ' ' . __('auth.resend_wait')]);
            }

            $code = $this->otpService->generateForEmail($identifier);
            Mail::to($identifier)->send(new OtpMail($code));

            return back()->with(['status' => 'Kod qayta yuborildi.', 'dev_email_otp' => $code]);
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

    // ── Step 5: yangi parol formasi ───────────────────────────────────────────
    public function showNewPasswordForm(Request $request): View|RedirectResponse
    {
        if (!$this->hasValidResetSession($request)) {
            return redirect()->route('password.request')
                ->withErrors(['identifier' => __('auth.session_expired')]);
        }

        return view('auth.reset-password');
    }

    // ── Step 6: parolni saqlash ───────────────────────────────────────────────
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!$this->hasValidResetSession($request)) {
            return redirect()->route('password.request')
                ->withErrors(['identifier' => __('auth.session_expired')]);
        }

        $identifier = $request->session()->get('pwd_reset_verified_identifier');
        $type       = $request->session()->get('pwd_reset_verified_type', 'phone');

        $user = $type === 'email'
            ? User::where('email', $identifier)->first()
            : User::where('phone', $identifier)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        $user->update(['password' => Hash::make($request->password)]);

        $request->session()->forget(['pwd_reset_verified_identifier', 'pwd_reset_verified_type', 'pwd_reset_expires']);

        return redirect()->route('login')->with('status', __('auth.password_updated'));
    }

    private function hasValidResetSession(Request $request): bool
    {
        return $request->session()->has('pwd_reset_verified_identifier')
            && $request->session()->has('pwd_reset_expires')
            && now()->timestamp < $request->session()->get('pwd_reset_expires');
    }
}
