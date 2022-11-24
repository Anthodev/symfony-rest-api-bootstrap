<?php

declare(strict_types=1);

namespace App\Application\Common\Manager;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Exception\EntityNotFoundException;
use App\Application\Common\Exception\EntityValidationException;

interface BaseManagerInterface
{
    /**
     * @throws EntityValidationException
     */
    public function insert(EntityInterface $entity): EntityInterface;

    /**
     * @throws EntityValidationException
     * @throws EntityNotFoundException
     */
    public function update(EntityInterface $entityToUpdate): EntityInterface;

    public function delete(EntityInterface $entity): EntityInterface;
}
