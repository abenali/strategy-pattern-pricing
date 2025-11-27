<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * Find a product by ID.
     *
     * @throws \RuntimeException if product not found
     */
    public function findById(string $id): Product;
}
