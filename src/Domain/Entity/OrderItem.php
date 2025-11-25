<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class OrderItem
{
    public function __construct(
        private Product $product,
        private int $quantity,
        private float $unitPrice,
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }

        if ($unitPrice < 0) {
            throw new \InvalidArgumentException('Unit price cannot be negative');
        }
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getTotal(): float
    {
        return $this->unitPrice * $this->quantity;
    }
}
