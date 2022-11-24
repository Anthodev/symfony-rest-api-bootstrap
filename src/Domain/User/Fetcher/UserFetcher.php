<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Application\Common\Fetcher\AbstractFetcher;
use App\Domain\User\Repository\UserRepository;

class UserFetcher extends AbstractFetcher implements UserFetcherInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($userRepository);
    }

    public function getUserCount(): int
    {
        return $this->userRepository->getUserCount();
    }
}
