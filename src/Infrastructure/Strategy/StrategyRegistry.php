<?php

declare(strict_types=1);

namespace App\Infrastructure\Strategy;

use App\Domain\Repository\PromotionalEventRepositoryInterface;
use App\Domain\Strategy\PricingStrategyInterface;

final class StrategyRegistry
{
    public function __construct(
        private readonly PromotionalEventRepositoryInterface $eventRepository,
        private ?\DateTimeInterface $currentDate = null,
    ) {
        $this->currentDate = $currentDate ?? new \DateTime();
    }

    /**
     * @throws StrategyNotFoundException
     */
    public function get(string $code): PricingStrategyInterface
    {
        return match ($code) {
            'standard' => new StandardPricingStrategy(),
            'vip' => new VipPricingStrategy(),
            'student' => new StudentPricingStrategy(),
            'black-friday' => $this->createEventStrategy('black-friday'),
            'summer-sale' => $this->createEventStrategy('summer-sale'),
            default => throw new StrategyNotFoundException($code),
        };
    }

    private function createEventStrategy(string $code): PricingStrategyInterface
    {
        try {
            $event = $this->eventRepository->findByCode($code);
        } catch (\RuntimeException $e) {
            throw new StrategyNotFoundException($code, previous: $e);
        }

        return new EventBasedPricingStrategy($event, $this->currentDate);
    }
}
