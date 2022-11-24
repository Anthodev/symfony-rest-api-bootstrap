<?php

declare(strict_types=1);

namespace App\Application\Common\Data;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\Loader\PurgerLoader;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataLoader
{
    public function __construct(
        private readonly ?PurgerLoader $fixtureLoader,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /** @param list<string> $fixtures */
    public function loadFixtures(array $fixtures, bool $purge = false, bool $validate = true): void
    {
        if (!$this->fixtureLoader instanceof PurgerLoader) {
            throw new \RuntimeException(
                sprintf('This method needs %s to be available', PurgerLoader::class)
            );
        }

        $purgeMode = PurgeMode::createNoPurgeMode();

        if (true === $purge) {
            $purgeMode = PurgeMode::createDeleteMode();
        }

        foreach ($this->fixtureLoader->load(fixturesFiles: $fixtures, purgeMode: $purgeMode) as $entity) {
            if (false === $validate) {
                return;
            }

            $this->entityManager->refresh($entity);
            $constraintViolationList = $this->validator->validate($entity);

            if (0 !== $constraintViolationList->count()) {
                $violationsAsStrings = [
                    /** @phpstan-ignore-next-line  */
                    sprintf('Entity: %s (%s) ', $entity, $entity::class),
                ];

                /** @var ConstraintViolation $constraintViolation */
                foreach ($constraintViolationList as $constraintViolation) {
                    $violationsAsStrings[] = sprintf(
                        '%s: %s',
                        $constraintViolation->getPropertyPath(),
                        $constraintViolation->getMessage(),
                    );
                }

                throw new \RuntimeException(
                    implode("\n", $violationsAsStrings)
                );
            }
        }
    }
}
