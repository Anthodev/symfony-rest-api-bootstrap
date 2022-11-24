<?php

declare(strict_types=1);

namespace App\Application\Common\Command;

use App\Application\Common\Data\DataLoader;
use App\Application\Common\Data\FixtureFilesProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseLoadFixturesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        protected readonly DataLoader $dataLoader,
        protected readonly FixtureFilesProvider $fixtureFilesProvider,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Loading the fixtures...',
            '========================================================',
            '',
        ]);

        $applicationCommand = $this->getApplication();

        if (null === $applicationCommand) {
            throw new \LogicException('Application command is not set.');
        }

        $output->writeln([
            'Populating the database...',
            '',
        ]);

        $this->entityManager->beginTransaction();

        try {
            $this->loadFixtures();
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            $output->writeln([
                '<error>' . $exception->getMessage() . '</error>',
            ]);

            return Command::FAILURE;
        }

        $output->writeln('Fixtures have loaded successfully!');

        return Command::SUCCESS;
    }

    abstract protected function loadFixtures(): void;
}
