<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailOtpVerificationController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (!$user->hasVerifiedPhone()) {
            return redirect()->route('phone.verify');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('products.index'));
        }

        $resend = $this->otpService->canResendEmail($user->email);

        return view('auth.verify-email-otp', ['resendSeconds' => $resend['seconds']]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = $request->user();
        $result = $this->otpService->verifyEmail($user->email, $request->code);

        if (!$result['ok']) {
            return back()->withErrors(['code' => $result['error']]);
        }

        $user->update(['email_verified_at' => now()]);

        return redirect()->intended(route('products.index'))
            ->with('success', "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi!");
    }

    public function resend(Request $request): RedirectResponse
    {
        $user   = $request->user();
        $resend = $this->otpService->canResendEmail($user->email);

        if (!$resend['can']) {
            return back()->withErrors([
                'code' => "{$resend['seconds']} soniyadan so'ng qayta yuboring.",
            ]);
        }

        $code = $this->otpService->generateForEmail($user->email);
        Mail::to($user->email)->send(new OtpMail($code));
        session(['dev_email_otp' => $code]);

        return back()->with('status', 'Kod qayta yuborildi.');
    }
}
