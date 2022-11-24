<?php

declare(strict_types=1);

namespace App\Application\Common\Doctrine\Filter;

use App\Application\Common\Traits\SoftDeletableTrait;
use App\PHP\ClassHelper;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeletedFilter extends SQLFilter
{
    private const SOFT_DELETE_COLUMN_NAME = 'deleted_at';

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!ClassHelper::isTraitUsed(SoftDeletableTrait::class, $targetEntity->getReflectionClass()->getName())) {
            return '';
        }

        $tableNameAndColumnName = sprintf('%s.%s', $targetTableAlias, self::SOFT_DELETE_COLUMN_NAME);

        return sprintf(
            "%s > '%s' OR %s IS NULL",
            $tableNameAndColumnName,
            (new \DateTime())->format('Y-m-d H:i:s'),
            $tableNameAndColumnName
        );
    }
}
