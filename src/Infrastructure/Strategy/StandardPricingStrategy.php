<?php

declare(strict_types=1);

namespace App\Infrastructure\Strategy;

use App\Domain\Strategy\PricingStrategyInterface;

final class StandardPricingStrategy implements PricingStrategyInterface
{
    public function calculate(float $amount): float
    {
        return $amount;
    }

    public function getName(): string
    {
        return 'Standard';
    }

    public function getDiscountPercentage(): int
    {
        return 0;
    }
}