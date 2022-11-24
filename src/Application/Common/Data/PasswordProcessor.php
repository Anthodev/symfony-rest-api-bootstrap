<?php

declare(strict_types=1);

namespace App\Application\Common\Data;

use App\Domain\User\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function preProcess(string $id, object $object): void
    {
        if (!$object instanceof User) {
            return;
        }

        $plainPassword = $object->getPassword();
        \assert(\is_string($plainPassword));

        $object->setPassword(
            $this->userPasswordHasher->hashPassword($object, $plainPassword)
        );
    }

    public function postProcess(string $id, object $object): void
    {
    }
}
