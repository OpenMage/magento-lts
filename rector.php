<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector as CodeQuality;
use Rector\DeadCode\Rector as DeadCode;
use Rector\Config\RectorConfig;

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
        __DIR__ . '/shell/translations.php',
        __DIR__ . '/shell/update-copyright.php.php'
    ])
    ->withRules([
//        CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class, # wait for https://github.com/rectorphp/rector/issues/8781
        CodeQuality\Foreach_\UnusedForeachValueToArrayKeysRector::class,
        CodeQuality\If_\SimplifyIfReturnBoolRector::class,
        DeadCode\ClassMethod\RemoveUselessParamTagRector::class,
        DeadCode\ClassMethod\RemoveUselessReturnTagRector::class,
        DeadCode\Property\RemoveUselessVarTagRector::class,
    ]);
