<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
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

    /** @return HasMany<ProductImage> */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true)->limit(1);
    }

    /** @return HasMany<ProductVariant> */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->orderBy('sort_order');
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_available', true)
            ->orderBy('sort_order');
    }
    /** @return HasMany<Review> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<Review> */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    public function translate(string $locale = 'en'): ?ProductTranslation
    {
        return $this->translations()
            ->where('locale', $locale)
            ->first();
    }
    public function getNameAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $this->translate($locale)?->name;
    }
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
        if (!$this->track_inventory) {
            return true;
        }

        return $this->current_stock > 0;
    }
    public function getIsLowStockAttribute(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }

        return $this->current_stock > 0 && $this->current_stock <= $this->low_stock_threshold;
    }
    public function getAverageRatingAttribute(): float
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where(function ($q) {
                $q->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('available_until')
                    ->orWhere('available_until', '>=', now());
            });
    }

}
