<?php

namespace App\Application\Common\Provider;

use App\Domain\User\Entity\Role;
use App\Domain\User\Entity\User;
use App\Domain\User\Fetcher\UserFetcherInterface;
use Symfony\Component\Security\Core\Security;

class ContextProvider
{
    public function __construct(
        private readonly Security $security,
        private readonly UserFetcherInterface $userFetcher,
    ) {
    }

    public function getContextUser(): User
    {
        $user = $this->security->getUser();
        \assert($user instanceof User);

        $user = $this->userFetcher->find($user->getUuid());
        \assert($user instanceof User);

        return $user;
    }

    public function getContextUserRoleCode(): string
    {
        $user = $this->getContextUser();

        $role = $user->getRole();
        \assert($role instanceof Role);

        return $role->getCode();
    }
}
