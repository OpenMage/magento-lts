<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
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
        SimplifyIfReturnBoolRector::class
    ]);
