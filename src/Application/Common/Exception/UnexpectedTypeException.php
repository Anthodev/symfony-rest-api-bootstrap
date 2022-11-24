<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnexpectedTypeException extends ApplicationException
{
    public function __construct(mixed $value, string $expectedType)
    {
        parent::__construct(sprintf('Expected argument of type "%s", "%s" given', $expectedType, get_debug_type($value)));
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
