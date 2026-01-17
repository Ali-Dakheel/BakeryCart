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
    /** @return array<string, string> */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];


    /** @return BelongsTo<Category> */
    public function parent(): belongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** "@return HasMany<Category> */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /** @return HasMany<CategoryTranslation> */
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    /** @return HasMany<Product> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /** @param string $locale
     * @return Model<CategoryTranslation|null>
     */
    public function translate(string $locale = 'en'): ?CategoryTranslation
    {
        return $this->translations()->where('locale', $locale)->first();
    }

    /** @return string|null */
    public function getNameAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $this->translate($locale)?->name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

}
