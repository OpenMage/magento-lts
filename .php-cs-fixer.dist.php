<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

use PhpCsFixer\Fixer as PhpCsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/app/Mage.php',
        __DIR__ . '/app/code/core',
        __DIR__ . '/dev',
        __DIR__ . '/errors',
        __DIR__ . '/lib/Mage',
        __DIR__ . '/lib/Magento',
        __DIR__ . '/lib/Varien',
        __DIR__ . '/shell',
        __DIR__ . '/tests',
    ])
    ->withFileExtensions(['php'])
    ->withRootFiles()
    ->withCache(directory: __DIR__ . '/.cache/.ecs.cache')
    ->withPhpCsFixerSets(perCS20: true)
    ->withRules([
        // RISKY: Replaces intval, floatval, doubleval, strval and boolval function calls with according type casting operators
        PhpCsFixer\CastNotation\ModernizeTypesCastingFixer::class,
        // RISKY: Use && and || logical operators instead of and and or
        PhpCsFixer\Operator\LogicalOperatorsFixer::class,
        // There should not be empty PHPDoc blocks
        PhpCsFixer\Phpdoc\NoEmptyPhpdocFixer::class,
        // PHPDoc annotation descriptions should not be a sentence
        PhpCsFixer\Phpdoc\PhpdocAnnotationWithoutDotFixer::class,
        // Docblocks should have the same indentation as the documented subject
        PhpCsFixer\Phpdoc\PhpdocIndentFixer::class,
        // Orders all @param annotations in DocBlocks according to method signature
        PhpCsFixer\Phpdoc\PhpdocParamOrderFixer::class,
        // Single line @var PHPDoc should have proper spacing
        PhpCsFixer\Phpdoc\PhpdocSingleLineVarSpacingFixer::class,
        // PHPDoc should start and end with content, excluding the very first and last line of the docblocks
        PhpCsFixer\Phpdoc\PhpdocTrimFixer::class,
        // Removes extra blank lines after summary and after description in PHPDoc
        PhpCsFixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer::class,
        // @var and @type annotations must have type and name in the correct order
        PhpCsFixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer::class,
        // @var and @type annotations of classy properties should not contain the name
        PhpCsFixer\Phpdoc\PhpdocVarWithoutNameFixer::class,
    ])
    ->withConfiguredRule(
    // PHP84: Adds or removes ? before single type declarations or |null at the end of union types when parameters have a default null value.
        PhpCsFixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer::class,
        ['use_nullable_type_declaration' => true],
    )
    ->withConfiguredRule(
    // Sort union types and intersection types using configured order.
        PhpCsFixer\Operator\OperatorLinebreakFixer::class,
        ['only_booleans' => false, 'position' => 'beginning'],
    )
    ->withConfiguredRule(
    // Operators - when multiline - must always be at the beginning or at the end of the line.
        PhpCsFixer\ClassNotation\OrderedTypesFixer::class,
        ['null_adjustment' => 'always_first', 'sort_algorithm' => 'alpha'],
    )
    ->withConfiguredRule(
    // All items of the given PHPDoc tags must be either left-aligned or (by default) aligned vertically
        PhpCsFixer\Phpdoc\PhpdocAlignFixer::class,
        ['align' => 'vertical'],
    )
    ->withConfiguredRule(
    // Annotations in PHPDoc should be ordered in defined sequence
        PhpCsFixer\Phpdoc\PhpdocOrderFixer::class,
        ['order' => ['param', 'return', 'throws', 'deprecated', 'see', 'SuppressWarnings', 'phpstan-ignore']],
    )
    ->withConfiguredRule(
    // Annotations in PHPDoc should be ordered in defined sequence
        PhpCsFixer\Phpdoc\PhpdocOrderByValueFixer::class,
        ['annotations' => ['author', 'covers', 'group', 'method', 'throws', 'uses']],
    )
    ->withConfiguredRule(
    // Fixes casing of PHPDoc tags
        PhpCsFixer\Phpdoc\PhpdocScalarFixer::class,
        ['types' => ['boolean', 'callback', 'double', 'integer', 'real', 'str']],
    )
    ->withConfiguredRule(
    // Fixes casing of PHPDoc tags
        PhpCsFixer\Phpdoc\PhpdocTagCasingFixer::class,
        ['tags' => ['inheritDoc']],
    )
    ->withConfiguredRule(
    // Sorts PHPDoc types
        PhpCsFixer\Phpdoc\PhpdocTypesOrderFixer::class,
        ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_first'],
    )
    ->withConfiguredRule(
    // Calls to PHPUnit\Framework\TestCase static methods must all be of the same type, either $this->, self:: or static::
        PhpCsFixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer::class,
        ['call_type' => 'self'],
    )
    ->withConfiguredRule(
    // There MUST NOT be more than one property or constant declared per statement.
        PhpCsFixer\ClassNotation\SingleClassElementPerStatementFixer::class,
        ['elements' =>  ['const', 'property']],
    )
    ->withConfiguredRule(
    // Convert double quotes to single quotes for simple strings.
        PhpCsFixer\StringNotation\SingleQuoteFixer::class,
        ['strings_containing_single_quote_chars' => false],
    )
    ->withConfiguredRule(
    // Arguments lists, array destructuring lists, arrays that are multi-line, match-lines and parameters lists must have a trailing comma.
    // removed "match" and "parameters" for PHP7
    // see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/8308
        PhpCsFixer\ControlStructure\TrailingCommaInMultilineFixer::class,
        ['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays']],
    )
    ->withConfiguredRule(
    // A single space or none should be around union type and intersection type operators
        PhpCsFixer\Whitespace\TypesSpacesFixer::class,
        ['space' => 'none'],
    );
