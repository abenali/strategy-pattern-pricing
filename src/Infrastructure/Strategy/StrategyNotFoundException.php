<?php

declare(strict_types=1);

namespace App\Infrastructure\Strategy;

use Symfony\Component\HttpFoundation\Response;

final class StrategyNotFoundException extends \RuntimeException
{
    public function __construct(string $code, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Strategy with code "%s" not found', $code),
            Response::HTTP_NOT_FOUND,
            $previous
        );
    }
}
