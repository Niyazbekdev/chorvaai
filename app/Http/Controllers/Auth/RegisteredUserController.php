<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EskizService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private OtpService  $otpService,
        private EskizService $eskizService,
    ) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Telefon raqamni normallashtirish: oxirgi 9 ta raqam + +998 prefiksi
        $digits = preg_replace('/\D/', '', $request->input('phone', ''));
        $request->merge(['phone' => '+998' . substr($digits, -9)]);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'phone'      => ['required', 'string', 'regex:/^\+998\d{9}$/', 'unique:users,phone'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex'  => "Telefon raqam +998XXXXXXXXX formatida bo'lishi kerak.",
            'phone.unique' => 'Bu telefon raqam allaqachon ro\'yxatdan o\'tgan.',
        ]);

        $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'phone_verified_at' => null,
        ]);

        $code = $this->otpService->generate($user->phone);
        $this->eskizService->send($user->phone, "ChorvaAI: tasdiqlash kodingiz: $code. Amal qilish muddati 5 daqiqa.");

        if (config('services.eskiz.test_mode') || app()->environment('local')) {
            \Illuminate\Support\Facades\Log::info('OTP (test)', ['phone' => $user->phone, 'code' => $code]);
            session(['dev_otp' => $code]);
        }

        Auth::login($user);

        return redirect()->route('phone.verify');
    }
}
