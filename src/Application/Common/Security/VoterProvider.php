<?php

declare(strict_types=1);

namespace App\Application\Common\Security;

class VoterProvider
{
    public function __construct(
        /** @var list<VoterInterface> $voters */
        private iterable $voters
    ) {
    }

    /** @return list<VoterInterface> */
    public function getFromClassName(string $className): iterable
    {
        /** @var VoterInterface $voter */
        foreach ($this->voters as $voter) {
            if (!is_a($className, $voter->getEntityClass(), true)) {
                continue;
            }

            yield $voter;
        }
    }
}
