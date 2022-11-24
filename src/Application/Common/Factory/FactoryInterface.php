<?php

declare(strict_types=1);

namespace App\Application\Common\Factory;

use App\Application\Common\Entity\EntityInterface;

interface FactoryInterface
{
    /** @param array<string, mixed> $input */
    public static function create(array $input): EntityInterface;
}
