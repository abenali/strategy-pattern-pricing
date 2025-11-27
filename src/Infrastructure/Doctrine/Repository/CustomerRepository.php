<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Customer;
use App\Domain\Repository\CustomerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
final class CustomerRepository extends ServiceEntityRepository implements CustomerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findById(string $id): Customer
    {
        $customer = $this->find($id);

        if (!$customer instanceof Customer) {
            throw new \RuntimeException(sprintf('Customer with ID "%s" not found', $id));
        }

        return $customer;
    }
}
