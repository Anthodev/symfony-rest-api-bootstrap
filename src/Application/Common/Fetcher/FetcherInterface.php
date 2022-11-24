<?php

declare(strict_types=1);

namespace App\Application\Common\Fetcher;

use App\Application\Common\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Uid\Uuid;

interface FetcherInterface
{
    /**
     * @return EntityInterface[]
     */
    public function findAll(bool $disableSoftDeleteFilter = false): array;

    public function find(string|Uuid $id, bool $disableSoftDeleteFilter = false): ?EntityInterface;

    /**
     * @param array<string, object|string|array<string, string|object>>            $criteria
     * @param array<string, string|array{field: string, orientation: string}>|null $orderBy
     *
     * @return EntityInterface[]
     */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        int $limit = null,
        int $offset = null,
        bool $disableSoftDeleteFilter = false,
    ): array;

    /**
     * @param array<string, object|string|array<string, string|object>> $criteria
     * @param array<string, string>|null                                $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null, bool $disableSoftDeleteFilter = false): ?EntityInterface;

    public function getEntityRepository(): ServiceEntityRepository;
}
