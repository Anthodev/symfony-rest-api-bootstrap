<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Application\Common\Data\DataLoader;
use App\Application\Common\Data\FixtureFilesProvider;
use Doctrine\ORM\EntityManagerInterface;

trait AliceLoaderTrait
{
    /**
     * @param list<string> $fixtures
     */
    public function loadAdditionalFixtures(?array $fixtures = []): void
    {
        /** @var DataLoader $dataLoader */
        $dataLoader = static::getContainer()->get(DataLoader::class);
        $projectDir = static::getContainer()->getParameter('kernel.project_dir');

        $fixtures = array_merge(
            [
                $projectDir.'/tests/DataFixtures/Base.yaml',
            ],
            $fixtures,
        );

        $fixtureFilesProvider = static::getContainer()->get(FixtureFilesProvider::class);

        $dataLoader->loadFixtures(array_merge(
            $fixtureFilesProvider->getReferenceData(),
            $fixtures,
        ), true, false);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $entityManager->clear();
    }

    public function loadBaseFixturesOnly(): void
    {
        $this->loadAdditionalFixtures();
    }
}
