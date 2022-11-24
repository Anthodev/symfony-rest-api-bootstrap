<?php

declare(strict_types=1);

namespace App\Application\Common\Command;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:load-fixtures',
    description: 'Loads the database with data',
    hidden: false,
)]
class LoadFixturesCommand extends BaseLoadFixturesCommand
{
    protected function loadFixtures(): void
    {
        $this->dataLoader->loadFixtures(array_merge(
            $this->fixtureFilesProvider->getReferenceData(),
            $this->fixtureFilesProvider->getFixtures(),
        ));
    }
}
