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
            'product_id' => $this->product_id,
            'product' => [
                'id' => $this->product->id,
                'sku' => $this->product->sku,
                'name' => $productTranslation?->name ?? 'Unnamed Product',
                'image' => $this->product->images->where('is_primary', true)->first()?->image_url,
            ],
            'variant' => $this->when($this->variant, function () use ($locale) {
                return [
                    'id' => $this->variant->id,
                    'name' => $this->variant->name,
                    'sku' => $this->variant->sku,
                ];
            }),
            'quantity' => $this->quantity,
            'price' => (float)$this->price,
            'subtotal' => (float)$this->subtotal,
        ];
    }
}
