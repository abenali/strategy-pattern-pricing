<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\CustomerType;
use Symfony\Component\Uid\Uuid;

class Customer
{
    private string $id;

    public function __construct(
        private string $email,
        private CustomerType $type,
        private float $totalPurchases = 0.0,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): CustomerType
    {
        return $this->type;
    }

    public function getTotalPurchases(): float
    {
        return $this->totalPurchases;
    }

    public function isVip(): bool
    {
        return CustomerType::VIP === $this->type;
    }

    public function isStudent(): bool
    {
        return CustomerType::STUDENT === $this->type;
    }

    public function isStandard(): bool
    {
        return CustomerType::STANDARD === $this->type;
    }
}
