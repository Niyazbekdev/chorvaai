<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Role;
use App\Models\User;
use App\Services\EskizService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private OtpService   $otpService,
        private EskizService $eskizService,
    ) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $digits = preg_replace('/\D/', '', $request->input('phone', ''));
        $request->merge(['phone' => '+998' . substr($digits, -9)]);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'phone'      => ['required', 'string', 'regex:/^\+998\d{9}$/'],
            'email'      => ['required', 'string', 'email', 'max:255'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex'    => "Telefon raqam +998XXXXXXXXX formatida bo'lishi kerak.",
            'email.required' => "Email manzil kiritish majburiy.",
            'email.email'    => "To'g'ri email manzil kiriting.",
        ]);

        // Telefon raqam bilan mavjud foydalanuvchini tekshirish
        $existingByPhone = User::where('phone', $request->phone)->first();
        if ($existingByPhone) {
            if ($existingByPhone->phone_verified_at !== null) {
                return back()->withErrors(['phone' => "Bu telefon raqam allaqachon ro'yxatdan o'tgan."])->withInput();
            }
            $existingByPhone->delete();
        }

        // Email bilan mavjud foydalanuvchini tekshirish
        $existingByEmail = User::where('email', $request->email)->first();
        if ($existingByEmail) {
            if ($existingByEmail->email_verified_at !== null) {
                return back()->withErrors(['email' => "Bu email manzil allaqachon ro'yxatdan o'tgan."])->withInput();
            }
            $existingByEmail->delete();
        }

        $customerRole = Role::where('slug', 'customer')->first();

        $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'phone_verified_at' => null,
            'email_verified_at' => null,
            'role_id'           => $customerRole?->id,
        ]);

        $code = $this->otpService->generate($user->phone);
        $this->eskizService->send($user->phone, "ChorvaAI: tasdiqlash kodingiz: $code. Amal qilish muddati 5 daqiqa.");

        session(['dev_otp' => $code]);

        Auth::login($user);

        return redirect()->route('phone.verify');
    }
}
