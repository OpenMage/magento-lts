<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector as CodeQuality;
use Rector\DeadCode\Rector as DeadCode;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector as TypeDeclaration;

return RectorConfig::configure()
    ->withPaths([
        __DIR__,
    ])
    ->withSkipPath(__DIR__ . '/vendor')
    ->withSkip([
        CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class,
        CodeQuality\If_\SimplifyIfReturnBoolRector::class,
        __DIR__ . '/shell/translations.php',
        __DIR__ . '/shell/update-copyright.php.php',
        __DIR__ . '/tests/unit/Mage/Reports/Model/Resource/Report/CollectionTest.php',
    ])
    ->withSets([
        PHPUnitSetList::PHPUNIT_90,
        SetList::PRIVATIZATION,
        SetList::PHP_52,
    ])
    ->withRules([
        CodeQuality\BooleanNot\ReplaceMultipleBooleanNotRector::class,
        CodeQuality\Foreach_\UnusedForeachValueToArrayKeysRector::class,
        CodeQuality\FuncCall\ChangeArrayPushToArrayAssignRector::class,
        CodeQuality\FuncCall\CompactToVariablesRector::class,
        CodeQuality\FunctionLike\SimplifyUselessVariableRector::class,
        CodeQuality\Identical\SimplifyArraySearchRector::class,
        CodeQuality\Identical\SimplifyConditionsRector::class,
        CodeQuality\Identical\StrlenZeroToIdenticalEmptyStringRector::class,
        CodeQuality\NotEqual\CommonNotEqualRector::class,
        CodeQuality\LogicalAnd\LogicalToBooleanRector::class,
        CodeQuality\Ternary\SimplifyTautologyTernaryRector::class,
        DeadCode\ClassMethod\RemoveUselessParamTagRector::class,
        DeadCode\ClassMethod\RemoveUselessReturnTagRector::class,
        DeadCode\Property\RemoveUselessVarTagRector::class,
        TypeDeclaration\ClassMethod\ReturnNeverTypeRector::class,
    ]);
