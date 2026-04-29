---
name: phpunit-openmage-tests
description: PHPUnit testing for OpenMage — OpenMageTest base, final classes + #[Override], Subject alias, public static data-provider traits, runInSeparateProcess for admin tests, getMockWithCalledMethods helper, per-module test suites. Use when writing or running tests under tests/unit/, picking the right base class, working with data providers, or running per-module phpunit suites.
---

# PHPUnit / OpenMage tests

PHPUnit 9.6 against `tests/unit/`, namespace `OpenMage\Tests\Unit\<MirrorOfSource>`. Tree mirrors `app/code/core/Mage/`. Bootstrap `tests/bootstrap.php` requires `app/Mage.php` + `errors/processor.php`.

## Base class

Every new test extends `OpenMage\Tests\Unit\OpenMageTest` (`tests/unit/OpenMageTest.php`) — never raw `TestCase`. The base calls `Mage::app()` in `setUpBeforeClass`, so the application is booted for the whole class.

```php
abstract class OpenMageTest extends TestCase
{
    public const WILL_RETURN_SELF = '__willReturnSelf__';
    public static function setUpBeforeClass(): void { parent::setUpBeforeClass(); Mage::app(); }
    public function getMockWithCalledMethods(string $class, array $methods, ?bool $expectOnce = false): MockObject;
}
```

## Canonical skeleton

```php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Override;
use Mage;
use Mage_Catalog_Model_Product as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\BoolTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\CatalogTrait;

final class ProductTest extends OpenMageTest
{
    use BoolTrait;
    use CatalogTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/product');
    }

    /** @group Model */
    public function testValidate(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->validate());
    }
}
```

Hard rules:

- `final class` — Rector enforces.
- `#[Override]` on every overridden method (`setUpBeforeClass`, `setUp`, `tearDown`, …) — `AddOverrideAttributeToOverriddenMethodsRector` will add it on `composer run rector:fix` if you don't.
- `declare(strict_types=1);` and `@package OpenMage_Tests` in the file docblock.
- Native return types on every method, including `: void`.
- `@group Model` / `@group Block` / `@group Helper` etc. on each test method.

## Subject alias convention

`use Mage_Foo_Helper_Data as Subject;`, then either:

- `Subject::method(...)` for static helpers, or
- `private static Subject $subject;` populated in `setUpBeforeClass()` for stateful instances.

This keeps assertions short and lets the test class be retargeted with a single `use … as Subject;` line.

## Data-provider traits

Shared providers live in traits under `tests/unit/Traits/DataProvider/<Mirror>/...`. Tests `use FooTrait;` inside the class.

```php
namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core;

use Generator;

trait CoreTrait
{
    public static function provideGetStoreId(): Generator
    {
        yield 'string' => [1, '1'];
        yield 'int'    => [1, 1];
        yield 'null'   => [null, null];
    }
}
```

PHPUnit 10 / `dynamicCallOnStaticMethod` rules:

- Every provider is `public static function`. Returning `Generator` (yield) or `array` is fine.
- Inside a provider, never use `$this->` — use `static::` (so subclasses/host classes can override) or `self::`.
- If you make a helper `static` to call it from a provider, update **every** caller — including non-static test methods — from `$this->helper()` to `self::helper()`. PHPStan will flag `staticMethod.dynamicCall` otherwise.

Wire to a test method via `@dataProvider provideGetStoreId`.

## runInSeparateProcess

Tests that touch admin globals, the autoloader, `Mage::register`/`Mage::unregister` state, or anything that mutates singletons need PHPUnit's process isolation:

```php
/**
 * @group Block
 * @group runInSeparateProcess
 * @runInSeparateProcess
 */
public function testGetFlushStorageUrl(): void { ... }
```

Both the `@runInSeparateProcess` annotation and the matching `@group` (so the suite can be filtered) are conventional. Most `Mage_Adminhtml` block tests are isolated this way — see `tests/unit/Mage/Adminhtml/Block/CacheTest.php`.

## Mocking helper

`getMockWithCalledMethods()` on the base class collapses the common `getMockBuilder()->setMethods()->method()->willReturn()` chain:

```php
public function getMockWithCalledMethods(string $class, array $methods, ?bool $expectOnce = false): MockObject;
```

- `$methods` is `['methodName' => $returnValue, …]`.
- `$expectOnce = true` wraps each in `expects(self::once())`.
- `$expectOnce = null` skips wiring entirely (returns the bare mock).
- Use the `OpenMageTest::WILL_RETURN_SELF` sentinel instead of a return value to get `willReturnSelf()` (for fluent APIs like collections / `Varien_Object` setters).

```php
$mock = $this->getMockWithCalledMethods(Subject::class, [
    'load'      => OpenMageTest::WILL_RETURN_SELF,
    'getId'     => 42,
    'getStatus' => 1,
]);
```

## Long-running test detection

`OpenMage\Tests\Unit\LongRunningTestAlert` is registered as a PHPUnit `<extension>` in `.phpunit.dist.xml`. Any test that exceeds `MAX_SECONDS_ALLOWED = 1.0` prints a warning to stderr after the run. Treat the warning as actionable — split, mock, or move heavy fixtures.

## Per-suite invocation

Suites are defined per Mage module in `.phpunit.dist.xml`. Defaults loaded by `composer run phpunit:test` are `Base,Error,Mage,Varien` only — every `Mage_<Module>` suite (e.g. `Mage_Catalog`, `Mage_Adminhtml`, `Mage_Sales`) must be invoked explicitly:

```bash
XDEBUG_MODE=off vendor/bin/phpunit --configuration .phpunit.dist.xml --testsuite Mage_Catalog
XDEBUG_MODE=off vendor/bin/phpunit --configuration .phpunit.dist.xml tests/unit/Mage/Catalog/Model/ProductTest.php
XDEBUG_MODE=off vendor/bin/phpunit --configuration .phpunit.dist.xml --filter testValidate tests/unit/Mage/Catalog/Model/ProductTest.php
```

Always set `XDEBUG_MODE=off` unless you need coverage; otherwise the run is ~10× slower. For HTML coverage use `composer run phpunit:coverage-local` (output under `build/phpunit/coverage/`).

## Checklist before committing a test

- Extends `OpenMageTest`, class is `final`, file has `declare(strict_types=1);`.
- `#[Override]` on `setUpBeforeClass` / `setUp` / `tearDown` if present.
- Subject alias used; static `$subject` typed as `Subject`.
- Providers are `public static`; no `$this->` inside them.
- `@group <Model|Block|Helper|...>` on each test; `@runInSeparateProcess` + matching group where the test mutates globals.
- Trait providers landed under `tests/unit/Traits/DataProvider/<Mirror>/`, not inline, when reused.
- The relevant `--testsuite Mage_<Module>` run is green locally; long-running warnings addressed.

## Cross-refs

- `phpstan-magento1` — `dynamicCallOnStaticMethod: true` is what forces `public static` providers and `self::`/`static::` discipline; baseline regen flow lives there.
- `mage-adminhtml` — admin-side tests and the `runInSeparateProcess` group convention.
- `rector-openmage` — `final` and `#[Override]` enforcement, custom Migration rules that may rewrite legacy method names referenced from tests.
