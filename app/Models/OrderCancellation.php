<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrderCancellation extends Model
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $fillable = [
        'order_id',
        'cancelled_by',
        'cancellation_reason',
        'refund_amount',
        'refund_status',
        'refund_method',
        'refund_transaction_id',
        'cancelled_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:3',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Order> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
