<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Order extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<string, mixed> */
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_id',
        'shipping_name',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_building',
        'shipping_floor',
        'shipping_apartment',
        'shipping_area',
        'shipping_city',
        'delivery_instructions',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'coupon_discount',
        'tax_percentage',
        'tax_amount',
        'shipping_fee',
        'total',
        'currency',
        'status',
        'payment_status',
        'fulfillment_status',
        'delivery_date',
        'delivery_time_slot',
        'delivered_at',
        'customer_notes',
        'admin_notes',
        'cancellation_reason',
        'ip_address',
        'user_agent',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:3',
            'discount_amount' => 'decimal:3',
            'coupon_discount' => 'decimal:3',
            'tax_percentage' => 'decimal:2',
            'tax_amount' => 'decimal:3',
            'shipping_fee' => 'decimal:3',
            'total' => 'decimal:3',
            'delivery_date' => 'date',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)
            ->orderBy('created_at', 'desc');
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(OrderCancellation::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getIsCancelableAttribute(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function getIsRefundableAttribute(): bool
    {
        return in_array($this->status, ['delivered', 'cancelled'])
            && $this->payment_status === 'paid';
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'pending');
    }
}
