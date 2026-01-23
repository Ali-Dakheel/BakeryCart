<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'sku' => ['sometimes', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['sometimes', 'integer', 'min:0'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:0'],
            'track_inventory' => ['sometimes', 'boolean'],
            'is_available' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after:available_from'],

            // Translations (optional on update)
            'translations' => ['sometimes', 'array'],
            'translations.*.locale' => ['required_with:translations', 'string', 'in:en,ar'],
            'translations.*.name' => ['required_with:translations', 'string', 'max:255'],
            'translations.*.short_description' => ['nullable', 'string', 'max:500'],
            'translations.*.description' => ['nullable', 'string'],
        ];
    }
}
