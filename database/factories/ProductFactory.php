<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            // Attach 1-3 random categories
            $categoryIds = \App\Models\Category::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $product->categories()->attach($categoryIds);
        });
    }
}
