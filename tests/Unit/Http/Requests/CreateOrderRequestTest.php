<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\CreateOrderRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class CreateOrderRequestTest extends TestCase
{
    /**
     * Test if the user is authorized to make the request.
     *
     * @return void
     */
    public function testAuthorize()
    {
        // Authenticated
        Auth::shouldReceive('check')->once()->andReturn(true);
        $request = new CreateOrderRequest();
        $this->assertTrue($request->authorize());

        // Rebinding with unauthenticated behavior
        Auth::shouldReceive('check')->once()->andReturn(false);
        $request = new CreateOrderRequest();
        $this->assertFalse($request->authorize());
    }

    /**
     * Test the validation rules for creating an order.
     *
     * @return void
     */
    public function test_validation_rules_pass_with_valid_data()
    {
        // Ensure that valid users and products exist in the database
        $user = User::factory()->create(); // Create a fake user
        $product1 = Product::factory()->create(); // Create a fake product with ID 1
        $product2 = Product::factory()->create(); // Create a fake product with ID 2

        // Prepare the valid data as provided
        $validData = [
            'user_id' => $user->id, // The valid user ID from the created user
            'total_price' => 150.75, // The valid total price
            'status' => 'pending', // Optional status
            'shipping_address' => 'test address', // Optional shipping address
            'order_items' => [
                [
                    'product_id' => $product1->id, // The valid product ID from the created product
                    'quantity' => 2, // Valid quantity (integer)
                    'price' => 50.25, // Valid price (numeric)
                ],
                [
                    'product_id' => $product2->id, // The valid product ID from the created product
                    'quantity' => 1, // Valid quantity (integer)
                    'price' => 50.25, // Valid price (numeric)
                ]
            ],
        ];

        // Run the validation
        $request = new CreateOrderRequest();
        $validator = Validator::make($validData, $request->rules());

        // Assert that the validation passes
        $this->assertTrue($validator->passes(), 'Validation failed for correct data');
    }





    public function test_validation_rules_fail_on_missing_required_field()
    {
        $request = new CreateOrderRequest();
        $invalidData = [
            'product_id' => 5,
            'quantity' => 2,
            'total_price' => 100.50,
            // Missing user_id
        ];

        $validator = Validator::make($invalidData, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
    }



}
