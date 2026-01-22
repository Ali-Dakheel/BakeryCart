<?php

declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['integer', 'min:0'],
            'low_stock_threshold' => ['integer', 'min:0'],
            'track_inventory' => ['boolean'],
            'is_available' => ['boolean'],
            'is_featured' => ['boolean'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after:available_from'],

            // Translations
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.locale' => ['required', 'string', 'in:en,ar'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.short_description' => ['nullable', 'string', 'max:500'],
            'translations.*.description' => ['nullable', 'string'],
        ];
    }
}
