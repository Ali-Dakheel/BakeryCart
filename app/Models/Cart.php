<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Cart extends Model
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $fillable = [
        'user_id',
        'session_id',
        'expires_at',
    ];

    /** @return \DateTime */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<CartItem> */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

}
