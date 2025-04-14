<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class UpdateProductRequestTest extends TestCase
{
    protected function getValidationRules(Request $request): array
    {
        return (new UpdateProductRequest())->rules();
    }

    /** @test */
    public function it_allows_partial_valid_data()
    {
        // Create a valid category for the test
        $category = Category::factory()->create();

        // Test data that should pass validation
        $data = [
            'price' => 199.99,
            'category_ids' => [$category->id],
            'is_active' => true
        ];

        // Create a mock request with route parameters
        $request = new Request();
        $request->setRouteResolver(function () {
            return Mockery::mock('Illuminate\Routing\Route')
                ->shouldReceive('parameter')
                ->with('id')
                ->andReturn(1)  // Return a valid product ID
                ->getMock();
        });

        // Validate the data against the rules
        $validator = Validator::make($data, $this->getValidationRules($request));

        // Assert the validation passes
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_with_invalid_category_id()
    {
        // Test data with an invalid category ID
        $data = [
            'category_ids' => [999999], // Invalid ID
        ];

        // Create a mock request with route parameters
        $request = new Request();
        $request->setRouteResolver(function () {
            return Mockery::mock('Illuminate\Routing\Route')
                ->shouldReceive('parameter')
                ->with('id')
                ->andReturn(1)  // Return a valid product ID
                ->getMock();
        });

        // Validate the data against the rules
        $validator = Validator::make($data, $this->getValidationRules($request));

        // Assert the validation fails
        $this->assertFalse($validator->passes());

        // Assert the error is related to the invalid category
        $this->assertArrayHasKey('category_ids.0', $validator->errors()->toArray());
    }

    /** @test */
    public function it_accepts_nullable_fields()
    {
        // Test data with nullable fields
        $data = [
            'name' => null,
            'slug' => null,
            'description' => null,
            'price' => null,
            'stock_quantity' => null,
            'is_active' => null,
            'images' => null,
        ];

        // Create a mock request with route parameters
        $request = new Request();
        $request->setRouteResolver(function () {
            return Mockery::mock('Illuminate\Routing\Route')
                ->shouldReceive('parameter')
                ->with('id')
                ->andReturn(1)  // Return a valid product ID
                ->getMock();
        });

        // Validate the data against the rules
        $validator = Validator::make($data, $this->getValidationRules($request));

        // Assert the validation passes
        $this->assertTrue($validator->passes());
    }

    /** @test */
}
