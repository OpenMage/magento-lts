<?php

/*
* This document has been generated with
* https://mlocati.github.io/php-cs-fixer-configurator/#version:3.4.0|configurator
* you can change this configuration by importing this file.
*/
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        // see https://cs.symfony.com/doc/ruleSets/PER-CS2.0.html
        '@PER-CS2.0' => true,
        // RISKY: Use && and || logical operators instead of and and or.
        'logical_operators' => true,
        // Unify heredoc or nowdoc closing marker.
        'heredoc_closing_marker' => true,
        // RISKY: Replaces intval, floatval, doubleval, strval and boolval function calls with according type casting operator.
        'modernize_types_casting' => true,
        // PHP84: Adds or removes ? before single type declarations or |null at the end of union types when parameters have a default null value.
        'nullable_type_declaration_for_default_null_value' => true,
        // Calls to PHPUnit\Framework\TestCase static methods must all be of the same type, either $this->, self:: or static::
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
        // All items of the given PHPDoc tags must be either left-aligned or (by default) aligned vertically.
        'phpdoc_align' => true,
        // PHPDoc should contain @param for all params.
        'phpdoc_add_missing_param_annotation' => true,
        // Docblocks should have the same indentation as the documented subject.
        'phpdoc_indent' => true,
        // Convert double quotes to single quotes for simple strings.
        'single_quote' => true,
        // Arguments lists, array destructuring lists, arrays that are multi-line, match-lines and parameters lists must have a trailing comma.
        // removed "match" and "parameters" for PHP7
        // see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/8308
        'trailing_comma_in_multiline' => ['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays']],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__,
            ])
            ->exclude([
                'shell/translations.php',
            ])
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true),
    );
