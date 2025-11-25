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

    public function test_should_apply_15_percent_discount(): void
    {
        // Arrange
        $amount = 100.0;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(85.0, $result);
    }

    public function test_should_handle_decimal_precision(): void
    {
        // Arrange
        $amount = 99.99;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(84.99, round($result, 2));
    }

    public function test_should_return_vip_as_name(): void
    {
        $this->assertEquals('VIP', $this->strategy->getName());
    }

    public function test_should_return_15_as_discount_percentage(): void
    {
        $this->assertEquals(15, $this->strategy->getDiscountPercentage());
    }

    public function test_should_handle_zero_amount(): void
    {
        $result = $this->strategy->calculate(0.0);
        $this->assertEquals(0.0, $result);
    }
}