<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Payment extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'order_id',
        'user_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'gateway',
        'transaction_id',
        'gateway_response',
        'gateway_data',
        'is_mock',
        'paid_at',
        'failed_at',
        'refunded_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'gateway_data' => 'array',
            'is_mock' => 'boolean',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }
}
