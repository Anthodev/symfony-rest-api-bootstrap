<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Doctrine\ORM\EntityNotFoundException as DoctrineEntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class EntityNotFoundException extends DoctrineEntityNotFoundException implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    /**
     * {@inheritDoc}
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return [];
    }
}
