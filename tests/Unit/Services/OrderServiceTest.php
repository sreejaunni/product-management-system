<?php

namespace Tests\Unit\Services;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use Tests\TestCase;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class OrderServiceTest extends TestCase
{
    protected $orderRepositoryMock;
    protected $orderService;

    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the ProductRepositoryInterface
        $this->productRepositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productService = new ProductService($this->productRepositoryMock);
        // Mock the OrderRepositoryInterface
        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $this->orderService = new OrderService($this->orderRepositoryMock, $this->productService);

    }

    /**
     * Test the createOrder method
     *
     * @return void
     */
    public function testCreateOrder()
    {
        // Arrange: Define the data that will be passed to createOrder
        $orderData = [
            'user_id' => 1,
            'total' => 100,
            'status' => 'pending',
            // other order data as needed
        ];

        // Create a mock order instance
        $orderMock = Mockery::mock(Order::class);
        $this->orderRepositoryMock->shouldReceive('createOrder')
            ->with($orderData)
            ->once()
            ->andReturn($orderMock);

        // Act: Call the createOrder method
        $order = $this->orderService->createOrder($orderData);

        // Assert: Ensure the correct order is returned
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($orderMock, $order);
    }

    /**
     * Test the getOrdersByUserId method
     *
     * @return void
     */
    public function testGetOrdersByUserId()
    {
        // Arrange: Define the user ID and expected result
        $userId = 1;

        // Create an actual Eloquent Collection of mocked Order models
        $orders = new Collection([
            Mockery::mock(Order::class),
            Mockery::mock(Order::class)
        ]);

        // Make the repository return the mocked collection of orders
        $this->orderRepositoryMock->shouldReceive('getOrdersByUserId')
            ->with($userId)
            ->once()
            ->andReturn($orders); // Return a real Eloquent collection

        // Act: Call the getOrdersByUserId method
        $result = $this->orderService->getOrdersByUserId($userId);

        // Assert: Ensure the result is an instance of Eloquent Collection
        $this->assertInstanceOf(Collection::class, $result);  // Verify it's an instance of Eloquent Collection
        $this->assertCount(2, $result); // Check if the collection has 2 orders
    }

    /**
     * Test the getOrderById method
     *
     * @return void
     */
    public function testGetOrderById()
    {
        // Arrange: Define the order ID and expected result
        $orderId = 1;
        $orderMock = Mockery::mock(Order::class);
        $this->orderRepositoryMock->shouldReceive('getOrderById')
            ->with($orderId)
            ->once()
            ->andReturn($orderMock);

        // Act: Call the getOrderById method
        $order = $this->orderService->getOrderById($orderId);

        // Assert: Ensure the correct order is returned
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($orderMock, $order);
    }

    /**
     * Test the case when an order is not found in getOrderById
     *
     * @return void
     */
    public function testGetOrderByIdNotFound()
    {
        // Arrange: Define the order ID
        $orderId = 999;

        // Make the repository return null when the order is not found
        $this->orderRepositoryMock->shouldReceive('getOrderById')
            ->with($orderId)
            ->once()
            ->andReturnNull();

        // Act: Call the getOrderById method
        $order = $this->orderService->getOrderById($orderId);

        // Assert: Ensure the order is null
        $this->assertNull($order);
    }
}
