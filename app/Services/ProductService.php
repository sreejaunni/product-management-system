<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\InsufficientStockException;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters, int $perPage = 10)
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(int $id)
    {
        $product = $this->repository->find($id);
        if (!$product) {
            throw new ProductNotFoundException("Product with ID {$id} not found.");
        }
        return $product;
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw new ProductNotFoundException("Product not found");
        }
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        $product = $this->repository->find($id);


        if (!$product) {
            throw new ProductNotFoundException("Product not found");
        }
        return $this->repository->delete($id);
    }

    /**
     * Decreases stock based on the order quantity.
     *
     * @param int $productId
     * @param int $quantity
     * @throws Exception
     * @return bool
     */
    public function decreaseStock(int $productId, int $quantity)
    {
        // Start a database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Find the product
            $product = $this->repository->find($productId);

            // Check if the product exists
            if (!$product) {
                throw new ProductNotFoundException("Product not found");
            }

            // Check if there is enough stock
            if ($product->stock_quantity < $quantity) {
                throw new InsufficientStockException("Not enough stock available");
            }

            // Decrease the stock
            $product->stock_quantity -= $quantity;

            // Save the updated product
            $saved = $product->save();

            // If save was successful, commit the transaction
            if ($saved) {
                DB::commit();
                return true;
            }

            // If save failed, rollback the transaction
            DB::rollBack();
            return false;

        } catch (Exception $e) {
            // Rollback the transaction on any failure
            DB::rollBack();
            throw $e; // Re-throw the exception to be handled by the caller
        }
    }

}
