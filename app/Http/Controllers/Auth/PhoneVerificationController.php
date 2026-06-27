<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EskizService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneVerificationController extends Controller
{
    public function __construct(
        private OtpService   $otpService,
        private EskizService $eskizService,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedPhone()) {
            return redirect()->intended(route('products.index'));
        }

        $resend = $this->otpService->canResend($request->user()->phone);

        return view('auth.verify-phone', ['resendSeconds' => $resend['seconds']]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = $request->user();
        $result = $this->otpService->verify($user->phone, $request->code);

        if (!$result['ok']) {
            return back()->withErrors(['code' => $result['error']]);
        }

        $user->update(['phone_verified_at' => now()]);

        return redirect()->intended(route('products.index'))
            ->with('success', 'Telefon raqamingiz tasdiqlandi!');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user   = $request->user();
        $resend = $this->otpService->canResend($user->phone);

        if (!$resend['can']) {
            return back()->withErrors([
                'code' => "{$resend['seconds']} soniyadan so'ng qayta yuboring.",
            ]);
        }

        $code = $this->otpService->generate($user->phone);
        $this->eskizService->send($user->phone, "ChorvaAI: tasdiqlash kodingiz: $code. Amal qilish muddati 5 daqiqa.");

        return back()->with('status', 'Kod qayta yuborildi.');
    }
}
