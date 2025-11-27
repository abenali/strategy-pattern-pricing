<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\CustomerType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]
class Customer
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', enumType: CustomerType::class)]
    private CustomerType $type;

    #[ORM\Column(type: 'float')]
    private float $totalPurchases;

    public function __construct(
        string $email,
        CustomerType $type,
        float $totalPurchases = 0.0,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
        $this->email = $email;
        $this->type = $type;
        $this->totalPurchases = $totalPurchases;
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
