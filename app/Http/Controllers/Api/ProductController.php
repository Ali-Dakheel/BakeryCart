<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
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
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $products = QueryBuilder::for(Product::class)
            ->where('is_available', true)
            ->whereNull('deleted_at')
            ->with('translations')
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

    public function featured(): AnonymousResourceCollection
    {
        $limit = (int)request()->input('limit', 10);
        $products = $this->productService->getFeatured($limit);

        return ProductResource::collection($products);
    }

    public function popular(): AnonymousResourceCollection
    {
        $limit = (int)request()->input('limit', 10);
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
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();
        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);
        $product->update($validated);
        if ($translations !== null) {
            foreach ($translations as $translation) {
                $product->translations()->updateOrCreate([
                    'locale' => $translation['locale']],
                    $translation);
            }
        }
        $product->load(['translations', 'images', 'variants', 'category']);
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 204);
    }

}
