<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Strategy;

use App\Infrastructure\Strategy\StandardPricingStrategy;
use PHPUnit\Framework\TestCase;

final class StandardPricingStrategyTest extends TestCase
{
    private StandardPricingStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new StandardPricingStrategy();
    }

    public function testShouldNotApplyDiscount(): void
    {
        // Arrange
        $amount = 100.0;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(100.0, $result);
    }

    public function testShouldReturnStandardAsName(): void
    {
        $this->assertEquals('Standard', $this->strategy->getName());
    }

    public function testShouldReturn0AsDiscountPercentage(): void
    {
        $this->assertEquals(0, $this->strategy->getDiscountPercentage());
    }

    public function testShouldHandleZeroAmount(): void
    {
        $result = $this->strategy->calculate(0.0);
        $this->assertEquals(0.0, $result);
    }
}
