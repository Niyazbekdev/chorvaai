<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductContactEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactEventController extends Controller
{
    public function store(Request $request, Product $product): JsonResponse
    {
        $request->validate(['type' => 'required|in:phone_view,call_click,message_click']);

        ProductContactEvent::create([
            'product_id' => $product->id,
            'viewer_id'  => $request->user()?->id,
            'seller_id'  => $product->user_id,
            'type'       => $request->type,
            'ip_address' => $request->ip(),
        ]);

        $phone = $product->contact_phone ?? $product->user?->phone;

        return response()->json(['ok' => true, 'phone' => $phone]);
    }
}
