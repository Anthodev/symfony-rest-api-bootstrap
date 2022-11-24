<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class BuilderError extends \LogicException implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return [];
    }
}
