<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Exceptions\ProductNotFoundException;

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
}
