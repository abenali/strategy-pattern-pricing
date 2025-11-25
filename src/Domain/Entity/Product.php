<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class Product
{
    private string $id;

    public function __construct(
        private string $name,
        private float $price,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();

        if ($price < 0) {
            throw new \InvalidArgumentException('Product price cannot be negative');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
