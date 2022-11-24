<?php

declare(strict_types=1);

namespace App\Domain\User\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AdminDemote extends Constraint
{
    public const USER_ADMIN_DEMOTE_ERROR_CODE = '01592fb9-a6ed-4577-950e-09d093f1468f';
    public string $message = 'You cannot demote yourself without another admin.';

    /**
     * @return string|string[]
     */
    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
