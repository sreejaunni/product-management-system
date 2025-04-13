<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{

    const CACHE_TIME = 10;
    public function getAll(array $filters = [], int $perPage = 10)
    {
        $cacheKey = $this->buildCacheKey($filters, $perPage, request()->get('page', 1));

        // Cache for 10 minutes (600 seconds)
        return Cache::remember($cacheKey, self::CACHE_TIME* 60, function () use ($filters, $perPage) {

            $query = Product::with(['categories']);

            if (!empty($filters['category_ids'])) {
                $query->whereHas('categories', function ($q) use ($filters) {
                    $q->whereIn('categories.id', $filters['category_ids']);
                });
            }

            // Filter by category name
            if (!empty($filters['category_name'])) {
                $query->whereHas('categories', function ($q) use ($filters) {
                    $q->where('categories.name', 'like', '%' . $filters['category_name'] . '%');
                });
            }

            return $query->paginate($perPage);
        });
    }

    public function find(int $id)
    {
        return Product::with('categories')->find($id);
    }

    public function create(array $data)
    {
        $product = Product::create($data);
        $product->categories()->syncWithoutDetaching($data['category_ids']);
        return $product;
    }

    public function update(int $id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        if (!empty($data['category_ids'])) {
            $product->categories()->syncWithoutDetaching($data['category_ids']); // Avoid duplicating the same pair
        }
        return $product;
    }

    public function delete(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }

    /**
     * Build a unique cache key based on filters, perPage, and page
     */
    private function buildCacheKey(array $filters, int $perPage, int $page)
    {
        $filtersKey = md5(json_encode($filters));
        return "products:filters:$filtersKey:perPage:$perPage:page:$page";
    }
}
