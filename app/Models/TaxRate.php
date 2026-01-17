<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'rate',
        'is_inclusive',
        'applies_to',
        'applicable_ids',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_inclusive' => 'boolean',
            'is_active' => 'boolean',
            'applicable_ids' => 'array',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    /**
     * Scope: Only active tax rates
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Currently effective tax rates
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEffective($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $now);
            });
    }

    /**
     * Check if tax applies to specific product
     *
     * @param int $productId
     * @return bool
     */
    public function appliesToProduct(int $productId): bool
    {
        if ($this->applies_to === 'all') {
            return true;
        }

        if ($this->applies_to === 'specific_products') {
            return in_array($productId, $this->applicable_ids ?? []);
        }

        return false;
    }

    /**
     * Check if tax applies to specific category
     *
     * @param int $categoryId
     * @return bool
     */
    public function appliesToCategory(int $categoryId): bool
    {
        if ($this->applies_to === 'all') {
            return true;
        }

        if ($this->applies_to === 'specific_categories') {
            return in_array($categoryId, $this->applicable_ids ?? []);
        }

        return false;
    }
}
