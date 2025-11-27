<?php

declare(strict_types=1);

namespace App\Application\UseCase\CalculateOrderPrice;

final class CalculateOrderPriceResponse
{
    /**
     * @param array<int, array{name: string, discount: int, amountAfter: float}> $appliedStrategies
     */
    public function __construct(
        public readonly float $subtotal,
        public readonly float $total,
        public readonly array $appliedStrategies,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'appliedStrategies' => $this->appliedStrategies,
        ];
    }
}
