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

    // ── Step 1: telefon kiritish formasi ─────────────────────────────────────
    public function showPhoneForm(): View
    {
        return view('auth.forgot-password');
    }

    // ── Step 2: OTP yuborish ─────────────────────────────────────────────────
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate(['phone' => ['required', 'string']]);

        $phone = '+998' . preg_replace('/\D/', '', $request->phone);

        if (!User::where('phone', $phone)->exists()) {
            return back()->withErrors(['phone' => "Bu telefon raqam ro'yxatdan o'tmagan."]);
        }

        $resend = $this->otpService->canResend($phone);
        if (!$resend['can']) {
            return back()->withErrors([
                'phone' => "{$resend['seconds']} soniyadan so'ng qayta urinib ko'ring.",
            ]);
        }

        $code = $this->otpService->generate($phone);
        $sent = $this->eskizService->send($phone, "ChorvaAI: parolni tiklash kodi: $code. 5 daqiqa amal qiladi.");

        $request->session()->put('pwd_reset_phone', $phone);

        $flash = [];
        if (!$sent || config('app.env') !== 'production') {
            $flash['dev_otp'] = $code;
        }

        return redirect()->route('password.otp')->with($flash);
    }

    // ── Step 3: OTP kiritish formasi ─────────────────────────────────────────
    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('pwd_reset_phone')) {
            return redirect()->route('password.request');
        }

        $phone  = $request->session()->get('pwd_reset_phone');
        $resend = $this->otpService->canResend($phone);

        return view('auth.reset-password-otp', [
            'phone'         => $phone,
            'resendSeconds' => $resend['seconds'],
        ]);
    }

    // ── Step 4: OTP tasdiqlash ────────────────────────────────────────────────
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $phone = $request->session()->get('pwd_reset_phone');
        if (!$phone) {
            return redirect()->route('password.request');
        }

        $result = $this->otpService->verify($phone, $request->code);

        if (!$result['ok']) {
            return back()->withErrors(['code' => $result['error']]);
        }

        // OTP to'g'ri — parol o'zgartirish uchun ruxsat berish (15 daqiqa)
        $request->session()->forget('pwd_reset_phone');
        $request->session()->put('pwd_reset_verified_phone', $phone);
        $request->session()->put('pwd_reset_expires', now()->addMinutes(15)->timestamp);

        return redirect()->route('password.reset');
    }

    // ── OTP qayta yuborish ────────────────────────────────────────────────────
    public function resendOtp(Request $request): RedirectResponse
    {
        $phone = $request->session()->get('pwd_reset_phone');
        if (!$phone) {
            return redirect()->route('password.request');
        }

        $resend = $this->otpService->canResend($phone);
        if (!$resend['can']) {
            return back()->withErrors(['code' => "{$resend['seconds']} soniyadan so'ng qayta yuboring."]);
        }

        $code = $this->otpService->generate($phone);
        $sent = $this->eskizService->send($phone, "ChorvaAI: parolni tiklash kodi: $code. 5 daqiqa amal qiladi.");

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
                ->withErrors(['phone' => 'Sessiya tugagan yoki noto\'g\'ri. Qaytadan boshlang.']);
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
                ->withErrors(['phone' => 'Sessiya tugagan. Qaytadan boshlang.']);
        }

        $phone = $request->session()->get('pwd_reset_verified_phone');
        $user  = User::where('phone', $phone)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        $user->update(['password' => Hash::make($request->password)]);

        $request->session()->forget(['pwd_reset_verified_phone', 'pwd_reset_expires']);

        return redirect()->route('login')
            ->with('status', 'Parol muvaffaqiyatli yangilandi. Endi kira olasiz!');
    }

    private function hasValidResetSession(Request $request): bool
    {
        return $request->session()->has('pwd_reset_verified_phone')
            && $request->session()->has('pwd_reset_expires')
            && now()->timestamp < $request->session()->get('pwd_reset_expires');
    }
}
