<?php

declare(strict_types=1);

$finder = new PhpCsFixer\Finder();
$finder
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor');

$config = new PhpCsFixer\Config();
$config
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PhpCsFixer' => true,
        'declare_strict_types' => true,
        'ordered_class_elements' => false,
        'php_unit_test_class_requires_covers' => false,
        'php_unit_internal_class' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    ])
    ->setFinder($finder);

return $config;
