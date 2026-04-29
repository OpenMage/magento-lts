---
name: rector-openmage
description: Rector in OpenMage — active presets (php81/deadCode/codeQuality/codingStyle/privatization/instanceOf/earlyReturn/carbon/phpunitCodeQuality), custom Migration rules under dev/rector/Migration/ (renames Zend→laminas/Carbon, old Mage method names), skip list, #[Override] enforcement. Use when running composer run rector:*, editing .rector.php, or understanding why Rector wants to rewrite your code.
---

# Rector in OpenMage

Rector is wired through `.rector.php` at the repo root. Cache lives under `.cache/.rector.result.cache`. Custom rules live under `dev/rector/Migration/`. Run via composer scripts — never the binary directly.

## Commands

- `composer run rector:test` — dry-run (no writes). What CI gates on. Run this before structural changes.
- `composer run rector:test:debug` — dry-run with `--debug` (slow; use to chase rule order).
- `composer run rector:fix` — writes changes in place.

CI workflow: `.github/workflows/rector.yml`. Targets `.php` and `.phtml`.

## Active config (`.rector.php`)

- PHP set: `php81: true` (PHP 8.0 / 8.1 fixers run; nothing newer).
- Prepared sets (on):
  - `deadCode` — drops unreachable code, unused privates, redundant casts.
  - `codeQuality` — simplifies expressions, flattens conditions, prefers `??`/`?:` where safe.
  - `codingStyle` — small style rewrites (use imports, encapsed strings, etc.).
  - `privatization` — narrows visibility, marks classes/methods `private`/`final` when safe.
  - `instanceOf` — collapses `instanceof` chains, drops redundant checks.
  - `earlyReturn` — flips conditions to early-return / early-continue.
  - `carbon` — rewrites `date()`/`time()`/`DateTime` toward Carbon.
  - `phpunitCodeQuality` — modernizes test idioms (e.g. `assertSame`, attributes).
- Prepared sets (off): `typeDeclarations`, `naming`, `strictBooleans`, `doctrineCodeQuality`, `symfonyCodeQuality`, `symfonyConfigs`. Don't enable without a plan — most would generate hundreds of changes.
- Explicit individual rules:
  - `Php83\AddOverrideAttributeToOverriddenMethodsRector` — enforces `#[Override]`. New tests and any overridden method need it.
  - `Php85\ArrayFirstLastRector` — `reset()`/`end()` → `array_first()`/`array_last()`.

## Custom Migration rules (`dev/rector/Migration/`)

Rector rewrites old API calls to current ones. When you write code calling an old method name, the next `rector:fix` will rename it — don't fight the rename, accept it.

Two categories:

### Mage method renames — `dev/rector/Migration/Mage/`

One file per module, each exposes `::renameMethod()` returning `RenameMethodRector` config. Wired in `.rector.php` via `withConfiguredRule(RenameMethodRector::class, ...)`.

- `Admin.php`, `Adminhtml.php` (largest; also exposes `replaceArgumentDefaultValue()`)
- `Bundle.php`, `Catalog.php`, `CatalogSearch.php`, `Checkout.php`, `ConfigurableSwatches.php`
- `Core.php`, `Eav.php`, `Paypal.php`, `Shipping.php`, `Sitemap.php`
- `Tag.php`, `Tax.php`, `Usa.php`, `Wishlist.php`

These map deprecated camelCase/legacy names on `Mage_*` classes to the current name. Don't add new aliases here — add the rename only when the old name has been removed and we need to migrate callsites.

### Zend → laminas / Carbon — `dev/rector/Migration/Zend/`

- `Log.php` — `Zend_Log` constants → `Laminas\Log` constants (`renameClassConst()`).
- `Measure.php` — `Zend_Measure_*` constants → `Laminas\Measure` (`renameClassConst()`).
- `Acl.php` — `Zend_Acl` method renames.
- `Captcha.php` — `Zend_Captcha` method renames.

### Docblock typing — `dev/rector/Migration/TypeDeclarationDocblocks.php`

Bulk-loaded via `Migration\TypeDeclarationDocblocks::getRules()`. Adds/normalizes docblock types where the type system can't be hinted natively (legacy BC).

## Skip list — read before "fixing" a finding

`.rector.php` has three `withSkip()` blocks. Categories:

### Permanent (BC / signature changes)

