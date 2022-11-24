<?php

declare(strict_types=1);

namespace App\Application\Common\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorCollectionFormatter
{
    public function format(ConstraintViolationListInterface $constraintViolationList): ArrayCollection
    {
        $errors = [];

        foreach ($constraintViolationList as $constraintViolation) {
            $propertyPath = $constraintViolation->getPropertyPath();
            $errorMessages = $errors[$propertyPath] ?? [];
            $errorMessages[] = $constraintViolation->getMessage();

            $errors[$propertyPath] = $errorMessages;
        }

        return new ArrayCollection($errors);
    }
}
