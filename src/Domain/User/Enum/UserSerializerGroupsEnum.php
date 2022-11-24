<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

enum UserSerializerGroupsEnum: string
{
    case USER_READ = 'user:read';

    /**
     * @return list<string>
     */
    public static function toArray(): array
    {
        return [
            self::USER_READ->value,
        ];
    }
}
