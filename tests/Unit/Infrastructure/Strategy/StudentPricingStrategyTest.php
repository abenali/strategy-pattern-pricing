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

    public function testShouldApply10PercentDiscount(): void
    {
        // Arrange
        $amount = 100.0;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(90.0, $result);
    }

    public function testShouldHandleDecimalPrecision(): void
    {
        // Arrange
        $amount = 99.99;

        // Act
        $result = $this->strategy->calculate($amount);

        // Assert
        $this->assertEquals(89.99, round($result, 2));
    }

    public function testShouldReturnStudentAsName(): void
    {
        $this->assertEquals('Student', $this->strategy->getName());
    }

    public function testShouldReturn10AsDiscountPercentage(): void
    {
        $this->assertEquals(10, $this->strategy->getDiscountPercentage());
    }

    public function testShouldHandleZeroAmount(): void
    {
        $result = $this->strategy->calculate(0.0);
        $this->assertEquals(0.0, $result);
    }
}
