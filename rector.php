<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector as CodeQuality;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/dev',
        __DIR__ . '/errors',
        __DIR__ . '/lib',
        __DIR__ . '/pub',
        __DIR__ . '/shell',
    ])
    ->withSkipPath(__DIR__ . '/vendor')
    ->withRules([
        CodeQuality\If_\SimplifyIfReturnBoolRector::class,
//        CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class, # wait for https://github.com/rectorphp/rector/issues/8781
    ]);
