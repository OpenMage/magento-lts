<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector as CodeQuality;
use Rector\CodingStyle\Rector as CodingStyle;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector as DeadCode;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\Php53\Rector as Php53;
use Rector\Php71\Rector as Php71;
use Rector\Php73\Rector as Php73;
use Rector\Php74\Rector as Php74;
use Rector\Php80\Rector as Php80;
use Rector\Php81\Rector as Php81;
use Rector\Php82\Rector as Php82;
use Rector\Php83\Rector as Php83;
use Rector\Php84\Rector as Php84;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\TypeDeclaration\Rector as TypeDeclaration;

try {
    return RectorConfig::configure()
        ->withFileExtensions(['php', 'phtml'])
        ->withCache(
            cacheDirectory: '.rector.result.cache',
            cacheClass: FileCacheStorage::class,
        )
        ->withPhpSets(
            php74: true,
        )
        ->withPaths([
            __DIR__,
        ])
        ->withSkipPath(__DIR__ . '/vendor')
        ->withSkip([
            CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class,
            CodeQuality\If_\SimplifyIfReturnBoolRector::class,
            # skip: may conflict with phpstan strict rules
            Php53\Ternary\TernaryToElvisRector::class,
            Php71\FuncCall\RemoveExtraParametersRector::class, # todo: check later
            # skip: causes syntax error in Varien_Db_Adapter_Pdo_Mysql
            Php73\FuncCall\RegexDashEscapeRector::class,
            # skip: causes issues with some tests
            Php74\Closure\ClosureToArrowFunctionRector::class,
            # skip: causes issues
            Php74\Assign\NullCoalescingOperatorRector::class,
            Php80\Class_\AnnotationToAttributeRector::class, # todo: wait for php80
            Php80\Class_\ClassPropertyAssignToConstructorPromotionRector::class, # todo: wait for php80
            Php80\Class_\StringableForToStringRector::class, # todo: wait for php80
            TypeDeclaration\ClassMethod\ReturnNeverTypeRector::class,
            # use static methods
            PreferPHPUnitThisCallRector::class,
            __DIR__ . '/shell/translations.php',
            __DIR__ . '/shell/update-copyright.php',
            __DIR__ . '/tests/unit/Mage/Reports/Model/Resource/Report/CollectionTest.php',
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
            CodingStyle\FuncCall\ConsistentImplodeRector::class,
            DeadCode\ClassMethod\RemoveUselessParamTagRector::class,
            DeadCode\ClassMethod\RemoveUselessReturnTagRector::class,
            DeadCode\Property\RemoveUselessVarTagRector::class,
            DeadCode\StaticCall\RemoveParentCallWithoutParentRector::class,
        ])
        ->withPreparedSets(
            deadCode: false,
            codeQuality: false,
            codingStyle: false,
            typeDeclarations: false,
            privatization: true,
            naming: false,
            instanceOf: false,
            earlyReturn: false,
            strictBooleans: false,
            carbon: false,
            rectorPreset: false,
            phpunitCodeQuality: true,
            doctrineCodeQuality: false,
            symfonyCodeQuality: false,
            symfonyConfigs: false,
        );
} catch (InvalidConfigurationException $exception) {
    echo $exception->getMessage();
    exit(1);
}
