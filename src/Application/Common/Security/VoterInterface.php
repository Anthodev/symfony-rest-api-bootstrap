<?php

declare(strict_types=1);

namespace App\Application\Common\Security;

interface VoterInterface
{
    public function getEntityClass(): string;
}
