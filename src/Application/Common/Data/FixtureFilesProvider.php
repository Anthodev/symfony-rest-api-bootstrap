<?php

declare(strict_types=1);

namespace App\Application\Common\Data;

class FixtureFilesProvider
{
    public function __construct(
        private readonly string $dataFixtureDir,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getReferenceData(): array
    {
        $databaseReferenceDataPath = "$this->dataFixtureDir/ReferenceData";

        return [
            "$databaseReferenceDataPath/User/Role.yaml",
        ];
    }

    /** @return list<string> */
    public function getFixtures(): array
    {
        $databaseFixturesPath = $this->dataFixtureDir;

        return [
        ];
    }

    /**
     * @return list<string>
     */
    public function getTestFixtures(): array
    {
        return [
            "$this->dataFixtureDir/User/Entity/User.yaml",
        ];
    }
}
