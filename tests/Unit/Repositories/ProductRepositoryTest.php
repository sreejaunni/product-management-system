<?php

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Models\Category;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = new ProductRepository();
    }

    /** @test */
    public function it_can_fetch_all_products_with_filters_and_pagination()
    {
        // Create sample categories and products
        $category = Category::factory()->create();
        $product = Product::factory()->create();

        // Attach the product to the category (use sync to prevent duplicates)
        $product->categories()->sync([$category->id]);

        // Define filters
        $filters = ['category_ids' => [$category->id]];
        $perPage = 10;

        // Call the repository method to get products
        $products = $this->productRepository->getAll($filters, $perPage);

        // Assert the correct number of products are fetched
        $this->assertNotEmpty($products);
        $this->assertTrue($products->contains($product));
    }


    /** @test */
    public function it_can_find_product_by_id()
    {
        $product = Product::factory()->create();

        // Call the find method
        $foundProduct = $this->productRepository->find($product->id);

        // Assert the product was found
        $this->assertEquals($product->id, $foundProduct->id);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        // Create a category
        $category = Category::factory()->create();

        // Prepare data with slug generation
        $data = [
            'name' => 'Product Test',
            'description' => 'Test description',
            'price' => 100.00,
            'category_ids' => [$category->id],
            'slug' => \Str::slug('Product Test'), // Generate slug if not provided
        ];

        // Call the create method
        $product = $this->productRepository->create($data);

        // Assert the product is created
        $this->assertDatabaseHas('products', ['name' => 'Product Test']);
        $this->assertTrue($product->categories->contains($category));
    }

    /** @test */
    public function it_can_update_a_product()
    {
        // Create a product
        $product = Product::factory()->create();
        $category = Category::factory()->create();
        $product->categories()->attach($category);

        // New data for updating
        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 200.00,
            'category_ids' => [$category->id],
            'slug' => 'updated-product'
        ];

        // Call the update method
        $updatedProduct = $this->productRepository->update($product->id, $data);

        // Assert the product is updated
        $this->assertEquals('Updated Product', $updatedProduct->name);
        $this->assertTrue($updatedProduct->categories->contains($category));
    }

    /** @test */

    public function test_product_soft_delete()
    {
        // Create a product
        $product = Product::factory()->create();

        // Soft delete the product
        $isDeleted = $this->productRepository->delete($product->id);

        // Assert that the product is soft deleted (check that deleted_at is not null)
        $this->assertTrue($isDeleted);
        $this->assertNotNull($product->fresh()->deleted_at);  // Check that the deleted_at field is not null

        // Ensure the product is still present in the database but marked as deleted
        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => $product->fresh()->deleted_at]);
    }
}
