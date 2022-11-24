<?php

declare(strict_types=1);

namespace App\Application\Common\Service;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Exception\EntityValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;

interface EntityValidationInterface
{
    /**
     * Validates a EntityInterface object against a constraint or a list of constraints.
     *
     * @param Constraint|Constraint[]|null                          $constraints The constraint(s) to validate against
     * @param string|GroupSequence|array<string|GroupSequence>|null $groups      The validation groups to validate. If none is given, "Default" is assumed
     *
     * @throws EntityValidationException
     */
    public function validateEntity(EntityInterface $entity, array|Constraint $constraints = null, array|GroupSequence|string $groups = null): void;
}
