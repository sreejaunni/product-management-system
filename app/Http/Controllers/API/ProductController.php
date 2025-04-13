<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a paginated list of products, optionally filtered by category.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['category_ids','category_name']);
        $perPage = $request->get('per_page', 10);

        $products = $this->productService->list($filters, $perPage);

        //can ue product resource later
        return response()->json($products);
    }

    /**
     * Display the specified product by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($product);
    }

    /**
     * Store a newly created product.
     *
     * @param  CreateProductRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateProductRequest $request)
    {
        // Ensure only admin can create products
        if (!Auth::user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized User: Only admin can create Product'], Response::HTTP_FORBIDDEN);
        }

        $data = $request->validated();
        $data['description'] = strip_tags($data['description']);

        // Trim any excess spaces from name or slug
        $data['name'] = trim($data['name']);
        $data['slug'] = trim($data['slug']);

        $product = $this->productService->create($data);

        if ($request->hasFile('images')) {
            echo "here";
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return response()->json($product, 201);
    }

    /**
     * Update the specified product.
     *
     * @param  UpdateProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, $id)
    {

        // Ensure only admin can update products
        if (!Auth::user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized User: Only admin can update Product'], Response::HTTP_FORBIDDEN);
        }

        $data = $request->validated();
        $product = $this->productService->update($id, $data);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        if ($request->has('images')) {
            // First, delete old images if they exist
            foreach ($product->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();  // Delete the record from the database
            }

            // Upload new images
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');  // Store image in the 'products' folder in 'public'

                // Save the new image to the database
                $product->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return response()->json($product);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Ensure only admin can delete products
        if (!Auth::user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized User: Only admin can delete Product'], 403);
        }

        $deleted = $this->productService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
