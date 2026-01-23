<?php

declare(strict_types=1);

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

final class StoreCategoryRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'icon' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.locale' => ['required', 'string', 'in:en,ar'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
        ];
    }
}
