<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        Product::factory()
            ->count(50)
            ->create()
            ->each(function ($product) use ($categories) {
                $randomCategoryIds = $categories->random(2)->pluck('id')->unique();
                $product->categories()->syncWithoutDetaching($randomCategoryIds);
            });
    }
}
