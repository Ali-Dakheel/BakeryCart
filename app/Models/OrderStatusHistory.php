<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrderStatusHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    /** @var array<string, mixed> */
    protected $fillable = [
        'order_id',
        'status',
        'notes',
        'changed_by',
        'notified_customer',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'notified_customer' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Order> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo<User> */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