- `ClassPropertyAssignToConstructorPromotionRector` — **changes constructor signatures**. Off globally; would break subclasses overriding `__construct`.
- `ReturnNeverTypeRector` — **changes method signatures** by adding `: never`. Off globally.
- `DeclareStrictTypesRector` — `declare(strict_types=1)` can't be applied repo-wide yet (legacy code relies on PHP juggling). New files add it manually per AGENTS.md.
- `FuncGetArgsToVariadicParamRector` — variadic conversion changes signatures.
- `TernaryToElvisRector` — conflicts with PHPStan strict rules.

### Targeted file skips

- `Carbon\DateFuncCallToCarbonRector` / `TimeFuncCallToCarbonRector` skipped on `tests/unit/Base/CarbonTest.php` (the test asserts the original behavior).
- `CompleteDynamicPropertiesRector` skipped on `lib/3Dsecure/XMLParser.php`.
- `ExplicitReturnNullRector` skipped on a handful of core files that return-or-throw — Rector can't model exception-as-return yet (rectorphp/rector#9719).
- `ThrowWithPreviousExceptionRector` skipped on `Mage_Api2_Model_Auth_Adapter_Oauth` (would change `getUserParams()` behavior).
- `RemoveAlwaysTrueIfConditionRector` skipped on `system/store/tree.phtml` (rule misreads).
- `ChangeNestedForeachIfsToEarlyContinueRector` skipped on `Checkout/Model/Cart/Payment/Api.php` (rectorphp/rector#9732).
- `AddParamBasedOnParentClassMethodRector` skipped on `lib/Varien/Directory/Collection.php`.

### Too-noisy (deferred — re-evaluate later)

- `UseIdenticalOverEqualWithSameTypeRector` — +300 occurrences.
- `ExplicitBoolCompareRector` — +300 occurrences.
- `EncapsedStringsToSprintfRector` — +250 occurrences.
- `PostIncDecToPreIncDecRector` — +200 occurrences.
- `RemoveNullPropertyInitializationRector` — +400 occurrences.
- `NullToStrictStringFuncCallArgRector` — +300 occurrences.
- `DisallowedEmptyRuleFixerRector` — ~100 occurrences.
- `StrictArraySearchRector` — waits on PHPStan strict rules.
- `IssetOnPropertyObjectToPropertyExistsRector` — breaks site loading.
- `AbsolutizeRequireAndIncludePathRector` — needs a global autoload review.
- `RemoveUnusedConstructorParamRector`, `RemoveDeadTryCatchRector`, `ClosureToArrowFunctionRector`, `BinaryOpNullableToInstanceofRector`, `RemoveExtraParametersRector` — need closer review.
- `Php81\ArrayToFirstClassCallableRector` — WIP, see PR #5434.
- `PreferPHPUnitThisCallRector` (PHPUnit) — we use static calls; skipped on `shell/translations.php` and one Reports test.

## When Rector rewrites your code

1. Read the diff. If the rewrite preserves behavior and matches the active presets, accept it — let `rector:fix` write.
2. If it looks wrong, check the skip list for the rule's class name. Rules in the "permanent" block are off for a reason; don't re-enable to "fix" one file.
3. If the rule isn't skipped and the rewrite is genuinely unsafe (BC break, semantics change), add the rule to the appropriate `withSkip()` block with a one-line comment explaining why.
4. Don't disable a custom Migration rename to keep an old method name — fix the call site to use the new name (or accept Rector's rename).

## `#[Override]` enforcement

`AddOverrideAttributeToOverriddenMethodsRector` runs always. New tests extending `OpenMageTest` need `#[Override]` on `setUpBeforeClass`, `setUp`, `tearDown` etc. — Rector adds it on `rector:fix`. PHP 8.3+ runtime enforces the attribute, so missing parents become hard errors.

## Adding a new rename

1. Find or create the appropriate `dev/rector/Migration/Mage/<Module>.php` (or `Zend/<X>.php`).
2. Add the `MethodCallRename` / `RenameClassConstFetch` value object to the array returned by `renameMethod()` / `renameClassConst()`.
3. Wire it in `.rector.php` via `withConfiguredRule(...)` if the file is new.
4. Run `composer run rector:test` to verify.
5. After merge, `composer run rector:fix` rewrites all callers in subsequent PRs.

## Cross-refs

- `phpstan-magento1` — some Rector skips wait on PHPStan strict rules being on (`StrictArraySearchRector`, `TernaryToElvisRector`).
- `phpunit-openmage-tests` — `#[Override]` and `final class` conventions are enforced by Rector but *described* there.
- `AGENTS.md` — high-level Rector posture and BC rules.
