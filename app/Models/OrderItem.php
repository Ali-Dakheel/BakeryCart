<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrderItem extends Model
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_description',
        'variant_name',
        'variant_attributes',
        'image_url',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'total',
        'special_instructions',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'variant_attributes' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'decimal:3',
            'discount_amount' => 'decimal:3',
            'tax_amount' => 'decimal:3',
            'total' => 'decimal:3',
        ];
    }

    /** @return BelongsTo<Order> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo<Product> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<ProductVariant> */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getSubtotalAttribute(): float
    {
        return (float)($this->unit_price * $this->quantity);
    }
}
