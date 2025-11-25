<?php

declare(strict_types=1);

namespace App\Infrastructure\Strategy;

use App\Domain\Entity\PromotionalEvent;
use App\Domain\Strategy\PricingStrategyInterface;

final class EventBasedPricingStrategy implements PricingStrategyInterface
{
    public function __construct(
        private PromotionalEvent $event,
        private \DateTimeInterface $currentDate,
    ) {
    }

    public function calculate(float $amount): float
    {
        if (!$this->event->isActive($this->currentDate)) {
            return $amount;
        }

        return $amount * (1 - $this->event->getDiscountPercentage() / 100);
    }

    public function getName(): string
    {
        return $this->event->getName();
    }

    public function getDiscountPercentage(): int
    {
        if (!$this->event->isActive($this->currentDate)) {
            return 0;
        }

        return $this->event->getDiscountPercentage();
    }
}
