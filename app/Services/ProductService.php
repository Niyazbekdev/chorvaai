<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        return Product::with(['category', 'user', 'type', 'color', 'region', 'city', 'status'])
            ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Sotildi'))
            ->when($filters['category'] ?? null, function ($q, $v) {
                $q->where(function ($sub) use ($v) {
                    $sub->where('category_id', $v)
                        ->orWhereHas('category', fn ($q2) => $q2->where('parent_id', $v));
                });
            })
            ->when($filters['region'] ?? null, fn ($q, $v) => $q->where('region_id', $v))
            ->when($filters['city'] ?? null, fn ($q, $v) => $q->where('city_id', $v))
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->where('type_id', $v))
            ->when($filters['price_from'] ?? null, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($filters['price_to'] ?? null, fn ($q, $v) => $q->where('price', '<=', $v))
            ->latest()
            ->paginate(12);
    }

    public function store(array $data, ?UploadedFile $image, int $userId): Product
    {
        $data['user_id'] = $userId;
        $data['image']   = $image?->store('products', 'public');

        return Product::create($data);
    }

    public function update(Product $product, array $data, ?UploadedFile $image): Product
    {
        if ($image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $image->store('products', 'public');
        }

        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }
}
