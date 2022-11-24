<?php

declare(strict_types=1);

namespace App\Domain\User\Service;

use App\Domain\User\Entity\User;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPassword
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function hash(User $user): string
    {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword) {
            throw new InvalidArgumentException('The plain password cannot be null.');
        }

        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }
}
