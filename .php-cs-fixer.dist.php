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
        // RISKY: Replaces intval, floatval, doubleval, strval and boolval function calls with according type casting operator.
        'modernize_types_casting' => true,
        // PHP84: Adds or removes ? before single type declarations or |null at the end of union types when parameters have a default null value.
        'nullable_type_declaration_for_default_null_value' => true,
        // Convert double quotes to single quotes for simple strings.
        'single_quote' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__,
            ])
            ->exclude([
                'lib/3Dsecure/',
                'lib/LinLibertineFont/',
                'lib/Unserialize/',
            ])
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
    );
