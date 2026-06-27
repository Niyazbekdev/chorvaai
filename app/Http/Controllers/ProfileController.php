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
