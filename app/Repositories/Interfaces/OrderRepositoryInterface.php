<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;

interface OrderRepositoryInterface
{
    /**
     * Create a new order.
     *
     * @param array $data
     * @return \App\Models\Order
     */
    public function createOrder(array $data);

    /**
     * Get orders by user ID.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByUserId($userId);

    /**
     * Get a single order by its ID.
     *
     * @param int $orderId
     * @return \App\Models\Order|null
     */
    public function getOrderById($orderId);
}
