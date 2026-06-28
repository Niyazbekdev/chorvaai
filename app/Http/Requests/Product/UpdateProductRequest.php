<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    use HasProductRules;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'status_id' => ['nullable', 'exists:statuses,id'],
        ]);
    }
}
