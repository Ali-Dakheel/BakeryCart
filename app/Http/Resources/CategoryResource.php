<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', 'en');
        $translation = $this->translations->where('locale', $locale)->first()
            ?? $this->translations->where('locale', 'en')->first();
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $translation?->name ?? 'Unnamed Category',
            'description' => $translation?->description,
            'icon' => $this->icon,
            'is_active' => (bool)$this->is_active,
            'sort_order' => $this->sort_order,
            'parent' => $this->whenLoaded('parent', function () use ($locale) {
                $parentTranslation = $this->parent->translations
                    ->where('locale', $locale)->first();
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
                        $childTranslation = $child->translations
                            ->where('locale', $locale)->first();
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
