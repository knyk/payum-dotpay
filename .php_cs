<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('docker')
    ->exclude('bin')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        '@DoctrineAnnotation' => true,
        'array_indentation' => true,
        'no_useless_return' => true,
        'no_useless_else' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'heredoc_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'fully_qualified_strict_types' => true,
        'no_null_property_initialization' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'constant_case' => [
            'case' => 'lower',
        ],
        'visibility_required' => [
            'elements' => [
                'property',
                'method',
                'const',
            ],
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method',
            ],
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
