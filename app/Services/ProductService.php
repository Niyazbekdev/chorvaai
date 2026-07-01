<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Status;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        return Product::with(['category', 'user', 'color', 'region', 'city', 'status'])
            ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Sotildi'))
            ->when(auth()->id(), fn ($q, $id) => $q->where('user_id', '!=', $id))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $term = '%' . $v . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('category', fn ($q2) => $q2->where('name', 'like', $term))
                        ->orWhereHas('region', fn ($q2) => $q2->where('name', 'like', $term))
                        ->orWhereHas('city', fn ($q2) => $q2->where('name', 'like', $term));
                });
            })
            ->when($filters['category'] ?? null, function ($q, $v) {
                $q->where(function ($sub) use ($v) {
                    $sub->where('category_id', $v)
                        ->orWhereHas('category', fn ($q2) => $q2->where('parent_id', $v));
                });
            })
            ->when($filters['region'] ?? null, fn ($q, $v) => $q->where('region_id', $v))
            ->when($filters['city'] ?? null, fn ($q, $v) => $q->where('city_id', $v))
            ->when($filters['price_from'] ?? null, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($filters['price_to'] ?? null, fn ($q, $v) => $q->where('price', '<=', $v))
            ->when($filters['gender'] ?? null, fn ($q, $v) => $q->where('gender', $v))
            ->when(
                isset($filters['lat'], $filters['lng'], $filters['radius']),
                fn ($q) => $this->filterByRadius($q, $filters['lat'], $filters['lng'], $filters['radius'])
            )
            ->latest()
            ->paginate(12);
    }

    private function filterByRadius($query, float $lat, float $lng, float $radius): void
    {
        $query->whereNotNull('latitude')->whereNotNull('longitude')
            ->whereRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                [$lat, $lng, $lat, $radius]
            );
    }

    public function store(array $data, array $imageFiles, int $userId): Product
    {
        $data['user_id']   = $userId;
        $data['status_id'] = Status::where('name', 'Faol')->value('id') ?? 1;

        if (!empty($imageFiles)) {
            $paths = array_map(fn ($f) => $f->store('products', 'public'), $imageFiles);
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }

        return Product::create($data);
    }

    public function update(Product $product, array $data, array $newImages): Product
    {
        if (!empty($newImages)) {
            foreach ($product->images ?? [] as $old) {
                Storage::disk('public')->delete($old);
            }
            if ($product->image && empty($product->images)) {
                Storage::disk('public')->delete($product->image);
            }

            $paths = array_map(fn ($f) => $f->store('products', 'public'), $newImages);
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }

        $product->update($data);
        return $product;
    }

    public function delete(Product $product): void
    {
        foreach ($product->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        if ($product->image && empty($product->images)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
    }
}
