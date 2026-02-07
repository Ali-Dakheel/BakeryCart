<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', 'en');
        $productTranslation = $this->product->translations->where('locale', $locale)->first();
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'product' => [
                'id' => $this->product->id,
                'sku' => $this->product->sku,
                'name' => $productTranslation?->name ?? 'Unnamed Product',
                'images' => $this->product->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'image_path' => $image->image_url,
                        'is_primary' => $image->is_primary,
                    ];
                })->values(),
            ],
            'variant' => $this->when($this->variant, function () use ($locale) {
                return [
                    'id' => $this->variant->id,
                    'name' => $this->variant->name,
                    'sku' => $this->variant->sku,
                ];
            }),
            'quantity' => $this->quantity,
            'price_snapshot' => (float)$this->price,
            'subtotal' => (float)$this->subtotal,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
