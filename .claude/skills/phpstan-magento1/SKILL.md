---
name: phpstan-magento1
description: PHPStan in OpenMage — level 8 + strictRules.allRules + bleedingEdge, macopedia/phpstan-magento1 plugin (alias resolution), dynamicCallOnStaticMethod trap, split-per-identifier baselines, regen workflow. Use when fixing PHPStan errors, running composer run phpstan:test, regenerating baselines, or understanding why $this->staticMethod() is flagged.
---

# PHPStan in OpenMage / magento-lts

OpenMage runs PHPStan at **level 8** with `strictRules.allRules` and `bleedingEdge` on, plus the `macopedia/phpstan-magento1` plugin for OpenMage alias resolution. Errors that aren't fixable yet live in **split-per-identifier baselines** under `.phpstan.dist.baselines/` (tens of thousands of entries — regenerate with `composer run phpstan:baseline` to get an exact count).

Cross-refs: `phpunit-openmage-tests` (data-provider statics interact with `dynamicCallOnStaticMethod`).

## Commands

```bash
composer run phpstan:test       # XDEBUG_MODE=off php vendor/bin/phpstan analyze
composer run phpstan:baseline   # regen + split + git add baselines
```

`phpstan:baseline` does three things in order:
1. `phpstan analyze -b .phpstan.dist.baselines/_loader.php` — write a fresh single baseline.
2. `split-phpstan-baseline .phpstan.dist.baselines/_loader.php --no-error-count` (from `shipmonk/phpstan-baseline-per-identifier`) — split into one file per identifier.
3. `git add .phpstan.dist.baselines/*` — stage the result.

Always commit the regenerated baselines alongside the change that moved the error count.

## Config posture (`.phpstan.dist.neon`)

- `level: 8`
- `strictRules.allRules: true` (via `phpstan/phpstan-strict-rules`) → enables `dynamicCallOnStaticMethod`, `noVariableVariables`, `strictFunctionCalls`, etc.
- `bleedingEdge.neon` included → opts in to upcoming default-on rules
- `treatPhpDocTypesAsCertain: false` — phpdoc-only types are treated as suspect
- `phpVersion.min: 80100`, `max: 80599` — PHP 8.1–8.5 surface
- `fileExtensions: [php, phtml]` — `.phtml` templates are analyzed
- Stubs under `dev/phpstan/stubs/Zend/` for `Log`, `Measure`, `Validate`
- Cache: `.cache/.phpstan.cache`
- Paths: `app/Mage.php`, `app/code/core/Mage`, `app/design/`, `errors`, `lib/Mage`, `lib/Magento`, `lib/Varien`, `shell`, `tests/unit`, plus `api.php cron.php get.php index.php install.php`

## macopedia/phpstan-magento1 plugin

The plugin teaches PHPStan about OpenMage idioms.

- **Alias resolution.** `Mage::getModel('catalog/product')` is inferred as `Mage_Catalog_Model_Product`. Same for `Mage::getSingleton`, `Mage::helper`, and block aliases. The plugin reads each module's `etc/config.xml` `<global><models|helpers|blocks>` mappings.
- **`_init()` calls.** The plugin registers dynamic return-type extensions for `Mage::getModel/getSingleton/getResourceModel/helper`, `Layout::createBlock/helper/getBlockSingleton`, and `Varien_Object` magic methods. It does NOT inspect `_init()` calls.
- **What it can't infer.**
  - Aliases built from runtime concatenation (`Mage::getModel($prefix . '/foo')`) — falls back to `Mage_Core_Model_Abstract|false`.
  - Custom factory wrappers that hide the alias string.
  - `@method` magic accessors on `Varien_Object` subclasses — these still need `@method` docblocks on the class for PHPStan to see `getFooBar()`.

When PHPStan can't see an alias, the fix is usually a `@method` entry on the model or a `/** @var Foo $x */` at the call site — not adding to baseline.

## The `dynamicCallOnStaticMethod` trap

`phpstan-strict-rules` with `strictRules.allRules: true` enables `dynamicCallOnStaticMethod`. Calling a `static` method via `$this->` is an error (`staticMethod.dynamicCall`).

```php
// WRONG — static method called via $this
final class FooTest extends OpenMageTest
{
    public static function provideThings(): array { return self::buildCases(); }

    public function testIt(): void
    {
        $cases = $this->buildCases(); // staticMethod.dynamicCall
    }

    private static function buildCases(): array { /* ... */ }
}

// RIGHT — call as self:: or static::
final class FooTest extends OpenMageTest
{
    public static function provideThings(): array { return self::buildCases(); }

    public function testIt(): void
    {
        $cases = self::buildCases();
    }

    private static function buildCases(): array { /* ... */ }
}
```

Inside a `public static function provideX()` data provider, **never** use `$this->`. Use `self::` or `static::` (the latter for trait helpers that may be overridden).

When you flip a helper to `static` so a data provider can call it, update **every** caller — non-static test methods included — from `$this->helper()` to `self::helper()`.

## Split-per-identifier baselines

`.phpstan.dist.baselines/_loader.php` `includes` one PHP file per error identifier — currently ~110 files. The split-per-identifier scheme keeps diffs scoped when an identifier's count moves; the totals are not stamped into the loader (run `composer run phpstan:baseline` if you need a fresh count). Highlights:

