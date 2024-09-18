<?php

declare(strict_types=1);

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->ignoreVCS(true)
    ->ignoreDotFiles(false)
;

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'yoda_style' => false,
        'nullable_type_declaration_for_default_null_value' => false,
        'declare_strict_types' => true,
    ])
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
