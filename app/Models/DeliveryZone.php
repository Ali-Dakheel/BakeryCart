<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
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
    /**
     * Get areas in this delivery zone
     *
     * @return HasMany<DeliveryZoneArea>
     */
    public function areas(): HasMany
    {
        return $this->hasMany(DeliveryZoneArea::class);
    }

    /**
     * Scope: Only active zones
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sort_order
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Calculate delivery fee for given cart total
     *
     * @param float $cartTotal
     * @return float
     */
    public function calculateFee(float $cartTotal): float
    {
        if ($this->free_delivery_threshold && $cartTotal >= $this->free_delivery_threshold) {
            return 0.0;
        }

        return (float) $this->base_fee;
    }

}
