<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Requests\CreateOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Order;

class OrderController extends Controller
{
    protected $orderService;

    /**
     * OrderController constructor.
     *
     * @param \App\Services\OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order.
     *
     * @param \App\Http\Requests\CreateOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateOrderRequest $request)
    {
        // The validated data is automatically handled by the form request
        $data = $request->validated();

        try {
            // Create the order using the OrderService
            $order = $this->orderService->createOrder($data);
            return response()->json(['message' => 'Order created successfully','order' => $order], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            // Catch errors and return an appropriate response
            return response()->json(['message' => 'Error creating order','error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Get all orders for a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get orders using the OrderService
            $orders = $this->orderService->getOrdersByUserId($request->user()->id);
            return response()->json(['orders' => $orders], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([ 'message' => 'Error fetching orders','error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a single order by its ID.
     *
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($orderId)
    {
            // Get the order using the OrderService
            $order = $this->orderService->getOrderById($orderId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['order' => $order], Response::HTTP_OK);
    }


}
