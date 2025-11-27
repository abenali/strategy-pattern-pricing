<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\PromotionalEvent;

interface PromotionalEventRepositoryInterface
{
    /**
     * Find a promotional event by code.
     *
     * @throws \RuntimeException if event not found
     */
    public function findByCode(string $code): PromotionalEvent;
}
