<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class DeliveryZone extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'base_fee',
        'free_delivery_threshold',
        'estimated_delivery_time',
        'is_active',
        'sort_order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'base_fee' => 'decimal:3',
            'free_delivery_threshold' => 'decimal:3',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function areas(): HasMany
    {
        return $this->hasMany(DeliveryZoneArea::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function calculateFee(float $cartTotal): float
    {
        if ($this->free_delivery_threshold && $cartTotal >= $this->free_delivery_threshold) {
            return 0.0;
        }

        return (float) $this->base_fee;
    }
}
