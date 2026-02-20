<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Normalize locale: "en-US" -> "en", "ar-SA" -> "ar"
        $locale = substr($request->header('Accept-Language', 'en'), 0, 2);
        $translation = $this->whenLoaded('translations', fn () => $this->translations->where('locale', $locale)->first()
            ?? $this->translations->where('locale', 'en')->first());

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $translation?->name ?? 'Unnamed Category',
            'description' => $translation?->description,
            'icon' => $this->icon,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'parent' => $this->whenLoaded('parent', function () use ($locale) {
                $parentTranslation = $this->parent->relationLoaded('translations')
                    ? $this->parent->translations->where('locale', $locale)->first()
                    : null;

                return [
                    'id' => $this->parent->id,
                    'slug' => $this->parent->slug,
                    'name' => $parentTranslation?->name ?? $this->parent->slug,
                ];
            }),
            'children' => $this->whenLoaded('children', function () use ($locale) {
                return $this->children
                    ->where('is_active', true)
                    ->sortBy('sort_order')
                    ->map(function ($child) use ($locale) {
                        $childTranslation = $child->relationLoaded('translations')
                            ? $child->translations->where('locale', $locale)->first()
                            : null;

                        return [
                            'id' => $child->id,
                            'slug' => $child->slug,
                            'name' => $childTranslation?->name ?? $child->slug,
                            'icon' => $child->icon,
                        ];
                    })->values();
            }),
            'products' => $this->whenLoaded('products', function () {
                return ProductResource::collection($this->products);
            }),
            'products_count' => $this->when(
                $this->relationLoaded('products'),
                fn () => $this->products->count()
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
