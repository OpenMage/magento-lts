# OpenMage / Magento LTS — Agent Guide

This file provides guidance to coding agents (Claude Code, Copilot, Cursor, etc.) working in this repository.

## Project

OpenMage / `openmage/magento-lts`: a community-driven LTS distribution intended as a drop-in replacement. This repo *is* the upstream for OpenMage — fix bugs in `app/code/core/Mage/...` directly. Don't paper over with `app/code/local` or `app/code/community` overrides.

Claude Code note: OpenMage relies on Magento 1 architecture and conventions: `Mage_*` modules, class aliases, XML configuration, setup scripts, events/observers, and layout XML remain core concepts.

PHP support range: `>=8.1 <8.6` (composer platform pinned to 8.1). PHPStan checks against 8.1–8.5.

### Backwards compatibility (hard constraint)

Merchants and third-party extensions depend on existing public surface area. Treat the following as public API and do not break:

- **Method signatures** on `Mage_*` / `Varien_*` / `lib/Magento/*` / `lib/Mage/*` classes — no changes to params, return types, or visibility on existing public/protected methods.
- **Public class members, constants, and class names.** Deprecate via `@deprecated` rather than removing. Don't rename.
- **DB schema.** All schema/data changes must go through versioned setup scripts under `app/code/core/Mage/<Module>/{sql,data}/<setup>/...` with the matching `<modules>` version bump in `etc/config.xml`.
- **Layout handles, block types, class aliases (`catalog/product`), config XML paths, and event names.** If a rename is unavoidable, keep the old name as a passthrough.

## Commands

All quality tooling is wired through composer scripts (`composer.json` → `scripts`). Always prefer the composer script over invoking the underlying binary so the right config and cache paths are used.

```bash
# Lint / format (ECS wraps php-cs-fixer)
composer run php-cs-fixer:test       # check
composer run php-cs-fixer:fix        # auto-fix

# Static analysis
composer run phpstan:test            # PHPStan level 8 with macopedia/phpstan-magento1
composer run phpstan:baseline        # regenerate split baselines under .phpstan.dist.baselines/

# Mess detection
composer run phpmd:test
composer run phpmd:baseline

# Automated refactors
composer run rector:test             # dry-run
composer run rector:fix

# Tests (PHPUnit 9.6)
composer run phpunit:test            # all default suites: Base, Error, Mage, Varien
composer run phpunit:coverage        # textdox + coverage (needs Xdebug)
composer run phpunit:coverage-local  # HTML coverage at build/phpunit/coverage/

# Aggregate (cs-fixer + phpstan + phpunit)
composer test
```

PHPCS (`magento-ecg/coding-standard` + `phpcompatibility/php-compatibility`) runs in CI only — there's no composer script. Reproduce locally with `vendor/bin/phpcs` if a CI run flags issues.

### Running a single test or suite

PHPUnit suites are defined per Mage module in `.phpunit.dist.xml`. To run one module:

```bash
XDEBUG_MODE=off vendor/bin/phpunit --configuration .phpunit.dist.xml --testsuite Mage_Catalog
```

To run a single test file or method:

```bash
XDEBUG_MODE=off vendor/bin/phpunit --configuration .phpunit.dist.xml tests/unit/Mage/Catalog/Model/ProductTest.php
XDEBUG_MODE=off vendor/bin/phpunit --configuration .phpunit.dist.xml --filter testSomeMethod tests/unit/Mage/Catalog/Model/ProductTest.php
```

The default `phpunit:test` script only loads `Base,Error,Mage,Varien`. Per-module suites (e.g. `Mage_Catalog`) exist but must be invoked explicitly.

### Local dev environment

DDEV config lives in `.ddev/` (see `dev/openmage/README.md` and the docs site). The Cypress base URL (`.cypress.config.js`) is `https://magento-lts.ddev.site`, which is what you'll get from `ddev start` in this repo. Cypress specs live under `cypress/e2e/`.

## Architecture

This is an OpenMage codebase — module-based, not Symfony/Laravel. Read this section before making structural changes.

### OpenMage mental model

A short orientation for agents new to OpenMage — these are the abstractions everything else hangs off:

