<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(), // or use an existing product id
            'path' => 'images/products/' . $this->faker->image('public/storage/images/products', 640, 480, null, false),
        ];
    }
}
