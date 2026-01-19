<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProductPriceHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'product_price_history';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'old_price',
        'new_price',
        'changed_by',
        'reason',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:3',
            'new_price' => 'decimal:3',
            'changed_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getPriceChangeAttribute(): float
    {
        return (float) ($this->new_price - $this->old_price);
    }

    public function getPriceChangePercentageAttribute(): float
    {
        if ($this->old_price == 0) {
            return 0;
        }

        return round((($this->new_price - $this->old_price) / $this->old_price) * 100, 2);
    }
}
