<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'promotional_events')]
class PromotionalEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $code;

    #[ORM\Column(type: 'integer')]
    private int $discountPercentage;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $endDate;

    public function __construct(
        string $name,
        string $code,
        int $discountPercentage,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
        $this->name = $name;
        $this->code = $code;
        $this->discountPercentage = $discountPercentage;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

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
