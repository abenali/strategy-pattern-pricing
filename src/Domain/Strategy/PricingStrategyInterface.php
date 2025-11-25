<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

interface PricingStrategyInterface
{
    /**
     * Calculate the final price after applying the strategy.
     *
     * @param float $amount The amount to calculate the strategy on
     *
     * @return float The amount after applying the strategy
     */
    public function calculate(float $amount): float;

    /**
     * Get the name of the strategy for display purposes.
     */
    public function getName(): string;

    /**
     * Get the discount percentage (0-100) for reporting.
     * Returns 0 if the strategy doesn't apply a percentage discount.
     */
    public function getDiscountPercentage(): int;
}
