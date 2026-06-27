<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function myProducts(Request $request): View
    {
        $user     = $request->user();
        $products = $user->products()->with(['category', 'status'])->latest()->get();

        $statuses   = Status::pluck('id', 'name');
        $faolId     = $statuses['Faol']     ?? null;
        $sotildiId  = $statuses['Sotildi']  ?? null;
        $korilyabdi = $statuses["Ko'rib chiqilmoqda"] ?? null;

        $stats = [
            'total'      => $products->count(),
            'active'     => $products->where('status_id', $faolId)->count(),
            'sold'       => $products->where('status_id', $sotildiId)->count(),
            'pending'    => $products->where('status_id', $korilyabdi)->count(),
            'total_value'=> $products->where('status_id', $faolId)->sum('price'),
        ];

        return view('profile.my-products', compact('user', 'products', 'stats'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
