<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Strategy;

use App\Infrastructure\Strategy\VipPricingStrategy;
use PHPUnit\Framework\TestCase;

final class VipPricingStrategyTest extends TestCase
{
    private VipPricingStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new VipPricingStrategy();
    }

    public function testShouldApply15PercentDiscount(): void
    {
        // Arrange
        $amount = 100.0;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(85.0, $result);
    }

    public function testShouldHandleDecimalPrecision(): void
    {
        // Arrange
        $amount = 99.99;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(84.99, round($result, 2));
    }

    public function testShouldReturnVipAsName(): void
    {
        $this->assertEquals('VIP', $this->strategy->getName());
    }

    public function testShouldReturn15AsDiscountPercentage(): void
    {
        $this->assertEquals(15, $this->strategy->getDiscountPercentage());
    }

    public function testShouldHandleZeroAmount(): void
    {
        $result = $this->strategy->calculate(0.0);
        $this->assertEquals(0.0, $result);
    }
}
