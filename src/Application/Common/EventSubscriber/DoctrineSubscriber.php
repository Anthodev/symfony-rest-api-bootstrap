<?php

declare(strict_types=1);

namespace App\Application\Common\EventSubscriber;

use App\Application\Common\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class DoctrineSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        /** @var EntityInterface $entity */
        $entity = $event->getObject();

        $entity->setCreatedAt();
        $entity->setDefaultUuid();
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        /** @var EntityInterface $entity */
        $entity = $event->getObject();

        $entity->setUpdatedAt();
    }
}
