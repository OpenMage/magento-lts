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

try {
    return RectorConfig::configure()
        ->withFileExtensions(['php', 'phtml'])
        ->withCache(
            cacheDirectory: __DIR__ . '/.cache/.rector.result.cache',
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
            Php83\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector::class,
            Php85\ArrayDimFetch\ArrayFirstLastRector::class,
        ])
        ->withRules(Migration\TypeDeclarationDocblocks::getRules())
        ->withConfiguredRule(Renaming\ClassConstFetch\RenameClassConstFetchRector::class, Migration\Zend\Log::renameClassConst())
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
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Zend\Acl::renameMethod())
        ->withConfiguredRule(Renaming\MethodCall\RenameMethodRector::class, Migration\Zend\Captcha::renameMethod())
        ->withConfiguredRule(ReplaceArgumentDefaultValueRector::class, Migration\Mage\Adminhtml::replaceArgumentDefaultValue())
        # skip: do not apply
        ->withSkip([
            # skip avoid renaming of methods in tests
            Carbon\FuncCall\DateFuncCallToCarbonRector::class => [
                __DIR__ . '/tests/unit/Base/CarbonTest.php',
            ],
            # skip avoid renaming of methods in tests
            Carbon\FuncCall\TimeFuncCallToCarbonRector::class => [
                __DIR__ . '/tests/unit/Base/CarbonTest.php',
            ],
            # skip adding dynamic property to class ... not sure about
            CodeQuality\Class_\CompleteDynamicPropertiesRector::class => [
                __DIR__ . '/lib/3Dsecure/XMLParser.php',
            ],
            # skip classes that throw an exception as a return value, which is not supported by Rector yet
            # see https://github.com/rectorphp/rector/issues/9719
            CodeQuality\ClassMethod\ExplicitReturnNullRector::class => [
                __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Type/Default.php',
                __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
                __DIR__ . '/app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
                __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export.php',
                __DIR__ . '/app/code/core/Mage/Oauth/Model/Token.php',
                __DIR__ . '/app/code/core/Mage/Paygate/Model/Authorizenet.php',
                __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Payment.php',
                __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract/Backend/Abstract.php',
            ],
            # changes method signature
            CodingStyle\ClassMethod\FuncGetArgsToVariadicParamRector::class,
            # skip: conflicts with phpstan strict rules
            Php53\Ternary\TernaryToElvisRector::class,
            # skip: changes method signature
            Php80\Class_\ClassPropertyAssignToConstructorPromotionRector::class,
            # skip: changes method signature
            TypeDeclaration\ClassMethod\ReturnNeverTypeRector::class,
            # skip: strict_type cannot be applied to OpenMage codebase - yet
            TypeDeclaration\StmtsAwareInterface\DeclareStrictTypesRector::class,
        ])
        # skip: ... @todo: check later
        ->withSkip([
            # ... causes issues with Mage_Api2_Model_Auth_Adapter_Oauth::getUserParams()
            CodeQuality\Catch_\ThrowWithPreviousExceptionRector::class => [
                __DIR__ . '/app/code/core/Mage/Api2/Model/Auth/Adapter/Oauth.php',
            ],
            # ... +300 occurrences
            CodeQuality\Equal\UseIdenticalOverEqualWithSameTypeRector::class,
            # ... +300 occurrences
            CodeQuality\If_\ExplicitBoolCompareRector::class,
            # ... review autoloading at all
            CodeQuality\Include_\AbsolutizeRequireAndIncludePathRector::class,
            # ... breaks loading website
            CodeQuality\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class,
            # ... +250 occurrences
            CodingStyle\Encapsed\EncapsedStringsToSprintfRector::class,
            # --- wait for phpstan strict rules
            CodingStyle\FuncCall\StrictArraySearchRector::class,
            # ... +200 occurrences, need closer review
            CodingStyle\PostInc\PostIncDecToPreIncDecRector::class,
            # ... needs closer review
            DeadCode\ClassMethod\RemoveUnusedConstructorParamRector::class,
            # ... messes up code
            DeadCode\If_\RemoveAlwaysTrueIfConditionRector::class => [
                __DIR__ . '/app/design/adminhtml/base/default/template/system/store/tree.phtml',
            ],
            # ... +400 occurrences, needs closer review
            DeadCode\PropertyProperty\RemoveNullPropertyInitializationRector::class,
            # ... needs closer review
            DeadCode\TryCatch\RemoveDeadTryCatchRector::class,
            # ... check again https://github.com/rectorphp/rector/issues/9732
            EarlyReturn\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector::class => [
                __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Payment/Api.php',
            ],
            # ... needs closer review and docblock fixes for magic methods
            Php71\FuncCall\RemoveExtraParametersRector::class,
            # ... needs closer review
            Php74\Closure\ClosureToArrowFunctionRector::class,
            # ... needs closer review
            Php80\ClassMethod\AddParamBasedOnParentClassMethodRector::class => [
                __DIR__ . '/lib/Varien/Directory/Collection.php',
            ],
            # ... +300 occurrences
            Php81\FuncCall\NullToStrictStringFuncCallArgRector::class,
            # ... ~100 occurrences
            Strict\Empty_\DisallowedEmptyRuleFixerRector::class,
            # ... needs closer review
            TypeDeclaration\BooleanAnd\BinaryOpNullableToInstanceofRector::class,
        ])
        # wait for rector support
        ->withSkip([
            # https://github.com/rectorphp/rector/issues/9743
            Php81\Array_\ArrayToFirstClassCallableRector::class => [
                __DIR__ . '/app/code/core/Mage/Paypal/Model/Api/Abstract.php',
                __DIR__ . '/app/code/core/Mage/Paypal/Model/Info.php',
            ],
        ])
        ->withSkip([
            # skip: use static methods
            PreferPHPUnitThisCallRector::class,
            __DIR__ . '/shell/translations.php',
            __DIR__ . '/tests/unit/Mage/Reports/Model/Resource/Report/CollectionTest.php',
        ]);
} catch (InvalidConfigurationException $exception) {
    echo $exception->getMessage();
    exit(1);
}
