<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'shipping_address' => $this->faker->address,
            'status' =>$this->faker->randomElement(['pending', 'shipped', 'delivered']),
            'order_date' => Carbon::now(),
        ];
    }
}
