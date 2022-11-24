<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Application\Common\Fetcher\AbstractFetcher;
use App\Domain\User\Repository\RoleRepository;

class RoleFetcher extends AbstractFetcher implements RoleFetcherInterface
{
    public function __construct(
        RoleRepository $roleRepository,
    ) {
        parent::__construct($roleRepository);
    }
}
