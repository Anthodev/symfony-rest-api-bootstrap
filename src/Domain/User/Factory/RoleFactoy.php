<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Application\Common\Factory\FactoryInterface;
use App\Domain\User\Entity\Role;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class RoleFactoy implements FactoryInterface
{
    public static function create(array $input = []): Role
    {
        $faker = Factory::create();

        /** @var string $code */
        $code = $input['code'] ?? $faker->word();

        /** @var string $label */
        $label = $input['label'] ?? $faker->word();

        $role = new Role();

        $role->setCode($code);

        $role->setLabel($label);

        if (!isset($input['uuid'])) {
            $role->setUuid(Uuid::v4());
        }

        return $role;
    }
}
