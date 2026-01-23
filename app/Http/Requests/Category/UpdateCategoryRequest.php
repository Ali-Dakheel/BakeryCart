<?php

declare(strict_types=1);

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id;
        return [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'image_url' => ['nullable', 'url', 'max:500'],
            'icon' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],

            'translations' => ['sometimes', 'array'],
            'translations.*.locale' => ['required_with:translations', 'string', 'in:en,ar'],
            'translations.*.name' => ['required_with:translations', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
        ];
    }
}
