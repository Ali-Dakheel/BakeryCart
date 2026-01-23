<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'totals' => $this->when(
                isset($this->totals),
                fn() => [
                    'subtotal' => (float)($this->totals['subtotal'] ?? 0),
                    'item_count' => $this->totals['item_count'] ?? 0,
                ]
            ),
            'expires_at' => $this->expires_at?->toIso8601String(),
        ];
    }
}
