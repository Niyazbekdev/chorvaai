<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function show(User $seller): View
    {
        $products = $seller->products()
            ->with(['category', 'status', 'region', 'city'])
            ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Sotildi'))
            ->latest()
            ->paginate(12);

        $totalActive = $seller->products()
            ->whereHas('status', fn ($q) => $q->where('name', 'Faol'))
            ->count();

        return view('seller.show', compact('seller', 'products', 'totalActive'));
    }
}
