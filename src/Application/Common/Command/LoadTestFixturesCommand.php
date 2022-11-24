<?php

declare(strict_types=1);

namespace App\Application\Common\Command;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:load-test-fixtures',
    description: 'Loads the test database with data',
    hidden: false,
)]
class LoadTestFixturesCommand extends BaseLoadFixturesCommand
{
    protected function loadFixtures(): void
    {
        $this->dataLoader->loadFixtures(array_merge(
            $this->fixtureFilesProvider->getReferenceData(),
            $this->fixtureFilesProvider->getTestFixtures(),
        ), validate: false);
    }
}
