<?php

declare(strict_types=1);

namespace App\Application\Common\Data;

use Doctrine\Common\DataFixtures\Purger\ORMPurger as DoctrineOrmPurger;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface as DoctrinePurgerInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Fidry\AliceDataFixtures\Persistence\PurgerFactoryInterface;
use Fidry\AliceDataFixtures\Persistence\PurgerInterface;
use InvalidArgumentException;
use Nelmio\Alice\IsAServiceTrait;

class Purger implements PurgerInterface, PurgerFactoryInterface
{
    use IsAServiceTrait;

    private ObjectManager $manager;
    private ?PurgeMode $purgeMode;
    private DoctrinePurgerInterface $purger;

    public function __construct(ObjectManager $manager, PurgeMode $purgeMode = null)
    {
        $this->manager = $manager;
        $this->purgeMode = $purgeMode;

        $this->purger = self::createPurger($manager, $purgeMode);
    }

    public function create(PurgeMode $mode, PurgerInterface $purger = null): PurgerInterface
    {
        if (null === $purger) {
            return new self($this->manager, $mode);
        }

        if ($purger instanceof DoctrinePurgerInterface) {
            /** @phpstan-ignore-next-line */
            $manager = $purger->getObjectManager();
        } elseif ($purger instanceof self) {
            $manager = $purger->manager;
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected purger to be either and instance of "%s" or "%s". Got "%s".',
                    DoctrinePurgerInterface::class,
                    __CLASS__,
                    $purger::class
                )
            );
        }

        if (null === $manager) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected purger "%s" to have an object manager, got "null" instead.',
                    $purger::class
                )
            );
        }

        return new self($manager, $mode);
    }

    /**
     * @throws Exception
     */
    public function purge(): void
    {
        // Because MySQL rocks, you got to disable foreign key checks when doing a TRUNCATE/DELETE unlike in for example
        // PostgreSQL. This ideally should be done in the Purger of doctrine/data-fixtures but meanwhile we are doing
        // it here.
        // See the progress in https://github.com/doctrine/data-fixtures/pull/272
        $disableFkChecks = (
            $this->purger instanceof DoctrineOrmPurger
            /** @phpstan-ignore-next-line */
            && in_array($this->purgeMode->getValue(), [PurgeMode::createDeleteMode()->getValue(), PurgeMode::createTruncateMode()->getValue()], true)
            && $this->purger->getObjectManager()->getConnection()->getDatabasePlatform() instanceof MySQLPlatform
        );

        if ($disableFkChecks) {
            /** @phpstan-ignore-next-line */
            $connection = $this->purger->getObjectManager()?->getConnection();

            $connection->executeStatement("SET session_replication_role = 'replica';");
        }

        $this->purger->purge();

        if ($disableFkChecks && isset($connection)) {
            $connection->executeStatement("SET session_replication_role = 'origin';");
        }
    }

    private static function createPurger(ObjectManager $manager, ?PurgeMode $purgeMode): DoctrinePurgerInterface
    {
        if ($manager instanceof EntityManagerInterface) {
            $purger = new DoctrineOrmPurger($manager, [
                'doctrine_migration_versions',
                'role',
            ]);

            if (null !== $purgeMode) {
                $purger->setPurgeMode($purgeMode->getValue());
            }

            return $purger;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Cannot create a purger for ObjectManager of class %s',
                $manager::class
            )
        );
    }
}
