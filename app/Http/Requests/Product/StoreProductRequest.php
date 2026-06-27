<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'price'       => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'type_id'     => ['required', 'exists:types,id'],
            'color_id'    => ['required', 'exists:colors,id'],
            'age'         => ['required', 'integer', 'min:0', 'max:100'],
            'weight'      => ['required', 'integer', 'min:0'],
            'region_id'   => ['required', 'exists:regions,id'],
            'city_id'     => ['required', 'exists:cities,id'],
            'status_id'   => ['required', 'exists:statuses,id'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'nomi',
            'description' => 'tavsif',
            'price'       => 'narx',
            'category_id' => 'kategoriya',
            'type_id'     => 'tur',
            'color_id'    => 'rang',
            'age'         => 'yosh',
            'weight'      => 'vazn',
            'region_id'   => 'viloyat',
            'city_id'     => 'shahar/tuman',
            'status_id'   => 'holat',
            'image'       => 'rasm',
        ];
    }
}
