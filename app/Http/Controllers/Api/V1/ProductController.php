<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

final class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $products = QueryBuilder::for(Product::class)
            ->whereNull('deleted_at')
            ->with('translations')
            ->allowedFilters([
                AllowedFilter::exact('category_id'),
                AllowedFilter::callback('is_featured', function ($query, $value) {
                    $query->where('is_featured', filter_var($value, FILTER_VALIDATE_BOOLEAN));
                }),
                AllowedFilter::callback('is_available', function ($query, $value) {
                    $query->where('is_available', filter_var($value, FILTER_VALIDATE_BOOLEAN));
                }),
                AllowedFilter::exact('sku'),
                AllowedFilter::operator('price', FilterOperator::DYNAMIC),
                AllowedFilter::callback('min_price', function ($query, $value) {
                    $query->where('price', '>=', $value);
                }),
                AllowedFilter::callback('max_price', function ($query, $value) {
                    $query->where('price', '<=', $value);
                }),
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
                '-price',
                'created_at',
                '-created_at',
                'sales_count',
                '-sales_count',
                'views_count',
                '-views_count',
                'is_featured',
                '-is_featured',
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
            ->appends(request()->query());

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

    public function featured(): AnonymousResourceCollection
    {
        $limit = (int) request()->input('limit', 10);
        $products = $this->productService->getFeatured($limit);

        return ProductResource::collection($products);
    }

    public function popular(): AnonymousResourceCollection
    {
        $limit = (int) request()->input('limit', 10);
        $products = $this->productService->getPopular($limit);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);
        $product = Product::create($validated);
        $product->translations()->createMany($translations);
        $product->load(['translations', 'category']);

        return $this->created(['product' => new ProductResource($product)], 'Product created successfully');
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();
        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);
        $product->update($validated);
        if ($translations !== null) {
            foreach ($translations as $translation) {
                $product->translations()->updateOrCreate(
                    ['locale' => $translation['locale']],
                    $translation
                );
            }
        }
        $product->load(['translations', 'images', 'variants', 'category']);

        return $this->success(['product' => new ProductResource($product)], 'Product updated successfully');
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->success(null, 'Product deleted successfully', 204);
    }
}
