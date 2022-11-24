<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets(
        [
            SetList::PSR_12,
            SetList::CLEAN_CODE,
            SetList::DOCTRINE_ANNOTATIONS,
        ]
    );

    $ecsConfig->ruleWithConfiguration(
        ArraySyntaxFixer::class,
        [
            'syntax' => 'short',
        ]
    );
};