- **Bootstrap & DI:** `Mage::app()` boots the application. Instances come from `Mage::getModel('catalog/product')`, `Mage::getSingleton(...)`, `Mage::helper(...)`. Cross-request state lives in `Mage::registry($key)`. Config reads via `Mage::getStoreConfig($path)`.
- **Class aliases:** `catalog/product` → `Mage_Catalog_Model_Product` is wired in each module's `etc/config.xml` under `<global><models>/<helpers>/<blocks>`. Aliases are public surface — don't rename without keeping the old as a passthrough.
- **Extending core:** prefer **config rewrites** (`<rewrite>` blocks in config.xml) over subclassing in `local/`. In *this* repo, prefer fixing in `app/code/core/Mage/...` directly per the BC rules above.
- **Events / observers:** `Mage::dispatchEvent('event_name', ['key' => $val])` + observers declared in config XML (`<events><event_name><observers>...`). Existing event names are public API.
- **Layout & rendering:** `app/design/{adminhtml,frontend}/<package>/<theme>/{layout/*.xml, template/**.phtml}`. Layout handles, block types, and template paths are public surface.
- **Setup scripts:** schema/data migrations are version-bumped per module via the `<modules>` block in `etc/config.xml`; files live under `sql/<setup>/...` (DDL) and `data/<setup>/...` (DML).
- **Translations:** `app/locale/<locale>/Mage_<Module>.csv`.

### Module layout

- `app/code/core/Mage/<Module>/` — OpenMage core modules. Treat as the canonical surface; **prefer fixing bugs in place** rather than overriding from `local`/`community`, since this repo *is* the upstream for OpenMage.
- `app/code/community/<Vendor>/<Module>/` — bundled third-party modules.
- `app/code/local/<Vendor>/<Module>/` — local overrides (rarely used in this repo itself; consumed by integrators).
- `lib/Varien/`, `lib/Mage/`, `lib/Magento/` — framework-level libraries (Varien is the legacy DB/collection/IO layer).
- `app/design/{adminhtml,frontend,install}/` — `.phtml` templates and layout XML. PHPStan analyzes `.phtml` files too.
- `app/etc/modules/*.xml` — module activation manifests.
- `app/locale/` — translation CSVs.
- `errors/` — error page handlers (also analyzed).
- `shell/` — CLI entry points (also analyzed).

A typical module has `etc/config.xml` + optional `etc/system.xml`/`api.xml`/`wsdl.xml`, `Block/`, `Helper/`, `Model/` (with `Resource/` for DB), `controllers/` (lowercase, classmap-autoloaded), `data/`/`sql/` (migrations), and the layout/template under `app/design/`. Class names follow Magento's underscore convention: `Mage_Catalog_Model_Product` ↔ `app/code/core/Mage/Catalog/Model/Product.php`.

### Bootstrap

- Web entry: `index.php` → `app/Mage.php` → `Mage::run()`.
- API: `api.php`. Cron: `cron.php`. Installer: `install.php`. Static media handler: `get.php`.
- Tests: `tests/bootstrap.php` requires `app/Mage.php` and `errors/processor.php`. The base `OpenMage\Tests\Unit\OpenMageTest` class calls `Mage::app()` in `setUpBeforeClass()` — extend it (not raw `TestCase`) for tests that need the app initialized.

### Tests

- **Layout:** namespace `OpenMage\Tests\Unit\<MirrorOfSource>`; the directory tree under `tests/unit/` mirrors `app/code/core/Mage/`.
- **Base class:** new test classes are `final` and extend `OpenMage\Tests\Unit\OpenMageTest` (which boots `Mage::app()` in `setUpBeforeClass`).
- **`#[Override]` attribute:** mark overridden methods (e.g. `setUpBeforeClass`) with `#[Override]` — Rector enforces via `AddOverrideAttributeToOverriddenMethodsRector`.
- **Subject alias convention:** `use Mage_Foo_Helper_Data as Subject;` then assert against `Subject::method()` or a `private static Subject $subject` populated in `setUpBeforeClass`.
- **Data providers:** shared providers live in traits under `tests/unit/Traits/DataProvider/<Mirror>/...` — `use FooTrait;` in the test class.
- **PHPUnit 10 compatibility:** data providers MUST be `public static`.
- **Long-running test detection:** `OpenMage\Tests\Unit\LongRunningTestAlert` PHPUnit extension.

## Coding conventions

These rules come from `.github/copilot-instructions.md`, the ECS config, and PHPStan/Rector — apply to all new code:

