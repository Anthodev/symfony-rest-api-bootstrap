<?php

declare(strict_types=1);

namespace App\Domain\User\Fetcher;

use App\Domain\User\Entity\Role;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface RoleFetcherInterface
{
}
