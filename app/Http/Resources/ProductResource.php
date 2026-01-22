<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', 'en');
        $translation = $this->translations->where('locale', $locale)->first()
            ?? $this->translations->where('locale', 'en')->first();
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'name' => $translation?->name ?? 'Unnamed Product',
            'short_description' => $translation?->short_description,
            'description' => $translation?->description,
            'price' => (float)$this->price,
            'compare_at_price' => $this->compare_at_price ? (float)$this->compare_at_price : null,
            'in_stock' => !$this->track_inventory || $this->current_stock > 0,
            'is_available' => (bool)$this->is_available,
            'is_featured' => (bool)$this->is_featured,
            'category' => $this->whenLoaded('category', function () use ($locale) {
                $catTranslation = $this->category->translations
                    ->where('locale', $locale)->first();
                return [
                    'id' => $this->category->id,
                    'slug' => $this->category->slug,
                    'name' => $catTranslation?->name ?? $this->category->slug,
                ];
            }),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->sortBy('sort_order')->map(fn($img) => [
                    'id' => $img->id,
                    'url' => $img->image_url,
                    'alt' => $img->alt_text,
                    'is_primary' => (bool)$img->is_primary,
                ])->values();
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->where('is_available', true)
                    ->sortBy('sort_order')->map(fn($v) => [
                        'id' => $v->id,
                        'name' => $v->name,
                        'sku' => $v->sku,
                        'price' => (float)$v->price,
                        'pack_quantity' => $v->pack_quantity,
                    ])->values();
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
