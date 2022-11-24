<?php

declare(strict_types=1);

namespace App\Application\Common\Manager;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtPayloadManager
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtTokenManager,
        private readonly Security $security,
    ) {
    }

    public function getJwtToken(): string
    {
        $user = $this->security->getUser();
        \assert($user instanceof UserInterface);

        return $this->jwtTokenManager->create($user);
    }
}
