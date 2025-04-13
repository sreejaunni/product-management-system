<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the product creation API.
     *
     * @return void
     */
    public function test_create_product()
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin']);

// Assign the 'admin' role to the user
        $user->roles()->attach($role);

        $token = $user->createToken('Test Token')->plainTextToken;

// Prepare mock data
        $category1 = Category::create(['name' => 'Category 1', 'slug' => 'category-1']);
        $category2 = Category::create(['name' => 'Category 2', 'slug' => 'category-2']);

// Prepare the data for the product creation
        $data = [
            'name' => 'Test Product',
            'description' => 'Test product description.',
            'price' => 100,
            'stock_quantity' => 12,
            'category_ids' => [$category1->id, $category2->id],
            'images' => [
                UploadedFile::fake()->image('product.jpg'), // this simulates a valid image
            ],
            'slug' => 'test-product-slug',
        ];

// Make a POST request to the product creation endpoint
        $response = $this->postJson('/api/products', $data, [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);

// Assert the response status is 201 (Created)
        $response->assertStatus(201);

// Assert the product was created
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test product description.',
            'price' => 100,
            'stock_quantity' => 12,
            'slug' => 'test-product-slug',
        ]);

// Assert product has associated categories
        $product = Product::where('name', 'Test Product')->first();
        $this->assertTrue($product->categories->contains($category1));
        $this->assertTrue($product->categories->contains($category2));
    }

    public function test_create_product_unauthorized()
    {
        $user= User::factory()->create();
        $userRole = Role::firstOrCreate(['name' => 'customer']);
        $user->roles()->attach($userRole);
        $userToken = $user->createToken('CustomerToken')->plainTextToken;
// Prepare mock data
        $category1 = Category::create(['name' => 'Category 1', 'slug' => 'category-1']);
        $category2 = Category::create(['name' => 'Category 2', 'slug' => 'category-2']);

// Prepare the data for the product creation
        $data = [
            'name' => 'Test Product',
            'description' => 'Test product description.',
            'price' => 100,
            'stock_quantity' => 12,
            'category_ids' => [$category1->id, $category2->id],
            'images' => [
                UploadedFile::fake()->image('product.jpg'), // this simulates a valid image
            ],
            'slug' => 'test-product-slug',
        ];

// Make a POST request to the product creation endpoint
        $response = $this->postJson('/api/products', $data, [
            'Authorization' => 'Bearer ' . $userToken,
            'Accept' => 'application/json',
        ]);

// Assert the response status is 201 (Created)
        $response->assertStatus(403);
    }

    public function test_update_product()
    {
        // Setup: create user and assign admin role
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->roles()->attach($role);

        $token = $user->createToken('Test Token')->plainTextToken;

        // Create category and product
        $category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        $product = Product::create([
            'name' => 'Old Product Name',
            'description' => 'Old Description',
            'price' => 50,
            'stock_quantity' => 5,
            'slug' => 'old-product',
        ]);

        // Attach the product to the category
        $product->categories()->attach($category->id);

        // Prepare updated data
        $updateData = [
            'name' => 'Updated Product Name',
            'description' => 'Updated description.',
            'price' => 120,
            'stock_quantity' => 8,
            'slug' => 'updated-product-slug',
            'category_ids' => [$category->id],
            'images' => [
                UploadedFile::fake()->image('new-image.jpg'),
            ],
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData, [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);

        // Confirm the updated values in database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'description' => 'Updated description.',
            'price' => 120,
            'stock_quantity' => 8,
            'slug' => 'updated-product-slug',
        ]);

        // Confirm categories still associated
        $product->refresh();
        $this->assertTrue($product->categories->contains($category));
    }


    public function test_list_products_with_filters()
    {
        $this->refreshDatabase();
        // Setup: create categories and products
        $category1 = Category::create(['name' => 'Toys', 'slug' => 'toys']);
        $category2 = Category::create(['name' => 'Books', 'slug' => 'books']);

        $product1 = Product::factory()->create(['name' => 'Laptop']);
        $product2 = Product::factory()->create(['name' => 'Book']);

        $product1->categories()->sync([$category1->id]);
        $product2->categories()->sync([$category2->id]);

        // Test filtering by category_ids
        $response = $this->getJson('/api/products?category_ids[]=' . $category1->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Laptop']);
        $response->assertJsonMissing(['name' => 'Book']);

        // Test filtering by category_name
        $response = $this->getJson('/api/products?category_name=Books');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Book']);
        $response->assertJsonMissing(['name' => 'Laptop']);
    }

    public function test_show_product_by_id()
    {
        // Create category and product
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        $product = Product::create([
            'name' => 'Sample Product',
            'description' => 'This is a sample product',
            'price' => 199.99,
            'stock_quantity' => 50,
            'slug' => 'sample-product',
        ]);
        $product->categories()->sync([$category->id]);

        // VALID product test
        $response = $this->getJson('/api/products/' . $product->id, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => 'Sample Product',
            'description' => 'This is a sample product',
        ]);

        // INVALID product test (non-existent ID)
        $invalidResponse = $this->getJson('/api/products/999999', [
            'Accept' => 'application/json',
        ]);

        $invalidResponse->assertStatus(404);
        $invalidResponse->assertJson([
            'message' => 'Product not found',
        ]);
    }

    public function test_delete_product()
    {
        $this->refreshDatabase();
        // Create admin user
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $adminToken = $admin->createToken('AdminToken')->plainTextToken;

        // Create product
        $product = Product::create([
            'name' => 'Deletable Product',
            'description' => 'This product can be deleted',
            'price' => 100,
            'stock_quantity' => 10,
            'slug' => 'deletable-product',
        ]);

// ✅ Then delete with admin user
        $response = $this->deleteJson('/api/products/' . $product->id, [], [
            'Authorization' => 'Bearer ' . $adminToken,
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Product deleted successfully']);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }


    public function test_delete_product_unauthorized()
    {
        $this->refreshDatabase();
        // Create admin user
        $user= User::factory()->create();
        $userRole = Role::firstOrCreate(['name' => 'customer']);
        $user->roles()->attach($userRole);
        $userToken = $user->createToken('CustomerToken')->plainTextToken;


        // Create product
        $product = Product::create([
            'name' => 'Deletable Product-1',
            'description' => 'This product can be deleted',
            'price' => 100,
            'stock_quantity' => 10,
            'slug' => 'deletable-product-1',
        ]);

// ✅ Then delete with admin user
        $response = $this->deleteJson('/api/products/' . $product->id, [], [
            'Authorization' => 'Bearer ' . $userToken,
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(403);
    }


    /**
     * Clean up fake storage after the test.
     */
    public function tearDown(): void
    {
        Storage::disk('public')->deleteDirectory('product_images');  // Clean up fake storage
        parent::tearDown();
    }

    /**
     * Set up test API token (for example purpose)
     */
    public function setUp(): void
    {
        parent::setUp();
    }
}
