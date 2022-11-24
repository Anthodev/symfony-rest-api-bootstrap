<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Application\Common\Factory\FactoryInterface;
use App\Domain\User\Entity\Role;
use App\Domain\User\Entity\User;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class UserFactory implements FactoryInterface
{
    public static function create(array $input = []): User
    {
        $faker = Factory::create();

        /** @var string $email */
        $email = $input['email'] ?? $faker->email();

        /** @var string $username */
        $username = $input['username'] ?? $faker->userName();

        /** @var Role $role */
        $role = $input['role'] ?? new Role();

        /** @var string $password */
        $password = $input['password'] ?? $faker->password();

        $user = new User();

        $user->setEmail($email);

        $user->setUsername($username);

        $user->setRole($role);

        $user->setPlainPassword($password);

        if (!isset($input['uuid'])) {
            $user->setUuid(Uuid::v4());
        }

        return $user;
    }
}
