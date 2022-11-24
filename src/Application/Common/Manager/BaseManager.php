<?php

declare(strict_types=1);

namespace App\Application\Common\Manager;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Exception\EntityNotFoundException;
use App\Application\Common\Exception\EntityValidationException;
use App\Application\Common\Service\EntityValidationInterface;
use App\Application\Common\Service\EntityValidationService;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;

class BaseManager implements BaseManagerInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        private readonly EntityValidationInterface $validationService,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws EntityValidationException
     */
    public function insert(EntityInterface $entity): EntityInterface
    {
        $this->validationService->validateEntity($entity, groups: [EntityValidationService::VALIDATION_GROUP_INSERT, (new ReflectionClass($entity))->getShortName()]);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function update(EntityInterface $entityToUpdate): EntityInterface
    {
        $this->validationService->validateEntity(entity: $entityToUpdate, groups: [EntityValidationService::VALIDATION_GROUP_UPDATE, (new ReflectionClass($entityToUpdate))->getShortName()]);

        if (null === $entityToUpdate->getId()) {
            throw new EntityNotFoundException(sprintf('Entity %s with id "%s" not found', (new ReflectionClass($entityToUpdate))->getShortName(), $entityToUpdate->getId()));
        }

        $this->entityManager->flush();

        return $entityToUpdate;
    }

    public function delete(EntityInterface $entity): EntityInterface
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @return array<int, EntityInterface>
     */
    public function list(string $className): array
    {
        /** @phpstan-ignore-next-line  */
        return $this->entityManager->getRepository($className)->findAll();
    }
}
