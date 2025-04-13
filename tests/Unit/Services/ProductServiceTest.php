<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var ProductService */
    protected $productService;

    /** @var ProductRepositoryInterface|MockObject */
    protected $productRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the ProductRepositoryInterface
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);

        // Instantiate the ProductService with the mocked repository
        $this->productService = new ProductService($this->productRepositoryMock);
    }

    public function test_getAll_products()
    {
        // Arrange: Prepare mock data
        $filters = ['category_ids' => [1, 2]];
        $perPage = 10;

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('getAll')
            ->with($filters, $perPage)
            ->willReturn(['product1', 'product2']);

        // Act: Call the service method
        $products = $this->productService->list($filters, $perPage);

        // Assert: Check if the result is as expected
        $this->assertEquals(['product1', 'product2'], $products);
    }

    public function test_create_product()
    {
        // Arrange: Prepare mock data
        $data = ['name' => 'New Product', 'price' => 100];

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn(['id' => 1, 'name' => 'New Product', 'price' => 100]);

        // Act: Call the service method
        $product = $this->productService->create($data);

        // Assert: Check if the product is created correctly
        $this->assertEquals(1, $product['id']);
        $this->assertEquals('New Product', $product['name']);
    }

    public function test_update_product()
    {
        // Arrange: Prepare mock data
        $productId = 1;
        $data = ['name' => 'Updated Product', 'price' => 150];

        // Mock the 'find' method to return a product
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn((object) ['id' => 1, 'name' => 'Old Product', 'price' => 100]);

        // Mock the 'update' method
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('update')
            ->with($productId, $data)
            ->willReturn((object) ['id' => 1, 'name' => 'Updated Product', 'price' => 150]);

        // Act: Call the service method
        $updatedProduct = $this->productService->update($productId, $data);

        // Assert: Check if the product is updated correctly
        $this->assertEquals('Updated Product', $updatedProduct->name);
        $this->assertEquals(150, $updatedProduct->price);
    }

    public function test_delete_product()
    {
        // Arrange: Prepare mock data
        $productId = 1;

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn(['id' => 1, 'name' => 'Old Product', 'price' => 100]);

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($productId)
            ->willReturn(true);

        // Act: Call the service method
        $result = $this->productService->delete($productId);

        // Assert: Check if the product is deleted successfully
        $this->assertTrue($result);
    }

    public function test_get_product_by_id_not_found()
    {
        // Arrange: Prepare mock data
        $productId = 999;

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn(null); // Simulate product not found

        // Act & Assert: Call the service method and expect an exception
        $this->expectException(\App\Exceptions\ProductNotFoundException::class);
        $this->productService->getById($productId);
    }
}
