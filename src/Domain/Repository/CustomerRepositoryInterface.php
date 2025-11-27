<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Customer;

interface CustomerRepositoryInterface
{
    /**
     * Find a customer by ID.
     *
     * @throws \RuntimeException if customer not found
     */
    public function findById(string $id): Customer;
}
