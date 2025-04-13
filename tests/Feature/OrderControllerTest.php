<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    public function test_user_can_create_order()
    {
        $user = $this->authenticate();
        $product = \App\Models\Product::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'total_price' => 199.99,
            'order_items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 99.99,
                ]
            ],
            'shipping_address' => '123 Test St, Test City',
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Order created successfully',
            ]);
    }


    public function test_user_order_history()
    {
        $user = $this->authenticate();

        // Assuming Order factory exists
        Order::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'orders',
            ]);
    }

    public function test_user_can_view_single_order()
    {
        $user = $this->authenticate();

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['order']);
    }

    public function test_user_gets_404_for_invalid_order()
    {
        $this->authenticate();

        $response = $this->getJson("/api/orders/999");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Order not found']);
    }
}
