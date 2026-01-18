<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

final class Address extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<string, mixed> */
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'building_number',
        'floor',
        'apartment',
        'area',
        'block',
        'city',
        'postal_code',
        'country',
        'delivery_instructions',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /** @return BelongsTo<User> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Order> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->building_number,
            $this->address_line_1,
            $this->address_line_2,
            $this->floor ? "Floor {$this->floor}" : null,
            $this->apartment ? "Apt {$this->apartment}" : null,
            $this->area,
            $this->block ? "Block {$this->block}" : null,
            $this->city,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function setAsDefault(): void
    {
        DB::transaction(function () {
            // Unset all other default addresses for this user
            static::where('user_id', $this->user_id)
                ->where('id', '!=', $this->id)
                ->update(['is_default' => false]);

            // Set this as default
            $this->update(['is_default' => true]);
        });
    }
}
