<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector as CodeQuality;
use Rector\CodingStyle\Rector as CodingStyle;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector as DeadCode;
use Rector\EarlyReturn\Rector as EarlyReturn;
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
use Rector\Privatization\Rector as Privatization;
use Rector\Renaming\Rector as Renaming;
use Rector\Strict\Rector as Strict;
use Rector\Transform\Rector as Transform;
use Rector\TypeDeclaration\Rector as TypeDeclaration;

try {
    return RectorConfig::configure()
        ->withFileExtensions(['php', 'phtml'])
        ->withCache(
            cacheDirectory: '.rector.result.cache',
            cacheClass: FileCacheStorage::class,
        )
        ->withPhpSets(
            php81: true,
        )
        ->withPaths([
            __DIR__,
        ])
        ->withSkipPath(__DIR__ . '/vendor')
        ->withSkip([
            CodeQuality\Assign\CombinedAssignRector::class, # todo: TMP
            CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class,
            CodeQuality\Catch_\ThrowWithPreviousExceptionRector::class, # todo: TMP
            CodeQuality\Class_\CompleteDynamicPropertiesRector::class, # todo: TMP (!?!)
            CodeQuality\Class_\InlineConstructorDefaultToPropertyRector::class, # todo: TMP
            CodeQuality\ClassMethod\ExplicitReturnNullRector::class, # todo: TMP
            CodeQuality\Concat\JoinStringConcatRector::class, # todo: TMP
            CodeQuality\Empty_\SimplifyEmptyCheckOnEmptyArrayRector::class, # todo: TMP
            CodeQuality\Equal\UseIdenticalOverEqualWithSameTypeRector::class, # todo: TMP
            CodeQuality\Expression\InlineIfToExplicitIfRector::class, # todo: TMP (!?!)
            CodeQuality\Expression\TernaryFalseExpressionToIfRector::class, # todo: TMP (!?!)
            CodeQuality\For_\ForRepeatedCountToOwnVariableRector::class, # todo: TMP
            CodeQuality\Foreach_\ForeachItemsAssignToEmptyArrayToAssignRector::class, # todo: TMP
            CodeQuality\FuncCall\SimplifyRegexPatternRector::class, # todo: TMP
            CodeQuality\FunctionLike\SimplifyUselessVariableRector::class, # todo: TMP
            CodeQuality\Identical\SimplifyBoolIdenticalTrueRector::class, # todo: TMP
            CodeQuality\Identical\SimplifyConditionsRector::class, # todo: TMP
            CodeQuality\If_\CombineIfRector::class, # todo: TMP<
            CodeQuality\If_\CompleteMissingIfElseBracketRector::class, # todo: TMP  (!?!)
            CodeQuality\If_\ExplicitBoolCompareRector::class, # todo: TMP
            CodeQuality\If_\SimplifyIfElseToTernaryRector::class,
            CodeQuality\If_\SimplifyIfReturnBoolRector::class,
            CodeQuality\Include_\AbsolutizeRequireAndIncludePathRector::class, # todo: TMP
            CodeQuality\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class, # todo: TMP
            CodeQuality\Switch_\SingularSwitchToIfRector::class, # todo: TMP
            CodeQuality\Ternary\NumberCompareToMaxFuncCallRector::class, # todo: TMP
            CodeQuality\Ternary\SwitchNegatedTernaryRector::class, # todo: TMP
            CodeQuality\Ternary\TernaryEmptyArrayArrayDimFetchToCoalesceRector::class, # todo: TMP
            CodeQuality\Ternary\UnnecessaryTernaryExpressionRector::class, # todo: TMP
            CodingStyle\Assign\SplitDoubleAssignRector::class, # todo: TMP
            CodingStyle\Catch_\CatchExceptionNameMatchingTypeRector::class, # todo: TMP
            CodingStyle\ClassMethod\FuncGetArgsToVariadicParamRector::class, # todo: TMP
            CodingStyle\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector::class, # todo: TMP
            CodingStyle\ClassMethod\NewlineBeforeNewAssignSetRector::class, # todo: TMP
            CodingStyle\Encapsed\EncapsedStringsToSprintfRector::class, # todo: TMP
            CodingStyle\Encapsed\WrapEncapsedVariableInCurlyBracesRector::class, # todo: TMP
            CodingStyle\FuncCall\CallUserFuncArrayToVariadicRector::class, # todo: TMP
            CodingStyle\FuncCall\CountArrayToEmptyArrayComparisonRector::class, # todo: TMP
            CodingStyle\FuncCall\StrictArraySearchRector::class, # todo: TMP
            CodingStyle\If_\NullableCompareToNullRector::class, # todo: TMP
            CodingStyle\PostInc\PostIncDecToPreIncDecRector::class, # todo: TMP
            CodingStyle\Stmt\NewlineAfterStatementRector::class, # todo: TMP
            CodingStyle\String_\SymplifyQuoteEscapeRector::class, # todo: TMP
            DeadCode\Assign\RemoveUnusedVariableAssignRector::class, # todo: TMP
            DeadCode\Cast\RecastingRemovalRector::class, # todo: TMP  (!?!)
            DeadCode\ClassMethod\RemoveUnusedConstructorParamRector::class, # todo: TMP (!?!)
            DeadCode\ClassMethod\RemoveEmptyClassMethodRector::class, # todo: TMP
            DeadCode\ClassMethod\RemoveNullTagValueNodeRector::class, # todo: TMP
            DeadCode\ClassMethod\RemoveUnusedPrivateMethodParameterRector::class, # todo: TMP  (!?!)
            DeadCode\Concat\RemoveConcatAutocastRector::class, # todo: TMP  (!?!)
            DeadCode\Foreach_\RemoveUnusedForeachKeyRector::class, # todo: TMP
            DeadCode\FunctionLike\RemoveDeadReturnRector::class, # todo: TMP
            DeadCode\If_\ReduceAlwaysFalseIfOrRector::class, # todo: TMP
            DeadCode\If_\RemoveAlwaysTrueIfConditionRector::class, # todo: TMP
            DeadCode\If_\RemoveDeadInstanceOfRector::class, # todo: TMP
            DeadCode\If_\RemoveUnusedNonEmptyArrayBeforeForeachRector::class, # todo: TMP
            DeadCode\If_\SimplifyIfElseWithSameContentRector::class, # todo: TMP
            DeadCode\Node\RemoveNonExistingVarAnnotationRector::class, # todo: TMP  (!?!)
            DeadCode\Plus\RemoveDeadZeroAndOneOperationRector::class, # todo: TMP  (!?!)
            DeadCode\Property\RemoveUnusedPrivatePropertyRector::class, # todo: TMP
            DeadCode\PropertyProperty\RemoveNullPropertyInitializationRector::class, # todo: TMP
            DeadCode\Switch_\RemoveDuplicatedCaseInSwitchRector::class, # todo: TMP  (!?!)
            DeadCode\Ternary\TernaryToBooleanOrFalseToBooleanAndRector::class, # todo: TMP
            DeadCode\TryCatch\RemoveDeadTryCatchRector::class, # todo: TMP  (!?!)
            EarlyReturn\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector::class, # todo: TMP
            EarlyReturn\If_\ChangeIfElseValueAssignToEarlyReturnRector::class, # todo: TMP
            EarlyReturn\If_\ChangeNestedIfsToEarlyReturnRector::class, # todo: TMP
            EarlyReturn\If_\ChangeOrIfContinueToMultiContinueRector::class, # todo: TMP
            EarlyReturn\If_\RemoveAlwaysElseRector::class, # todo: TMP
            EarlyReturn\Return_\ReturnBinaryOrToEarlyReturnRector::class, # todo: TMP
            EarlyReturn\Return_\PreparedValueToEarlyReturnRector::class, # todo: TMP
            EarlyReturn\StmtsAwareInterface\ReturnEarlyIfVariableRector::class, # todo: TMP
            # skip: may conflict with phpstan strict rules
            Php53\Ternary\TernaryToElvisRector::class,
            Php71\FuncCall\RemoveExtraParametersRector::class, # todo: check later
            # skip: causes syntax error in Varien_Db_Adapter_Pdo_Mysql
            Php73\FuncCall\RegexDashEscapeRector::class,
            # skip: causes issues with some tests
            Php74\Closure\ClosureToArrowFunctionRector::class,
            # skip: causes issues
            Php74\Assign\NullCoalescingOperatorRector::class,
            Php80\Catch_\RemoveUnusedVariableInCatchRector::class, # todo: TMP
            Php80\Class_\AnnotationToAttributeRector::class, # todo: wait for php80
            Php80\Class_\ClassPropertyAssignToConstructorPromotionRector::class, # todo: wait for php80
            Php80\Class_\StringableForToStringRector::class, # todo: wait for php80
            Php80\ClassConstFetch\ClassOnThisVariableObjectRector::class, # todo: TMP
            Php80\ClassMethod\AddParamBasedOnParentClassMethodRector::class, # todo: TMP
            Php80\FuncCall\ClassOnObjectRector::class, # todo: TMP
            Php81\Array_\FirstClassCallableRector::class, # todo: TMP
            Php81\FuncCall\NullToStrictStringFuncCallArgRector::class, # todo: check later
            Strict\Empty_\DisallowedEmptyRuleFixerRector::class, # todo: TMP
            TypeDeclaration\BooleanAnd\BinaryOpNullableToInstanceofRector::class, # todo: TMP
            TypeDeclaration\ClassMethod\ReturnNeverTypeRector::class,
            # skip: cannot be applied to OpenMage codebase - yet
            TypeDeclaration\StmtsAwareInterface\DeclareStrictTypesRector::class,
            # skip: use static methods
            PreferPHPUnitThisCallRector::class,
            __DIR__ . '/shell/translations.php',
            __DIR__ . '/shell/update-copyright.php',
            __DIR__ . '/tests/unit/Mage/Reports/Model/Resource/Report/CollectionTest.php',
        ])
        ->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            codingStyle: true,
            typeDeclarations: false,
            privatization: true,
            naming: false,
            instanceOf: true,
            earlyReturn: true,
            strictBooleans: false,
            carbon: false,
            rectorPreset: true,
            phpunitCodeQuality: true,
            doctrineCodeQuality: false,
            symfonyCodeQuality: false,
            symfonyConfigs: false,
        );
} catch (InvalidConfigurationException $exception) {
    echo $exception->getMessage();
    exit(1);
}
