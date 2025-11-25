<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class PromotionalEvent
{
    private string $id;

    public function __construct(
        private string $name,
        private string $code,
        private int $discountPercentage,
        private \DateTimeImmutable $startDate,
        private \DateTimeImmutable $endDate,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();

        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new \InvalidArgumentException('Discount percentage must be between 0 and 100');
        }

        if ($endDate < $startDate) {
            throw new \InvalidArgumentException('End date must be after start date');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDiscountPercentage(): int
    {
        return $this->discountPercentage;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function isActive(\DateTimeInterface $currentDate): bool
    {
        return $currentDate >= $this->startDate && $currentDate <= $this->endDate;
    }
}
