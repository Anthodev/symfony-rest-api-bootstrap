<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Domain\User\Entity\User;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface UserFetcherInterface
{
    public function getUserCount(): int;
}
