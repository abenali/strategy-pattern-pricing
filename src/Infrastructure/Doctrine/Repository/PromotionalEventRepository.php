<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\PromotionalEvent;
use App\Domain\Repository\PromotionalEventRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PromotionalEvent>
 */
final class PromotionalEventRepository extends ServiceEntityRepository implements PromotionalEventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromotionalEvent::class);
    }

    public function findByCode(string $code): PromotionalEvent
    {
        $event = $this->findOneBy(['code' => $code]);

        if (!$event instanceof PromotionalEvent) {
            throw new \RuntimeException(sprintf('Promotional event with code "%s" not found', $code));
        }

        return $event;
    }
}