- `argument.type.php` — call-site type doesn't match the param hint
- `arguments.count.php` — wrong number of args
- `arrayFilter.strict.php` — `array_filter` without strict mode
- `arrayValues.list.php` — `array_values` result not narrowed to `list`
- `assign.propertyType.php` — assignment doesn't match declared property type
- `binaryOp.invalid.php` — invalid `+`/`-`/`.` operands
- `booleanAnd.*.php`, `booleanOr.*.php`, `booleanNot.*.php` — strict-rules: condition always true/false, non-bool operand
- `cast.useless.php` — redundant `(int)` / `(string)` etc.
- `catch.neverThrown.php` — `catch` clause for an exception that can't be thrown
- `class.extendsDeprecatedClass.php`, `class.nameCase.php`, `class.notFound.php` — class-level deprecation/case/missing
- `classConstant.deprecatedClass.php`
- `constructor.missingParentCall.php` — strict-rules: child ctor doesn't call `parent::__construct`
- `deadCode.unreachable.php` — code after `return`/`throw`
- `echo.nonString.php` — `echo` of a non-stringable value
- `elseif.alwaysFalse.php`, `elseif.alwaysTrue.php`, `elseif.condNotBoolean.php`
- `empty.notAllowed.php` — strict-rules: ban on `empty()` (use explicit checks)
- `equal.alwaysTrue.php`, `equal.invalid.php`, `equal.notAllowed.php` — strict-rules: forbid `==`, prefer `===`
- `for.variableOverwrite.php`, `foreach.{emptyArray,keyOverwrite,nonIterable,valueOverwrite}.php`
- `function.alreadyNarrowedType.php`, `function.impossibleType.php`, `function.strict.php`
- `greater.*.php`, `greaterOrEqual.alwaysTrue.php`, `identical.alwaysFalse.php` — comparisons that always resolve one way
- `if.alwaysFalse.php`, `if.alwaysTrue.php`, `if.condNotBoolean.php`
- `instanceof.alwaysTrue.php`
- `method.deprecated.php`, `method.deprecatedClass.php`, `method.dynamicName.php`, `method.nonObject.php`, `method.notFound.php`
- `methodTag.deprecatedClass.php` — `@method` docblock points at deprecated class
- `minus.{left,right}NonNumeric.php`
- `missingType.{generics,iterableValue,parameter,property,return}.php` — level-6 missing type info (the bulk of entries)
- `notEqual.notAllowed.php`, `notIdentical.alwaysTrue.php`
- `offsetAccess.nonOffsetAccessible.php`
- `parameterByRef.type.php`
- `property.{deprecated,deprecatedClass,dynamicName,nonObject,notFound,phpDocType}.php`
- `return.empty.php`, `return.missing.php`, `return.type.php`
- `staticMethod.deprecated.php`, `staticMethod.notFound.php`
- `switch.type.php`
- `ternary.alwaysTrue.php`, `ternary.condNotBoolean.php`, `ternary.shortNotAllowed.php`
- `varTag.deprecatedClass.php`, `varTag.type.php`
- `variable.{dynamicName,implicitArray,undefined}.php`
- `while.condNotBoolean.php`

Inline `ignoreErrors` in `.phpstan.dist.neon` separately silences a few patterns globally (e.g. `Cannot call method ... on Mage_Core_Model_Store|null`, undefined `$this` in legacy admin `.phtml`, `variable.undefined` under `lib/Varien/*` and `errors/*`, `method.protected`/`property.protected` under `app/design/*/*/template/*`).

`method.childParameterType`, `method.childReturnType`, and `cast.string` errors are tracked entry-by-entry in their respective baseline files — they are not globally suppressed.

## Fix vs ignore vs baseline

When a new error appears:

1. **Fix** if the error is in code you're editing or it's a real bug. Default action.
2. **Ignore inline** (`@phpstan-ignore-next-line identifier.foo`) only when the analyzer is genuinely wrong about a Magento idiom the plugin can't model — and add a one-line comment explaining why.
3. **Baseline** if the error is pre-existing and the fix is out of scope for the current PR. After a refactor that *adds* a temporary identifier or that *moves* error counts, run `composer run phpstan:baseline` and commit.

Don't add new entries to baselines for code you just wrote — fix it. Baselines are for legacy debt only.

## Regen workflow

```bash
composer run phpstan:test       # see the new error
# either fix it, or:
composer run phpstan:baseline   # regen + split + stage
git status .phpstan.dist.baselines/
git commit
```

If `phpstan:baseline` produces churn unrelated to your change, run `phpstan:test` first to confirm the noise existed pre-edit, then commit the regen as a separate prep commit.

## Common gotchas

- Don't suppress `staticMethod.dynamicCall` — fix the call site to `self::` / `static::` / `ClassName::`.
- `empty()` is banned everywhere; legacy uses are baselined entry-by-entry. Use `=== null`, `=== ''`, `=== []`, or `=== 0` as appropriate.
- `==`/`!=` are banned (`equal.notAllowed`, `notEqual.notAllowed`). Use `===` / `!==`.
- `treatPhpDocTypesAsCertain: false` means a `@var Foo $x` is *not* enough to convince PHPStan when it can also see a wider native type — narrow with `assert()` or `instanceof` instead.
- `.phtml` templates are analyzed; variables must come from the block class (declared properties or `@method` accessors), not from globals.
- The macopedia plugin only resolves alias strings it can see at parse time; runtime-built aliases fall back to abstract types.
- After making a helper `static` for a data provider, grep the test file for `$this->helperName(` and rewrite each.

## Cross-refs

- `phpunit-openmage-tests` — data provider patterns, why `public static` and `self::` matter together.