- `declare(strict_types=1);` in new PHP files.
- Native parameter and return types on new methods are required, not optional. Add a return type even when it's `void`.
- Docblocks must not restate what native types already express. Drop `@param Foo $x` / `@return Foo` when the signature already says so. Keep them only when they add information the type system can't — array shapes (`array<string, Foo>`, `list<int>`), generics, union narrowing beyond the hint, or a meaningful description. A bare `@return self` above `: self` is noise; delete it.
- camelCase method names (no underscores) for new methods. Existing core methods stay as-is for BC.
- Don't introduce new underscore-prefixed properties (`protected $_var`) on new classes — that style is BC for legacy classes only. New classes use typed `private`/`protected` without the leading underscore.
- Strict comparisons `===`/`!==`. Avoid `empty()` — use explicit checks.
- Short array syntax `[]`. Single quotes for simple strings (ECS rewrites doubles to singles).
- Use named arguments at call sites where they aid clarity (e.g. boolean flags).
- PER-CS / PSR-12 style, enforced by ECS (`.php-cs-fixer.dist.php`).
- Prefer the `#[Override]` attribute on overridden methods (Rector enforces).
- Don't use Prototype.js for new JS. ES6+ and jQuery (already a dep) only.
- New PHP files use the standard 4-line file-level docblock:

  ```php
  /**
   * @copyright  For copyright and license information, read the COPYING.txt file.
   * @link       /COPYING.txt
   * @license    Open Software License (OSL 3.0)
   * @package    Mage_<Module>
   */
  ```

  For tests, `@package OpenMage_Tests`. Existing files use OSL-3.0 / AFL-3.0 — match the surrounding header when editing legacy code.

### Magic accessors and `@method` docblocks

Models extending `Varien_Object` use `__call` to expose `setX/getX/hasX/unsX` for any data key. Class docblocks declare them as `@method` for IDE autocomplete and PHPStan.

- When you add a new data key (DB column, registered attribute, custom payload), add or update the matching `@method` entries on the model.
- Don't trust IDE "Go to definition" hits — many `@method` entries route through `__call` and have no real implementation anywhere.
- ECS's `PhpdocAlignFixer` aligns these vertically and `PhpdocOrderFixer` enforces tag order — let the formatter run.

### PHPStan gotchas (level 8, strict rules on)

`.phpstan.dist.neon` sets `dynamicCallOnStaticMethod: true` (via `bleedingEdge.neon`). Two consequences when writing tests:

- Never call a `static` method via `$this->method()` — call as `self::method()` or `ClassName::method()`. PHPStan will flag `staticMethod.dynamicCall`.
- Inside a `public static function provideX()` data provider, never use `$this->`. Use `static::` (for trait helpers that may be overridden) or `self::`.
- When you make a helper `static` to be callable from a data provider, update *every* caller — including non-static test methods — from `$this->helper()` to `self::helper()`.

PHPStan baselines are split per identifier under `.phpstan.dist.baselines/` (loaded via `_loader.php`). After a refactor that changes the error count, run `composer run phpstan:baseline` and commit the regenerated baseline files.

### Rector — what it enforces

`.rector.php` runs at PHP 8.1 set + dead-code, codeQuality, codingStyle, privatization, instanceOf, earlyReturn, carbon, and phpunitCodeQuality presets.

- Custom Migration rules under `dev/rector/Migration/` rename old method names to current names (Mage modules, `Zend_Log`/`Zend_Measure`/`Zend_Acl`/`Zend_Captcha` → laminas/Carbon equivalents). When you write code calling an old method name, Rector will rewrite it on the next `composer run rector:fix` — don't fight it.
- A sizable skip list lives in `.rector.php` for rules that are too noisy, change method signatures, or break BC. If a Rector run looks wrong, check the skip list before changing code.
- Run `composer run rector:test` (dry-run) before pushing structural changes so CI doesn't surprise you.

### Vendor patches

Patches against composer dependencies are tracked in `patches.json` (with `patches.lock.json`) and stored under `.vendor-patches/`. To regenerate after editing a vendor file: `composer run vendor:patch` (uses `symplify/vendor-patches`).

## Caches & build artifacts

- All tooling caches live under `.cache/` (ECS, PHPStan, PHPMD, Rector). Coverage HTML and PHPMD reports go to `build/`. Both are gitignored.
- PHPStan baseline files under `.phpstan.dist.baselines/` are split per identifier and **committed**; regenerate with `composer run phpstan:baseline` after refactors that move the error count.

## CI

GitHub Actions under `.github/workflows/`: `composer.yml`, `phpstan.yml`, `phpcs.yml`, `phpmd.yml`, `php-cs-fixer.yml`, `rector.yml`, `cypress.yml`, `codeql-analysis.yml`, `docs.yml`, plus syntax/spellcheck/labeler/release-drafter helpers. Each tool's CI mirrors the corresponding composer script — running `composer test` locally before pushing covers the main gates (cs-fixer + phpstan + phpunit). PHPCS and Rector run in CI but have to be triggered manually or via their own scripts locally.

## Contribution flow

- PR template: `.github/PULL_REQUEST_TEMPLATE.md` — fill in description, related/fixed issues, and manual test steps.
- Run `composer test` before pushing; the same gates run in CI.
- Dependabot opens its own dep-bump and security PRs (`dependabot.yml`); don't bundle dep bumps into feature PRs.
- After a refactor that changes static-analysis output, regenerate the PHPStan baselines (`composer run phpstan:baseline`) and commit them with the change.
