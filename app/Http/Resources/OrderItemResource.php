<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'product_sku' => $this->product_sku,
            'variant_name' => $this->variant_name,
            'image_url' => $this->image_url,
            'quantity' => $this->quantity,
            'unit_price' => (float)$this->unit_price,
            'subtotal' => (float)$this->subtotal,
            'discount_amount' => (float)$this->discount_amount,
            'tax_amount' => (float)$this->tax_amount,
            'total' => (float)$this->total,
            'special_instructions' => $this->special_instructions,
        ];
    }
}
