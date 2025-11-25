<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class Order
{
    private string $id;

    /** @var OrderItem[] */
    private array $items = [];

    public function __construct(
        private Customer $customer,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getSubtotal(): float
    {
        $subtotal = 0.0;

        foreach ($this->items as $item) {
            $subtotal += $item->getTotal();
        }

        return $subtotal;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
