<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final readonly class ProductService
{
    public function getAvailableQuery(): Builder
    {
        return Product::query()
            ->where('is_available', true)
            ->where(function (Builder $query) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('available_until')
                    ->orWhere('available_until', '>=', now());
            });
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return $this->getAvailableQuery()
            ->where('is_featured', true)
            ->with(['translations', 'images', 'category'])
            ->orderBy('sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function search(string $term, string $locale = 'en'): Builder
    {
        return Product::query()
            ->whereHas('translations', function (Builder $query) use ($term, $locale) {
                $query->where('locale', $locale)
                    ->where(function (Builder $q) use ($term) {
                        $q->where('name', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%");
                    });
            })
            ->orWhere('sku', 'like', "%{$term}%");
    }

    public function filterByPriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function getByCategory(int $categoryId, bool $includeChildren = false): Builder
    {
        $query = $this->getAvailableQuery();

        if ($includeChildren) {
            return $query->where(function (Builder $q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', function (Builder $categoryQuery) use ($categoryId) {
                        $categoryQuery->where('parent_id', $categoryId);
                    });
            });
        }

        return $query->where('category_id', $categoryId);
    }

    public function getLowStock(): Collection
    {
        return Product::query()
            ->where('track_inventory', true)
            ->whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->where('current_stock', '>', 0)
            ->with(['translations'])
            ->get();
    }

    public function getOutOfStock(): Collection
    {
        return Product::query()
            ->where('track_inventory', true)
            ->where('current_stock', '<=', 0)
            ->with(['translations'])
            ->get();
    }

    public function incrementViews(Product $product): void
    {
        $product->increment('views_count');
    }

    public function incrementSales(Product $product, int $quantity = 1): void
    {
        $product->increment('sales_count', $quantity);
    }

    public function decrementStock(Product $product, int $quantity): bool
    {
        if (!$product->track_inventory) {
            return true;
        }

        if ($product->current_stock < $quantity) {
            return false;
        }

        $product->decrement('current_stock', $quantity);
        return true;
    }

    public function incrementStock(Product $product, int $quantity): void
    {
        if ($product->track_inventory) {
            $product->increment('current_stock', $quantity);
        }
    }

    public function isAvailableForPurchase(Product $product, int $quantity = 1): bool
    {
        if (!$product->is_available) {
            return false;
        }

        // Check scheduling
        if ($product->available_from && $product->available_from->isFuture()) {
            return false;
        }

        if ($product->available_until && $product->available_until->isPast()) {
            return false;
        }

        // Check stock
        if ($product->track_inventory && $product->current_stock < $quantity) {
            return false;
        }

        return true;
    }

    public function getTranslation(Product $product, string $locale): ?ProductTranslation
    {
        return $product->translations()
            ->where('locale', $locale)
            ->first();
    }

    public function getPopular(int $limit = 10): Collection
    {
        return $this->getAvailableQuery()
            ->orderBy('sales_count', 'desc')
            ->with(['translations', 'images', 'category'])
            ->limit($limit)
            ->get();
    }

    public function getRecent(int $limit = 10): Collection
    {
        return $this->getAvailableQuery()
            ->orderBy('created_at', 'desc')
            ->with(['translations', 'images', 'category'])
            ->limit($limit)
            ->get();
    }
}
