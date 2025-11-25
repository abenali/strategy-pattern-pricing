<?php

declare(strict_types=1);

namespace App\Infrastructure\Strategy;

use App\Domain\Strategy\PricingStrategyInterface;

final class VipPricingStrategy implements PricingStrategyInterface
{
    private const DISCOUNT_PERCENTAGE = 15;

    public function calculate(float $amount): float
    {
        return $amount * (1 - self::DISCOUNT_PERCENTAGE / 100);
    }

    public function getName(): string
    {
        return 'VIP';
    }

    public function getDiscountPercentage(): int
    {
        return self::DISCOUNT_PERCENTAGE;
    }
}