<?php

declare(strict_types=1);

namespace App\Domain\User\ReferenceProvider;

use App\Domain\User\Entity\Role;
use App\Domain\User\Fetcher\RoleFetcherInterface;

class RoleReferenceProvider
{
    private static RoleFetcherInterface $roleFetcher;

    public function __construct(RoleFetcherInterface $roleFetcher)
    {
        self::$roleFetcher = $roleFetcher;
    }

    public static function get(string $code): Role
    {
        $role = self::$roleFetcher->findOneBy([
            'code' => $code,
        ]);

        if (!$role instanceof Role) {
            throw new \RuntimeException(
                sprintf('Cannot find Role with code %s', $code)
            );
        }

        return $role;
    }
}
