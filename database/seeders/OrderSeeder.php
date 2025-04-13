<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Assuming you already have some users
        $users = User::all(); // Fetch all users, you can choose to seed users as well if needed

        foreach ($users as $user) {
            // Create an order for each user
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => rand(100, 1000), // Random price between 100 and 1000
                'status' => 'pending', // Order is initially pending
                'shipping_address' => '123 Some St, Some City, Some Country', // Example shipping address
                'order_date' => now(),
            ]);

            // Create random order items for the order
            $this->createOrderItems($order);
        }
    }

    // Helper method to create order items
    public function createOrderItems($order)
    {
        $products = \App\Models\Product::inRandomOrder()->take(rand(1, 5))->get(); // Get random products for order items

        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => rand(1, 3), // Random quantity between 1 and 3
                'price' => $product->price,
                'total' => $product->price * rand(1, 3), // Price * Quantity
            ]);
        }
    }
}
