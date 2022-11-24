<?php

declare(strict_types=1);

namespace Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

trait TestWebUtilsTrait
{
    public function getSecurity(): Security
    {
        return static::$client->getContainer()->get(Security::class);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return static::$client->getContainer()->get(EntityManagerInterface::class);
    }
}
