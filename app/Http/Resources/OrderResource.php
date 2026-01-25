<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'fulfillment_status' => $this->fulfillment_status,
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
            ],
            'shipping' => [
                'name' => $this->shipping_name,
                'phone' => $this->shipping_phone,
                'address_line_1' => $this->shipping_address_line_1,
                'address_line_2' => $this->shipping_address_line_2,
                'building' => $this->shipping_building,
                'floor' => $this->shipping_floor,
                'apartment' => $this->shipping_apartment,
                'area' => $this->shipping_area,
                'city' => $this->shipping_city,
                'instructions' => $this->delivery_instructions,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'totals' => [
                'subtotal' => (float)$this->subtotal,
                'discount' => (float)$this->discount_amount,
                'coupon_discount' => (float)$this->coupon_discount,
                'tax' => (float)$this->tax_amount,
                'shipping' => (float)$this->shipping_fee,
                'total' => (float)$this->total,
            ],
            'coupon_code' => $this->coupon_code,
            'delivery_date' => $this->delivery_date?->format('Y-m-d'),
            'delivery_time_slot' => $this->delivery_time_slot,
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'customer_notes' => $this->customer_notes,
            'is_cancelable' => $this->is_cancelable,
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
