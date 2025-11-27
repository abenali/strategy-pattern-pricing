<?php

declare(strict_types=1);

namespace App\Application\UseCase\CalculateOrderPrice;

final class CalculateOrderPriceCommand
{
    /**
     * @param array<int, array{productId: string, quantity: int}> $items
     * @param array<int, string>                                  $strategyCodes
     */
    public function __construct(
        public readonly string $customerId,
        public readonly array $items,
        public readonly array $strategyCodes,
    ) {
    }
}
