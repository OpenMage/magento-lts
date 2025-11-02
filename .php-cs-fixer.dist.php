<?php

declare(strict_types=1);

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        // see https://cs.symfony.com/doc/ruleSets/PER-CS2.0.html
        '@PER-CS2.0' => true,
        // RISKY: Use && and || logical operators instead of and and or.
        'logical_operators' => true,
        // RISKY: Replaces intval, floatval, doubleval, strval and boolval function calls with according type casting operator.
        'modernize_types_casting' => true,
        // PHP84: Adds or removes ? before single type declarations or |null at the end of union types when parameters have a default null value.
        'nullable_type_declaration_for_default_null_value' => true,
        // Calls to PHPUnit\Framework\TestCase static methods must all be of the same type, either $this->, self:: or static::
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        // Docblocks should have the same indentation as the documented subject.
        'phpdoc_indent' => true,
        // Annotations in PHPDoc should be ordered in defined sequence.
        'phpdoc_order' => true,
        // Order PHPDoc tags by value.
        'phpdoc_order_by_value' => ['annotations' => ['author', 'covers', 'group', 'method', 'throws', 'uses']],
        // Orders all @param annotations in DocBlocks according to method signature.
        'phpdoc_param_order' => true,
        // PHPDoc should start and end with content, excluding the very first and last line of the docblocks.
        'phpdoc_trim' => true,
        // Removes extra blank lines after summary and after description in PHPDoc.
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        // Single line @var PHPDoc should have proper spacing.
        'phpdoc_single_line_var_spacing' => true,
        // Sorts PHPDoc types.
        'phpdoc_types_order' => true,
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
                __DIR__ . '/shell/translations.php',
                __DIR__ . '/shell/update-copyright.php',
            ])
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true),
    );
