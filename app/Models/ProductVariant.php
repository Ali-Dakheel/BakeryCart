<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProductVariant extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'stock',
        'pack_quantity',
        'weight_grams',
        'is_available',
        'sort_order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'stock' => 'integer',
            'pack_quantity' => 'integer',
            'weight_grams' => 'integer',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * @throws \Exception If insufficient stock
     */
    public function decrementStock(int $quantity): bool
    {
        if (! $this->product->track_inventory) {
            return true;
        }

        if ($this->stock < $quantity) {
            throw new \Exception('Insufficient stock for variant');
        }

        return $this->decrement('stock', $quantity);
    }

    public function incrementStock(int $quantity): void
    {
        if (! $this->product->track_inventory) {
            return;
        }

        $this->increment('stock', $quantity);
    }
}
