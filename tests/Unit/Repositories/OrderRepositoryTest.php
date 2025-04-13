<?php

namespace Tests\Unit\Repositories;

use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating an order.
     *
     * @return void
     */
    public function it_creates_an_order_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a product (this is necessary to avoid the foreign key constraint error)
        $product = Product::factory()->create();

        // Define the order data
        $orderData = [
            'user_id' => $user->id,
            'total_price' => 200.00,
            'status' => 'pending',
            'shipping_address' => '123 Main St',
            'order_items' => [
                [
                    'product_id' => $product->id,  // Use the created product ID
                    'quantity' => 1,
                    'price' => 100.00,
                ],
            ]
        ];

        // Create the order using the repository
        $orderRepository = new OrderRepository();
        $order = $orderRepository->createOrder($orderData);

        // Assert that the order is saved in the database
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 200.00
        ]);
    }


    /**
     * Test fetching orders by user ID.
     *
     * @return void
     */
    public function testGetOrdersByUserId()
    {
        $user = User::factory()->create();
        Order::factory()->count(2)->create(['user_id' => $user->id]);

        $orderRepository = new OrderRepository();
        $orders = $orderRepository->getOrdersByUserId($user->id);

        // Assert that the user has 2 orders
        $this->assertCount(2, $orders);
    }

    /**
     * Test fetching a single order by ID.
     *
     * @return void
     */
    public function testGetOrderById()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $orderRepository = new OrderRepository();
        $orderFound = $orderRepository->getOrderById($order->id);

        // Assert that the order found matches the one in the database
        $this->assertEquals($order->id, $orderFound->id);
    }
}
