<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Status;
use App\Models\User;
use App\Services\EskizService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
