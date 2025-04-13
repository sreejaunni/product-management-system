<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductImage;
use App\Models\Product;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'products/sample1.jpg',
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'products/sample2.jpg',
            ]);
        }
    }
}
