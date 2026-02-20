<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

final class Review extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
        'admin_response',
        'responded_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
            'helpful_count' => 'integer',
            'responded_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        self::saving(function (self $review): void {
            if ($review->rating < 1 || $review->rating > 5) {
                throw new InvalidArgumentException('Rating must be between 1 and 5');
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class)
            ->orderBy('sort_order');
    }

    public function getHasAdminResponseAttribute(): bool
    {
        return ! is_null($this->admin_response);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeByRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeHelpful(Builder $query): Builder
    {
        return $query->orderBy('helpful_count', 'desc');
    }
}
