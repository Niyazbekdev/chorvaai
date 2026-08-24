<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Mail\OtpMail;
use App\Models\Status;
use App\Models\User;
use App\Services\EskizService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private OtpService   $otpService,
        private EskizService $eskizService,
    ) {}
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function myProducts(Request $request): View
    {
        $user     = $request->user();
        $products = $user->products()
            ->with(['category', 'status'])
            ->withCount(['favorites', 'conversations', 'contactEvents as phone_views_count' => fn ($q) => $q->where('type', 'phone_view')])
            ->latest()
            ->get();

        $statuses   = Status::pluck('id', 'name');
        $faolId     = $statuses['Faol']     ?? null;
        $sotildiId  = $statuses['Sotildi']  ?? null;
        $korilyabdi = $statuses["Ko'rib chiqilmoqda"] ?? null;

        $stats = [
            'total'       => $products->count(),
            'active'      => $products->where('status_id', $faolId)->count(),
            'sold'        => $products->where('status_id', $sotildiId)->count(),
            'pending'     => $products->where('status_id', $korilyabdi)->count(),
            'total_value' => $products->where('status_id', $faolId)->sum('price'),
            'total_views' => $products->sum('views_count'),
        ];

        return view('profile.my-products', compact('user', 'products', 'stats'));
    }

    public function favorites(Request $request): View
    {
        $user     = $request->user();
        $products = $user->favoriteProducts()
            ->with(['category', 'status', 'region', 'city'])
            ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Sotildi'))
            ->latest('favorites.created_at')
            ->paginate(12);

        return view('profile.favorites', compact('user', 'products'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->except('avatar');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->fill($data)->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // ── Telefon o'zgartirish: OTP yuborish ───────────────────────────────────
    public function requestPhoneChange(Request $request): RedirectResponse
    {
        $request->validate(['new_phone' => ['required', 'string']]);

        $digits   = preg_replace('/\D/', '', $request->new_phone);
        $newPhone = '+998' . substr($digits, -9);

        if ($newPhone === $request->user()->phone) {
            return back()->withErrors(['new_phone' => __('profile.phone_same')]);
        }

        if (User::where('phone', $newPhone)->exists()) {
            return back()->withErrors(['new_phone' => __('profile.phone_taken')]);
        }

        $resend = $this->otpService->canResend($newPhone);
        if (!$resend['can']) {
            return back()->withErrors(['new_phone' => $resend['seconds'] . ' ' . __('auth.resend_wait')]);
        }

        $code = $this->otpService->generate($newPhone);
        $sent = $this->eskizService->send($newPhone, "ChorvaAI: telefon tasdiqlash kodi: $code. 5 daqiqa amal qiladi.");

        $request->session()->put('phone_change_pending', $newPhone);

        $flash = [];
        if (!$sent || config('app.env') !== 'production') {
            $flash['dev_otp_change'] = $code;
        }

        return redirect()->route('profile.edit')->with($flash);
    }

    // ── Telefon o'zgartirish: OTP tasdiqlash ─────────────────────────────────
    public function verifyPhoneChange(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $newPhone = $request->session()->get('phone_change_pending');
        if (!$newPhone) {
            return redirect()->route('profile.edit');
        }

        $result = $this->otpService->verify($newPhone, $request->code);

        if (!$result['ok']) {
            return back()->withErrors(['code' => $result['error']])
                ->with('phone_change_pending', $newPhone);
        }

        $request->user()->update([
            'phone'             => $newPhone,
            'phone_verified_at' => now(),
        ]);

        $request->session()->forget('phone_change_pending');

        return redirect()->route('profile.edit')->with('status', 'phone-updated');
    }

    // ── Telefon o'zgartirish: OTP qayta yuborish ─────────────────────────────
    public function resendPhoneOtp(Request $request): RedirectResponse
    {
        $newPhone = $request->session()->get('phone_change_pending');
        if (!$newPhone) {
            return redirect()->route('profile.edit');
        }

        $resend = $this->otpService->canResend($newPhone);
        if (!$resend['can']) {
            return back()->withErrors(['code' => $resend['seconds'] . ' ' . __('auth.resend_wait')])
                ->with('phone_change_pending', $newPhone);
        }

        $code = $this->otpService->generate($newPhone);
        $sent = $this->eskizService->send($newPhone, "ChorvaAI: telefon tasdiqlash kodi: $code. 5 daqiqa amal qiladi.");

        $flash = ['status' => 'phone-otp-resent', 'phone_change_pending' => $newPhone];
        if (!$sent || config('app.env') !== 'production') {
            $flash['dev_otp_change'] = $code;
        }

        return redirect()->route('profile.edit')->with($flash);
    }

    // ── Telefon o'zgartirish: bekor qilish ───────────────────────────────────
    public function cancelPhoneChange(Request $request): RedirectResponse
    {
        $request->session()->forget('phone_change_pending');
        return redirect()->route('profile.edit');
    }

    // ── Email o'zgartirish: OTP yuborish ─────────────────────────────────────
    public function requestEmailChange(Request $request): RedirectResponse
    {
        $request->validate(['new_email' => ['required', 'string', 'email', 'max:255']]);

        $newEmail = trim($request->new_email);

        if ($newEmail === $request->user()->email) {
            return back()->withErrors(['new_email' => __('profile.email_same')]);
        }

        if (User::where('email', $newEmail)->exists()) {
            return back()->withErrors(['new_email' => __('profile.email_taken')]);
        }

        $resend = $this->otpService->canResendEmail($newEmail);
        if (!$resend['can']) {
            return back()->withErrors(['new_email' => $resend['seconds'] . ' ' . __('auth.resend_wait')]);
        }

        $code = $this->otpService->generateForEmail($newEmail);
        Mail::to($newEmail)->send(new OtpMail($code));

        $request->session()->put('email_change_pending', $newEmail);

        return redirect()->route('profile.edit')->with('dev_otp_email_change', $code);
    }

    // ── Email o'zgartirish: OTP tasdiqlash ───────────────────────────────────
    public function verifyEmailChange(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $newEmail = $request->session()->get('email_change_pending');
        if (!$newEmail) {
            return redirect()->route('profile.edit');
        }

        $result = $this->otpService->verifyEmail($newEmail, $request->code);

        if (!$result['ok']) {
            return back()->withErrors(['email_code' => $result['error']])
                ->with('email_change_pending', $newEmail);
        }

        $request->user()->update([
            'email'             => $newEmail,
            'email_verified_at' => now(),
        ]);

        $request->session()->forget('email_change_pending');

        return redirect()->route('profile.edit')->with('status', 'email-updated');
    }

    // ── Email o'zgartirish: OTP qayta yuborish ───────────────────────────────
    public function resendEmailOtp(Request $request): RedirectResponse
    {
        $newEmail = $request->session()->get('email_change_pending');
        if (!$newEmail) {
            return redirect()->route('profile.edit');
        }

        $resend = $this->otpService->canResendEmail($newEmail);
        if (!$resend['can']) {
            return back()->withErrors(['email_code' => $resend['seconds'] . ' ' . __('auth.resend_wait')])
                ->with('email_change_pending', $newEmail);
        }

        $code = $this->otpService->generateForEmail($newEmail);
        Mail::to($newEmail)->send(new OtpMail($code));

        return redirect()->route('profile.edit')
            ->with(['status' => 'email-otp-resent', 'email_change_pending' => $newEmail, 'dev_otp_email_change' => $code]);
    }

    // ── Email o'zgartirish: bekor qilish ─────────────────────────────────────
    public function cancelEmailChange(Request $request): RedirectResponse
    {
        $request->session()->forget('email_change_pending');
        return redirect()->route('profile.edit');
    }
}
