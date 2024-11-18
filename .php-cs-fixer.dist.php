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
        // There MUST be one blank line after the namespace declaration.
        'blank_line_after_namespace' => true,
        // The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.
        'braces' => true,
        // Remove extra spaces in a nullable typehint.
        'compact_nullable_typehint' => true,
        // The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.
        'constant_case' => true,
        // The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.
        'elseif' => true,
        // PHP code MUST use only UTF-8 without BOM (remove BOM).
        'encoding' => true,
        // PHP code must use the long `<?php` tags or short-echo `<?=` tags and not other tag variations.
        'full_opening_tag' => true,
        // Code MUST use configured indentation type.
        'indentation_type' => true,
        // All PHP files must use same line ending.
        'line_ending' => true,
        // Use && and || logical operators instead of and and or.
        'logical_operators' => true,
        // PHP keywords MUST be in lower case.
        'lowercase_keywords' => true,
        // Replaces intval, floatval, doubleval, strval and boolval function calls with according type casting operator.
        'modernize_types_casting' => true,
        // All instances created with new keyword must be followed by braces.
        'new_with_braces' => true,
        // There must be a comment when fall-through is intentional in a non-empty case body.
        'no_break_comment' => true,
        // The closing `? >` tag MUST be omitted from files containing only PHP.
        'no_closing_tag' => true,
        // There must be no space around double colons (also called Scope Resolution Operator or Paamayim Nekudotayim).
        'no_space_around_double_colon' => true,
        // When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
        'no_spaces_after_function_name' => true,
        // There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
        'no_spaces_inside_parenthesis' => true,
        // Remove trailing whitespace at the end of non-blank lines.
        'no_trailing_whitespace' => true,
        // There MUST be no trailing spaces inside comment or PHPDoc.
        'no_trailing_whitespace_in_comment' => true,
        // Adds or removes ? before single type declarations or |null at the end of union types when parameters have a default null value.
        'nullable_type_declaration_for_default_null_value' => true,
        // A PHP file without end tag must always end with a single empty line feed.
        'single_blank_line_at_eof' => true,
        // There MUST NOT be more than one property or constant declared per statement.
        'single_class_element_per_statement' => true,
        // Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.
        'single_line_after_imports' => true,
        // Convert double quotes to single quotes for simple strings.
        'single_quote' => true,
        // A case should be followed by a colon and not a semicolon.
        'switch_case_semicolon_to_colon' => true,
        // Removes extra spaces between colon and case value.
        'switch_case_space' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                'app/code/core/Mage/',
                'errors/',
                'lib/Mage/',
                'lib/Magento/',
                'lib/Varien/',
                'shell/',
                'tests/unit/',
            ])
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
    );
