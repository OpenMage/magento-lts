<?php

declare(strict_types=1);

use OpenMage\Rector\Migration;
use Rector\Arguments\Rector\ClassMethod\ReplaceArgumentDefaultValueRector;
use Rector\Carbon\Rector as Carbon;
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
use Rector\Php85\Rector as Php85;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Privatization\Rector as Privatization;
use Rector\Renaming\Rector as Renaming;
use Rector\Strict\Rector as Strict;
use Rector\Transform\Rector as Transform;
use Rector\TypeDeclaration\Rector as TypeDeclaration;
use Rector\TypeDeclarationDocblocks\Rector as TypeDeclarationDocblocks;

try {
    return RectorConfig::configure()
        ->withFileExtensions(['php', 'phtml'])
        ->withCache(
            cacheDirectory: '.rector.result.cache',
            cacheClass: FileCacheStorage::class,
        )
        ->withImportNames(removeUnusedImports: true)
        ->withPhpSets(
            php81: true,
        )
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
            carbon: true,
            rectorPreset: true,
            phpunitCodeQuality: true,
            doctrineCodeQuality: false,
            symfonyCodeQuality: false,
            symfonyConfigs: false,
        )
        ->withPaths([
            __DIR__,
        ])
        ->withSkipPath(__DIR__ . '/vendor')
        ->withRules([
            Php85\ArrayDimFetch\ArrayFirstLastRector::class,
            TypeDeclarationDocblocks\Class_\AddVarArrayDocblockFromDimFetchAssignRector::class,
            TypeDeclarationDocblocks\Class_\AddReturnArrayDocblockFromDataProviderParamRector::class,
            TypeDeclarationDocblocks\Class_\ClassMethodArrayDocblockParamFromLocalCallsRector::class,
            TypeDeclarationDocblocks\Class_\DocblockVarArrayFromGetterReturnRector::class,
            TypeDeclarationDocblocks\Class_\DocblockVarArrayFromPropertyDefaultsRector::class,
            TypeDeclarationDocblocks\Class_\DocblockVarFromParamDocblockInConstructorRector::class,
            TypeDeclarationDocblocks\ClassMethod\AddParamArrayDocblockFromAssignsParamToParamReferenceRector::class,
            TypeDeclarationDocblocks\ClassMethod\AddParamArrayDocblockFromDataProviderRector::class,
            TypeDeclarationDocblocks\ClassMethod\AddReturnDocblockForArrayDimAssignedObjectRector::class,
            TypeDeclarationDocblocks\ClassMethod\AddReturnDocblockForCommonObjectDenominatorRector::class,
            TypeDeclarationDocblocks\ClassMethod\AddReturnDocblockForDimFetchArrayFromAssignsRector::class,
            TypeDeclarationDocblocks\ClassMethod\AddReturnDocblockForJsonArrayRector::class,
            TypeDeclarationDocblocks\ClassMethod\AddReturnDocblockFromMethodCallDocblockRector::class,
            TypeDeclarationDocblocks\ClassMethod\DocblockReturnArrayFromDirectArrayInstanceRector::class,
        ])
        ->withConfiguredRule(Renaming\ClassConstFetch\RenameClassConstFetchRector::class, Migration\Zend\Measure::renameClassConst())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Admin::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Adminhtml::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Bundle::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Catalog::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\CatalogSearch::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\ConfigurableSwatches::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Checkout::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Core::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Eav::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Paypal::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Shipping::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Sitemap::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Tag::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Tax::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Usa::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Mage\Wishlist::renameMethod())
        ->withConfiguredRule(ReplaceArgumentDefaultValueRector::class, Migration\Mage\Adminhtml::replaceArgumentDefaultValue())
        ->withSkip([
            Carbon\FuncCall\DateFuncCallToCarbonRector::class => [
                __DIR__ . '/tests/unit/Base/CarbonTest.php',
            ],
            Carbon\FuncCall\TimeFuncCallToCarbonRector::class => [
                __DIR__ . '/tests/unit/Base/CarbonTest.php',
            ],
            CodeQuality\BooleanNot\SimplifyDeMorganBinaryRector::class,
            # skip: causes issues with Mage_Api2_Model_Auth_Adapter_Oauth::getUserParams()
            CodeQuality\Catch_\ThrowWithPreviousExceptionRector::class => [
                __DIR__ . '/app/code/core/Mage/Api2/Model/Auth/Adapter/Oauth.php',
            ],
            CodeQuality\Class_\CompleteDynamicPropertiesRector::class, # todo: TMP (!?!)
            CodeQuality\ClassMethod\ExplicitReturnNullRector::class, # todo: TMP
            CodeQuality\Empty_\SimplifyEmptyCheckOnEmptyArrayRector::class, # todo: TMP
            CodeQuality\Equal\UseIdenticalOverEqualWithSameTypeRector::class, # todo: TMP
            CodeQuality\Expression\TernaryFalseExpressionToIfRector::class, # todo: TMP (!?!)
            CodeQuality\Identical\SimplifyBoolIdenticalTrueRector::class, # todo: TMP
            CodeQuality\If_\CombineIfRector::class, # todo: TMP<
            CodeQuality\If_\CompleteMissingIfElseBracketRector::class, # todo: TMP  (!?!)
            CodeQuality\If_\ExplicitBoolCompareRector::class, # todo: TMP
            CodeQuality\If_\SimplifyIfElseToTernaryRector::class,
            CodeQuality\If_\SimplifyIfReturnBoolRector::class,
            CodeQuality\Include_\AbsolutizeRequireAndIncludePathRector::class, # todo: TMP
            CodeQuality\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class, # todo: TMP
            CodeQuality\Ternary\TernaryEmptyArrayArrayDimFetchToCoalesceRector::class, # todo: TMP
            CodingStyle\ClassMethod\FuncGetArgsToVariadicParamRector::class, # todo: TMP
            CodingStyle\Encapsed\EncapsedStringsToSprintfRector::class, # todo: TMP
            CodingStyle\Encapsed\WrapEncapsedVariableInCurlyBracesRector::class, # todo: TMP
            CodingStyle\FuncCall\StrictArraySearchRector::class, # todo: TMP
            CodingStyle\If_\NullableCompareToNullRector::class, # todo: TMP
            CodingStyle\PostInc\PostIncDecToPreIncDecRector::class, # todo: TMP
            DeadCode\Assign\RemoveUnusedVariableAssignRector::class, # todo: TMP
            DeadCode\Cast\RecastingRemovalRector::class, # todo: TMP  (!?!)
            DeadCode\ClassMethod\RemoveUnusedConstructorParamRector::class, # todo: TMP (!?!)
            DeadCode\If_\RemoveAlwaysTrueIfConditionRector::class, # todo: TMP
            DeadCode\MethodCall\RemoveNullArgOnNullDefaultParamRector::class, # todo: TMP
            DeadCode\Plus\RemoveDeadZeroAndOneOperationRector::class, # todo: TMP  (!?!)
            DeadCode\PropertyProperty\RemoveNullPropertyInitializationRector::class, # todo: TMP
            DeadCode\Ternary\TernaryToBooleanOrFalseToBooleanAndRector::class, # todo: TMP
            DeadCode\TryCatch\RemoveDeadTryCatchRector::class, # todo: TMP  (!?!)
            EarlyReturn\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector::class, # todo: TMP
            EarlyReturn\If_\ChangeNestedIfsToEarlyReturnRector::class, # todo: TMP
            EarlyReturn\If_\ChangeOrIfContinueToMultiContinueRector::class, # todo: TMP
            EarlyReturn\Return_\ReturnBinaryOrToEarlyReturnRector::class, # todo: TMP
            EarlyReturn\Return_\PreparedValueToEarlyReturnRector::class, # todo: TMP
            # skip: may conflict with phpstan strict rules
            Php53\Ternary\TernaryToElvisRector::class,
            Php71\FuncCall\RemoveExtraParametersRector::class, # todo: check later
            # skip: causes issues with some tests
            Php74\Closure\ClosureToArrowFunctionRector::class,
            # skip: causes issues
            Php74\Assign\NullCoalescingOperatorRector::class,
            Php80\Class_\ClassPropertyAssignToConstructorPromotionRector::class, # todo: wait for php80
            Php80\Class_\StringableForToStringRector::class, # todo: wait for php80
            # see https://github.com/OpenMage/magento-lts/pull/5040
            Php80\ClassMethod\AddParamBasedOnParentClassMethodRector::class => [
                __DIR__ . '/lib/Varien/Directory/Collection.php',
            ],
            Php81\Array_\ArrayToFirstClassCallableRector::class, # todo: TMP
            Php81\FuncCall\NullToStrictStringFuncCallArgRector::class, # todo: check later
            Strict\Empty_\DisallowedEmptyRuleFixerRector::class, # todo: TMP
            TypeDeclaration\BooleanAnd\BinaryOpNullableToInstanceofRector::class, # todo: TMP
            TypeDeclaration\ClassMethod\ReturnNeverTypeRector::class,
            # skip: cannot be applied to OpenMage codebase - yet
            TypeDeclaration\StmtsAwareInterface\DeclareStrictTypesRector::class,
            # skip: use static methods
            PreferPHPUnitThisCallRector::class,
            __DIR__ . '/shell/translations.php',
            __DIR__ . '/tests/unit/Mage/Reports/Model/Resource/Report/CollectionTest.php',
        ]);
} catch (InvalidConfigurationException $exception) {
    echo $exception->getMessage();
    exit(1);
}
