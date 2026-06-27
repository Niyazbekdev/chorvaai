<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        $favorite = $user->favorites()->where('product_id', $product->id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['favorited' => false, 'count' => $product->favorites()->count()]);
        }

        $user->favorites()->create(['product_id' => $product->id]);
        return response()->json(['favorited' => true, 'count' => $product->favorites()->count()]);
    }
}
