<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Create a new order.
     *
     * @param array $data
     * @return \App\Models\Order
     */
    public function createOrder(array $data)
    {
        // Start a database transaction to ensure the order and its items are created atomically
        \DB::beginTransaction();

        try {
            // Create the order
            $order = Order::create([
                'user_id' => $data['user_id'],
                'total_price' => $data['total_price'],
                'status' => $data['status'] ?? 'pending',
                'shipping_address'=>$data['shipping_address'] ?? '',
            ]);

            // Create the order items
            foreach ($data['order_items'] as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' =>$item['price'] * $item['quantity'],
                ]);
            }

            // Commit the transaction
            \DB::commit();

            return $order;
        } catch (\Exception $e) {
            // Rollback the transaction if anything goes wrong
            \DB::rollBack();
            throw $e; // Re-throw the exception
        }
    }

    /**
     * Get orders by user ID.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByUserId($userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with('orderItems')  // Eager load the associated order items
            ->get();
    }

    /**
     * Get a single order by its ID.
     *
     * @param int $orderId
     * @return \App\Models\Order|null
     */
    public function getOrderById($orderId): ?Order
    {
        return Order::with('orderItems')  // Eager load the associated order items
        ->find($orderId);  // Find by ID and return the result or null
    }
}
