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

    public function test_should_not_apply_discount(): void
    {
        // Arrange
        $amount = 100.0;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(100.0, $result);
    }

    public function test_should_return_standard_as_name(): void
    {
        $this->assertEquals('Standard', $this->strategy->getName());
    }

    public function test_should_return_0_as_discount_percentage(): void
    {
        $this->assertEquals(0, $this->strategy->getDiscountPercentage());
    }

    public function test_should_handle_zero_amount(): void
    {
        $result = $this->strategy->calculate(0.0);
        $this->assertEquals(0.0, $result);
    }
}
