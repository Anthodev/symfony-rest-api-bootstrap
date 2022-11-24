<?php

declare(strict_types=1);

namespace App\Application\Common\Service;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Exception\EntityValidationException;
use App\Application\Common\Validator\ValidationErrorCollectionFormatter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidationService implements EntityValidationInterface
{
    public const VALIDATION_GROUP_INSERT = 'insert';
    public const VALIDATION_GROUP_UPDATE = 'update';
    public const VALIDATION_GROUP_DELETE = 'delete';

    public function __construct(
        protected ValidationErrorCollectionFormatter $validationErrorCollectionFormatter,
        protected ValidatorInterface $validator,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validateEntity(EntityInterface $entity, array|Constraint $constraints = null, array|GroupSequence|string $groups = null): void
    {
        /** @var ConstraintViolationList|ConstraintViolation[] $errors */
        $errors = $this->validator->validate($entity, $constraints, $groups);

        if ($errors->count() > 0) {
            throw (new EntityValidationException((string) $errors))->setErrors($this->validationErrorCollectionFormatter->format($errors));
        }
    }
}
