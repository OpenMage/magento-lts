<?php
declare(strict_types=1);

use Rector\CodeQuality\Rector as CodeQuality;
use Rector\CodingStyle\Rector as CodingStyle;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector as DeadCode;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\Php53\Rector as Php53;
use Rector\Php70\Rector as Php70;
use Rector\Php71\Rector as Php71;
use Rector\Php73\Rector as Php73;
use Rector\Php74\Rector as Php74;
use Rector\Php80\Rector as Php80;
use Rector\TypeDeclaration\Rector as TypeDeclaration;

try {
    return RectorConfig::configure()
        ->withPhpSets(
            php70: true,
        )
        ->withPaths([
            __DIR__,
        ])
        ->withSkipPath(__DIR__ . '/vendor')
        ->withSkip([
            CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class,
            CodeQuality\If_\SimplifyIfReturnBoolRector::class,
            # may conflict with phpstan strict rules
            Php53\Ternary\TernaryToElvisRector::class,
            Php70\If_\IfToSpaceshipRector::class,
            Php70\FuncCall\RandomFunctionRector::class,
            Php70\Ternary\TernaryToNullCoalescingRector::class,
            Php70\Ternary\TernaryToSpaceshipRector::class,
            Php70\Variable\WrapVariableVariableNameInCurlyBracesRector::class,
            Php71\FuncCall\RemoveExtraParametersRector::class,
            Php73\FuncCall\RegexDashEscapeRector::class,
            Php80\Class_\AnnotationToAttributeRector::class,
            Php80\Class_\ClassPropertyAssignToConstructorPromotionRector::class,
            Php80\Class_\StringableForToStringRector::class,
            Php80\FunctionLike\MixedTypeRector::class,
            TypeDeclaration\ClassMethod\ReturnNeverTypeRector::class,
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
