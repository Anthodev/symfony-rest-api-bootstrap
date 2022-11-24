<?php

declare(strict_types=1);

namespace App\Application\Common\Builder;

use App\Application\Common\Exception\BuilderError;

/**
 * Interface for AbstractBaseBuilder.
 */
interface BaseBuilderInterface
{
    /**
     * Build an object from input data.
     *
     * @param class-string          $className  The class name to use to build object
     * @param array<string, mixed>  $objectData input data to use to fill object built
     * @param array<string, string> $context
     *
     * @throws BuilderError
     */
    public function build(string $className, array $objectData, array $context = []): object;

    /**
     * @param array<string, mixed>  $objectData input data to use to fill object built
     * @param array<string, string> $context
     *
     * @throws BuilderError
     */
    public function populate(array $objectData, object $objectToPopulate, array $context = []): object;
}
