<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class WishlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Normalize locale: "en-US" -> "en", "ar-SA" -> "ar"
        $locale = substr($request->header('Accept-Language', 'en'), 0, 2);
        $productTranslation = $this->product
            ->translations->where('locale', $locale)->first();
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'sku' => $this->product->sku,
                'name' => $productTranslation?->name ?? 'Unnamed Product',
                'price' => (float)$this->product->price,
                'image' => $this->product->images->where('is_primary', true)->first()?->image_url,
                'is_available' => (bool)$this->product->is_available,
            ],
            'added_at' => $this->added_at?->toIso8601String(),
        ];

    }
}
