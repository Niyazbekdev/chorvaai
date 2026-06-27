<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Status;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    use AuthorizesRequests;

    public function markAsSold(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $request->validate([
            'sold_price' => ['nullable', 'integer', 'min:0'],
            'source'     => ['nullable', 'in:platform_chat,phone_call,outside'],
        ]);

        $soldStatus = Status::where('name', 'Sotildi')->first();

        if (!$soldStatus) {
            return redirect()->back()->with('error', '"Sotildi" statusi topilmadi.');
        }

        Sale::create([
            'product_id' => $product->id,
            'seller_id'  => $request->user()->id,
            'buyer_id'   => null,
            'sold_price' => $request->sold_price ?? $product->price,
            'sold_at'    => now(),
            'source'     => $request->source ?? 'outside',
        ]);

        $product->update(['status_id' => $soldStatus->id]);

        return redirect()->route('profile.my-products')
            ->with('success', "«{$product->name}» mahsuloti sotildi deb belgilandi.");
    }
}
