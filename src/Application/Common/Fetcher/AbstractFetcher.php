<?php

declare(strict_types=1);

namespace App\Application\Common\Fetcher;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Exception\ORMApplicationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractFetcher implements FetcherInterface
{
    #[Required]
    public LoggerInterface $logger;

    #[Required]
    public EntityManagerInterface $entityManager;

    public function __construct(
        protected readonly ServiceEntityRepository $repository
    ) {
    }

    public function findOneBy(array $criteria = [], ?array $orderBy = null, bool $disableSoftDeleteFilter = false): ?EntityInterface
    {
        if ($disableSoftDeleteFilter) {
            $this->disableSoftDeleteFilter();
        }

        /** @var ?EntityInterface $data */
        $data = $this->repository->findOneBy($criteria, $orderBy);

        $this->enableSoftDeleteFilter();

        $this->logger->debug('find one ' . static::class, compact('data'));

        return $data;
    }

    /**
     * @param array<string, string>|null $orderBy
     * @throws ORMApplicationException
     */
    public function findBy(
        array $criteria = [],
        ?array $orderBy = null,
        int $limit = null,
        int $offset = null,
        bool $disableSoftDeleteFilter = false,
    ): array {
        if ($disableSoftDeleteFilter) {
            $this->disableSoftDeleteFilter();
        }

        /** @var EntityInterface[] $result */
        $result = $this->repository->findBy($criteria, $orderBy, $limit, $offset);

        $this->enableSoftDeleteFilter();

        $this->logger->debug('find one ' . static::class, compact('result'));

        return $result;
    }

    public function findAll(bool $disableSoftDeleteFilter = false): array
    {
        if ($disableSoftDeleteFilter) {
            $this->disableSoftDeleteFilter();
        }

        /** @var list<EntityInterface> $entity */
        $entity = $this->repository->findAll();

        $this->enableSoftDeleteFilter();

        $this->logger->debug('find all ' . static::class, compact('entity'));

        return $entity;
    }

    public function find(int|string|Uuid $id, bool $disableSoftDeleteFilter = false): ?EntityInterface
    {
        $criteria = match (true) {
            $id instanceof Uuid, is_string($id) => ['uuid' => $id],
            default => ['id' => $id],
        };

        if ($disableSoftDeleteFilter) {
            $this->disableSoftDeleteFilter();
        }

        /** @var EntityInterface|null $entity $entity */
        $entity = $this->repository->findOneBy($criteria);

        $this->enableSoftDeleteFilter();

        $this->logger->debug('find ' . static::class, compact('entity'));

        return $entity ?? null;
    }

    public function getEntityRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function enableSoftDeleteFilter(): void
    {
        if (!$this->entityManager->getFilters()->isEnabled('softDeleted')) {
            $this->entityManager->getFilters()->enable('softDeleted');
            $this->logger->debug(__FUNCTION__, ['isEnabled' => $this->entityManager->getFilters()->isEnabled('softDeleted')]);
        }
    }

    protected function disableSoftDeleteFilter(): void
    {
        if ($this->entityManager->getFilters()->isEnabled('softDeleted')) {
            $this->entityManager->getFilters()->disable('softDeleted');
            $this->logger->debug(__FUNCTION__, ['isEnabled' => $this->entityManager->getFilters()->isEnabled('softDeleted')]);
        }
    }
}
