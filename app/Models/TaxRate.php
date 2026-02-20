<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class TaxRate extends Model
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEffective(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function (Builder $q) use ($now): void {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $now);
            })
            ->where(function (Builder $q) use ($now): void {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $now);
            });
    }

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
