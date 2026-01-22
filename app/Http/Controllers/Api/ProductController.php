<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

final class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $products = QueryBuilder::for(Product::class)
            ->where('is_available', true)
            ->whereNull('deleted_at')
            ->allowedFilters([
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('is_featured'),
                AllowedFilter::exact('sku'),
                AllowedFilter::operator('price', FilterOperator::DYNAMIC),
                AllowedFilter::partial('sku_search', 'sku'),
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->whereHas('translations', function ($tq) use ($value) {
                            $tq->where('name', 'like', "%{$value}%")
                                ->orWhere('short_description', 'like', "%{$value}%");
                        });
                    });
                }),
                AllowedFilter::scope('popular'),
            ])
            ->allowedSorts([
                'price',
                'created_at',
                'sales_count',
                'views_count',
                AllowedSort::field('newest', 'created_at'),
                AllowedSort::field('bestselling', 'sales_count'),
            ])->defaultSort('-created_at')
            ->allowedIncludes([
                'translations',
                'images',
                'variants',
                'category',
                'category.translations',
                AllowedInclude::count('reviewsCount'),
            ])
            ->paginate(request()->input('per_page', 15))
            ->appends(request()->query());;
        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        $this->productService->incrementViews($product);
        $product->load([
            'translations',
            'images',
            'variants',
            'category.translations',
        ]);

        return new ProductResource($product);
    }
}
