<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'parent_id',
        'slug',
        'image_url',
        'icon',
        'description',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];


    /** @return BelongsTo<Category> */
    public function parent() : belongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    /** "@return HasMany<Category> */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /** @return HasMany<CategoryTranslation> */
    public function translations() : HasMany
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }
    /**
     * Get translation for specific locale
     */
    public function translate(string $locale = 'en'): ?CategoryTranslation
    {
        return $this->translations()
            ->where('locale', $locale)
            ->first();
    }

    /**
     * Get translated name for current locale
     */
    public function getNameAttribute(): ?string
    {
        $locale = app()->getLocale(); // Gets current app locale
        return $this->translate($locale)?->name;
    }

    /**
     * Scope: Get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get only top-level categories (no parent)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
