<?php

declare(strict_types=1);

namespace App\Models;

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
    /**
     * Get the product that owns this variant
     *
     * @return BelongsTo<Product>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<CartItem> */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /** @return BelongsTo<OrderItem> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if variant is in stock
     *
     * @return bool
     */
    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
