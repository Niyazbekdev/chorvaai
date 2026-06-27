<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'max:5000'],
            'price'         => ['required', 'integer', 'min:0'],
            'category_id'   => ['required', 'exists:categories,id'],
            'type_id'       => ['nullable', 'exists:types,id'],
            'color_id'      => ['nullable', 'exists:colors,id'],
            'breed'         => ['nullable', 'string', 'max:255'],
            'gender'        => ['nullable', 'in:erkak,urgochi'],
            'health_status' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'age'           => ['required', 'integer', 'min:0', 'max:100'],
            'weight'        => ['required', 'integer', 'min:0'],
            'region_id'     => ['required', 'exists:regions,id'],
            'city_id'       => ['required', 'exists:cities,id'],
            'status_id'     => ['nullable', 'exists:statuses,id'],
            'location'      => ['nullable', 'string', 'max:500'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
            'images'        => ['nullable', 'array', 'max:8'],
            'images.*'      => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'          => 'nomi',
            'description'   => 'tavsif',
            'price'         => 'narx',
            'category_id'   => 'kategoriya',
            'breed'         => 'zoti',
            'gender'        => 'jinsi',
            'health_status' => 'sog\'liq holati',
            'contact_phone' => 'telefon raqam',
            'age'           => 'yosh',
            'weight'        => 'vazn',
            'region_id'     => 'viloyat',
            'city_id'       => 'shahar/tuman',
            'location'      => 'manzil',
            'images'        => 'rasmlar',
        ];
    }
}
