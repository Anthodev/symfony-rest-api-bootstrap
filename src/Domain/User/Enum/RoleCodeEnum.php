<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

enum RoleCodeEnum: string
{
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_USER = 'ROLE_USER';
    case ROLE_GUEST = 'ROLE_GUEST';
}
