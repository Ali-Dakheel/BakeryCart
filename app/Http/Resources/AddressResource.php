<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'building_number' => $this->building_number,
            'floor' => $this->floor,
            'apartment' => $this->apartment,
            'area' => $this->area,
            'block' => $this->block,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country ?? 'Bahrain',
            'delivery_instructions' => $this->delivery_instructions,
            'is_default' => (bool)$this->is_default,
            'formatted_address' => $this->formatted_address,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
