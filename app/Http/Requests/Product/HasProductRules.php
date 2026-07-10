<?php

namespace App\Http\Requests\Product;

trait HasProductRules
{
    protected function baseRules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:5000'],
            'price'         => ['required', 'integer', 'min:0'],
            'category_id'   => ['required', 'exists:categories,id'],
            'color_id'      => ['nullable', 'exists:colors,id'],
            'gender'        => ['nullable', 'in:erkak,urgochi'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'age'           => ['required', 'integer', 'min:0', 'max:100'],
            'weight'        => ['required', 'integer', 'min:0'],
            'region_id'     => ['required', 'exists:regions,id'],
            'city_id'       => ['required', 'exists:cities,id'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],

            'images'        => ['nullable', 'array', 'max:8'],
            'images.*'      => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'          => 'nomi',
            'description'   => 'tavsif',
            'price'         => 'narx',
            'category_id'   => 'kategoriya',
            'gender'        => 'jinsi',
            'contact_phone' => 'telefon raqam',
            'age'           => 'yosh',
            'weight'        => 'vazn',
            'region_id'     => 'viloyat',
            'city_id'       => 'shahar/tuman',
            'latitude'      => 'xaritada belgi',
            'longitude'     => 'xaritada belgi',
            'images'        => 'rasmlar',
        ];
    }
}
