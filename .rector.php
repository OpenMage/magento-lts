<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector as CodeQuality;
use Rector\DeadCode\Rector as DeadCode;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector as TypeDeclaration;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/dev',
        __DIR__ . '/errors',
        __DIR__ . '/lib',
        __DIR__ . '/pub',
        __DIR__ . '/shell',
        __DIR__ . '/tests',
    ])
    ->withSkipPath(__DIR__ . '/vendor')
    ->withSkip([
        CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class,
        CodeQuality\If_\SimplifyIfReturnBoolRector::class,
        __DIR__ . '/shell/translations.php',
        __DIR__ . '/shell/update-copyright.php.php'
    ])
    ->withRules([
        CodeQuality\BooleanNot\ReplaceMultipleBooleanNotRector::class,
        CodeQuality\Foreach_\UnusedForeachValueToArrayKeysRector::class,
        CodeQuality\FuncCall\ChangeArrayPushToArrayAssignRector::class,
        CodeQuality\FuncCall\CompactToVariablesRector::class,
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
    ])
    ->withPreparedSets(
        false,
        false,
        false,
        false,
        true,
        false,
        false,
        false,
        false,
        false,
        false,
        true,
        false,
        false,
        false,
        false,
        true,
    );