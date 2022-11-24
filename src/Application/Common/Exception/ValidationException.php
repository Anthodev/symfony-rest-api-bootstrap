<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Doctrine\Common\Collections\ArrayCollection;
use Stringable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ValidationException extends ApplicationException
{
    public function __construct(
        string $message = '',
        ?Throwable $previous = null,
        protected ArrayCollection $errors = new ArrayCollection()
    ) {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $previous);
    }

    public function getErrors(): ArrayCollection
    {
        return $this->errors;
    }

    public function setErrors(ArrayCollection $errors): ValidationException
    {
        $this->errors = $errors;

        return $this;
    }

    public function addError(string $propertyPath, Stringable|string $message): ArrayCollection
    {
        $this->errors->set($propertyPath, $message);

        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
