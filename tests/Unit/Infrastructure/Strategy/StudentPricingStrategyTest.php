<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Strategy;

use App\Infrastructure\Strategy\StudentPricingStrategy;
use PHPUnit\Framework\TestCase;

final class StudentPricingStrategyTest extends TestCase
{
    private StudentPricingStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new StudentPricingStrategy();
    }

    public function test_should_apply_10_percent_discount(): void
    {
        // Arrange
        $amount = 100.0;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(90.0, $result);
    }

    public function test_should_handle_decimal_precision(): void
    {
        // Arrange
        $amount = 99.99;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(89.99, round($result, 2));
    }

    public function test_should_return_student_as_name(): void
    {
        $this->assertEquals('Student', $this->strategy->getName());
    }

    public function test_should_return_10_as_discount_percentage(): void
    {
        $this->assertEquals(10, $this->strategy->getDiscountPercentage());
    }

    public function test_should_handle_zero_amount(): void
    {
        $result = $this->strategy->calculate(0.0);
        $this->assertEquals(0.0, $result);
    }
}