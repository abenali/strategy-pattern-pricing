<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Strategy;

use App\Domain\Entity\PromotionalEvent;
use App\Infrastructure\Strategy\EventBasedPricingStrategy;
use PHPUnit\Framework\TestCase;

final class EventBasedPricingStrategyTest extends TestCase
{
    public function test_should_apply_discount_when_event_is_active(): void
    {
        // Arrange
        $startDate = new \DateTimeImmutable('-1 day');
        $endDate = new \DateTimeImmutable('+1 day');
        $currentDate = new \DateTimeImmutable('now');
        
        $event = new PromotionalEvent(
            'Black Friday',
            'BF2025',
            20,
            $startDate,
            $endDate
        );

        $strategy = new EventBasedPricingStrategy($event, $currentDate);
        $amount = 100.0;

        // Act
        $result = $strategy->calculate($amount);

        // Assert
        $this->assertEquals(80.0, $result);
        $this->assertEquals(20, $strategy->getDiscountPercentage());
    }

    public function test_should_not_apply_discount_when_event_is_not_started(): void
    {
        // Arrange
        $startDate = new \DateTimeImmutable('+1 day');
        $endDate = new \DateTimeImmutable('+2 days');
        $currentDate = new \DateTimeImmutable('now');
        
        $event = new PromotionalEvent(
            'Future Sale',
            'FUTURE',
            20,
            $startDate,
            $endDate
        );

        $strategy = new EventBasedPricingStrategy($event, $currentDate);
        $amount = 100.0;

        // Act
        $result = $strategy->calculate($amount);

        // Assert
        $this->assertEquals(100.0, $result);
        $this->assertEquals(0, $strategy->getDiscountPercentage());
    }

    public function test_should_not_apply_discount_when_event_is_ended(): void
    {
        // Arrange
        $startDate = new \DateTimeImmutable('-2 days');
        $endDate = new \DateTimeImmutable('-1 day');
        $currentDate = new \DateTimeImmutable('now');
        
        $event = new PromotionalEvent(
            'Past Sale',
            'PAST',
            20,
            $startDate,
            $endDate
        );

        $strategy = new EventBasedPricingStrategy($event, $currentDate);
        $amount = 100.0;

        // Act
        $result = $strategy->calculate($amount);

        // Assert
        $this->assertEquals(100.0, $result);
        $this->assertEquals(0, $strategy->getDiscountPercentage());
    }

    public function test_should_return_event_name(): void
    {
        // Arrange
        $startDate = new \DateTimeImmutable('-1 day');
        $endDate = new \DateTimeImmutable('+1 day');
        $currentDate = new \DateTimeImmutable('now');
        
        $event = new PromotionalEvent(
            'Summer Sale',
            'SUMMER',
            15,
            $startDate,
            $endDate
        );

        $strategy = new EventBasedPricingStrategy($event, $currentDate);

        // Act & Assert
        $this->assertEquals('Summer Sale', $strategy->getName());
    }
}
