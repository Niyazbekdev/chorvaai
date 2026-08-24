<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')
                ->withErrors(['login' => "Google orqali kirishda xatolik yuz berdi. Qayta urinib ko'ring."]);
        }

        // google_id bo'yicha topish
        $user = User::where('google_id', $googleUser->getId())->first();

        // Email bo'yicha mavjud akkauntni Google bilan bog'lash
        if (!$user && $googleUser->getEmail()) {
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        // Yangi foydalanuvchi yaratish
        if (!$user) {
            $nameParts    = explode(' ', $googleUser->getName(), 2);
            $customerRole = Role::where('slug', 'customer')->first();

            $user = User::create([
                'google_id'         => $googleUser->getId(),
                'first_name'        => $nameParts[0] ?? $googleUser->getName(),
                'last_name'         => $nameParts[1] ?? '',
                'email'             => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'avatar'            => $googleUser->getAvatar(),
                'role_id'           => $customerRole?->id,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('products.index'));
    }
}
