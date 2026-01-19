<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Product extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'category_id',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'compare_at_price',
        'cost',
        'current_stock',
        'low_stock_threshold',
        'track_inventory',
        'daily_production_capacity',
        'lead_time_hours',
        'preparation_time_minutes',
        'available_from_time',
        'available_to_time',
        'weight',
        'attributes',
        'is_available',
        'is_featured',
        'is_taxable',
        'requires_shipping',
        'meta_title',
        'meta_description',
        'og_image_url',
        'available_from',
        'available_until',
        'views_count',
        'sales_count',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'compare_at_price' => 'decimal:3',
            'cost' => 'decimal:3',
            'current_stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'track_inventory' => 'boolean',
            'daily_production_capacity' => 'integer',
            'lead_time_hours' => 'integer',
            'preparation_time_minutes' => 'integer',
            'weight' => 'integer',
            'attributes' => 'array',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'is_taxable' => 'boolean',
            'requires_shipping' => 'boolean',
            'available_from' => 'datetime',
            'available_until' => 'datetime',
            'views_count' => 'integer',
            'sales_count' => 'integer',
        ];
    }
    /** @return BelongsTo<Model> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<ProductTranslation> */
    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    /**@return HasMany<ProductImage> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    /** @return HasMany<ProductVariant> */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->orderBy('sort_order');
    }
    /** @return HasMany<Review> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    /** @return HasMany<CartItem> */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /** @return HasMany<OrderItem> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasMany<Wishlist> */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class)
            ->orderBy('changed_at', 'desc');
    }


    /** Accessors */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->is_on_sale) {
            return null;
        }

        return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100, 2);
    }

    public function getInStockAttribute(): bool
    {
        return !$this->track_inventory || $this->current_stock > 0;
    }
    public function getIsLowStockAttribute(): bool
    {
        return $this->track_inventory
            && $this->current_stock > 0
            && $this->current_stock <= $this->low_stock_threshold;
    }

    public function getNameAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $this->translations()
            ->where('locale', $locale)
            ->first()?->name;
    }
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('track_inventory', false)
                ->orWhere('current_stock', '>', 0);
        });
    }
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }
}
