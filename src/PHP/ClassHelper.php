<?php

declare(strict_types=1);

namespace App\PHP;

class ClassHelper
{
    public static function isTraitUsed(string $trait, object|string $subject): bool
    {
        if (\is_object($subject)) {
            $subject = $subject::class;
        }

        if (!class_exists($subject)) {
            throw new \RuntimeException(
                sprintf('Class %s does not exist', $subject)
            );
        }

        return \in_array($trait, self::getUsedTraits($subject), true);
    }

    /**
     * @return list<string>
     */
    private static function getUsedTraits(string $subject): array
    {
        $treeSubjects = [];
        do {
            $treeSubjects[] = $subject;
        } while (false !== $subject = get_parent_class($subject));

        $usedTraits = [];
        foreach ($treeSubjects as $treeSubject) {
            $traits = class_uses($treeSubject);
            \assert(\is_array($traits));
            $usedTraits[] = $traits;
        }
        $usedTraits = array_merge(...$usedTraits);

        return array_values($usedTraits);
    }
}
