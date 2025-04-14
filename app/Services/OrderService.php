<?php

namespace App\Services;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ProductService;

class OrderService
{

    protected $orderRepository;
    protected $productService;

    public function __construct(OrderRepositoryInterface $orderRepository,ProductService $productService)
    {
        $this->orderRepository = $orderRepository;
        $this->productService = $productService;

    }

    /**
     * Create a new order.
     *
     * @param array $data
     * @return \App\Models\Order
     * @throws \Exception
     */
    public function createOrder(array $data): Order
    {
        if(!empty($data['order_items'])){
            foreach ($data['order_items'] as $item) {
                // Decrease stock for each product in the order
                $this->productService->decreaseStock($item['product_id'], $item['quantity']);
            }
        }

        // Use the repository to create the order and its order items
        return $this->orderRepository->createOrder($data);
    }

    /**
     * Get a list of orders for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByUserId(int $userId): Collection
    {
        // Use the repository to fetch orders by user
        return $this->orderRepository->getOrdersByUserId($userId);
    }

    /**
     * Get a single order by its ID.
     *
     * @param int $orderId
     * @return \App\Models\Order|null
     */
    public function getOrderById(int $orderId): ?Order
    {
        // Use the repository to fetch a single order by its ID
        return $this->orderRepository->getOrderById($orderId);
    }
}
