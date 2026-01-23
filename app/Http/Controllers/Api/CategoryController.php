<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = QueryBuilder::for(Category::class)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->whereNull('deleted_at')
            ->with(['translations'])
            ->allowedFilters([
                AllowedFilter::exact('slug'),
                AllowedFilter::partial('translations.name'),
            ])
            ->allowedIncludes([
                'translations',
                'children',
                'products',
                'children.translations',
                'products.translations',
            ])
            ->defaultSort('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category): CategoryResource
    {
        $category->load([
            'translations',
            'children' => function ($query) {
            $query->where('is_active', true)
            ->orderBy('sort_order');
            },
            'children.translations',
            'products.translations',
            'products.images'
        ]);
        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $validated = $request->validated();
        $translation = $validated['translations'] ?? [];
        unset($validated['translations']);
        $category = Category::create($validated);
        $category->translations()->createMany($translation);
        $category->load(['translations', 'parent']);
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $validated = $request->validated();
        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);
        $category->update($validated);
        if ($translations !== null) {
            foreach ($translations as $translation) {
                $category->translations()->updateOrCreate(
                    ['locale' => $translation['locale']],
                    $translation
                );
            }
        }
        $category->load(['translations', 'parent', 'children']);
        return new CategoryResource($category);
    }
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
