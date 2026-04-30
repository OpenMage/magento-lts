---
name: openmage-module-structure
description: OpenMage module manifest, etc/config.xml, class aliases, version bumps, area scoping. Use when creating or registering a module, editing app/etc/modules/*.xml or */etc/config.xml, bumping module versions, or wiring class aliases (catalog/product, sales/order). Triggers on Mage::getModel/getSingleton/helper alias resolution questions.
---

# OpenMage Module Structure

Module = a directory under `app/code/<pool>/<Vendor>/<Module>/` plus an activation manifest under `app/etc/modules/`. Both files are required; either alone is silently ignored. See `AGENTS.md` for the broader BC contract on aliases and config paths.

## Activation: two files, both required

`app/etc/modules/<Vendor>_<Module>.xml` controls *whether* the module loads and which `codePool` it lives in. The module's own `etc/config.xml` controls *what* it does. Mage_* manifests are read in the order defined by `Mage_Core_Model_Config::MAGE_MODULES`; non-Mage manifests follow in filesystem order; only then is each enabled module's `etc/config.xml` merged into the global config tree.

```xml
<?xml version="1.0"?>
<config>
    <modules>
        <Mage_Catalog>
            <active>true</active>
            <codePool>core</codePool>
            <depends>
                <Mage_Cms/>
                <Mage_Dataflow/>
                <Mage_Eav/>
                <Mage_Index/>
            </depends>
        </Mage_Catalog>
    </modules>
</config>
```

Real example: `app/etc/modules/Mage_Catalog.xml`. `<codePool>` is `core` | `community` | `local`; this dictates which directory the module's PHP/XML are loaded from. Bundled community modules in this repo: `Cm_Cache`, `Cm_RedisSession`, `MM_Ignition` under `app/code/community/` — same pattern, just a different pool. `Mage_All.xml` is now empty (see the inline comment in that file); load order is hard-coded in `Mage_Core_Model_Config::MAGE_MODULES`.

## Module config.xml: declare aliases, set version

Every module that owns models/blocks/helpers declares them under `<global>`. Real shape from `app/code/core/Mage/Catalog/etc/config.xml`:

```xml
<config>
    <modules>
        <Mage_Catalog>
            <version>1.6.0.0.19.1.7</version>
        </Mage_Catalog>
    </modules>
    <global>
        <models>
            <catalog>
                <class>Mage_Catalog_Model</class>
                <resourceModel>catalog_resource</resourceModel>
            </catalog>
            <catalog_resource>
                <class>Mage_Catalog_Model_Resource</class>
                <entities>
                    <product><table>catalog_product_entity</table></product>
                </entities>
            </catalog_resource>
        </models>
        <blocks>
            <catalog><class>Mage_Catalog_Block</class></catalog>
        </blocks>
        <helpers>
            <catalog><class>Mage_Catalog_Helper</class></catalog>
        </helpers>
    </global>
</config>
```

The `<version>` here is what drives setup-script execution (see `openmage-db-setup-scripts`). The version in `app/etc/modules/*.xml` is *not* read for setup — that file only controls activation. Bump the version in `etc/config.xml`, never the manifest.

## Alias resolution: catalog/product → Mage_Catalog_Model_Product

Aliases like `catalog/product`, `sales/order`, `cms/page` are resolved by `Mage_Core_Model_Config::getGroupedClassName` (see `app/code/core/Mage/Core/Model/Config.php`). The split is on `/`: left side is the *group* (matched against `<global>/<models|blocks|helpers>/<group>`), right side is the *suffix*. The group's `<class>` is concatenated with the suffix in `uc_words` form.

```
Mage::getModel('catalog/product')
  → group='catalog', suffix='product'
  → <global><models><catalog><class>  = "Mage_Catalog_Model"
  → final class                       = "Mage_Catalog_Model_Product"
  → file (PSR-0-ish)                  = app/code/core/Mage/Catalog/Model/Product.php
```

Helpers are special-cased: `Mage::helper('catalog')` rewrites internally to `catalog/data` and resolves to `Mage_Catalog_Helper_Data`. See `getHelperClassName` in `Config.php`.

The same code path checks `<rewrite>` *before* applying the group prefix, which is how third parties override core classes:

```xml
<global>
    <models>
        <catalog>
            <rewrite>
                <product>Acme_Foo_Model_Product</product>
            </rewrite>
        </catalog>
    </models>
</global>
```

Aliases (`catalog/product`) and rewrite targets are public surface — see the BC rules in `AGENTS.md`.

## Resource models and entity tables

When a `<models>` group declares `<resourceModel>foo_resource</resourceModel>`, `Mage::getResourceModel('catalog/product')` resolves through that secondary group: `Mage_Catalog_Model_Resource_Product`. The `<entities>` map under the resource group is what `getResource()->getTableName('catalog/product')` reads — never hard-code table names in queries.

Setup classes are declared under `<global><resources>`, keyed `<modulename>_setup`:

```xml
<global>
    <resources>
        <catalog_setup>
            <setup>
                <module>Mage_Catalog</module>
                <class>Mage_Catalog_Model_Resource_Setup</class>
            </setup>
        </catalog_setup>
    </resources>
</global>
```

The `<module>` value links the resource node to the `<modules>/<Mage_Catalog>/<version>` value. Every `etc/config.xml` version bump triggers a re-scan of `sql/<setup>/` and `data/<setup>/` for that module. See `openmage-db-setup-scripts` for setup-script naming and the `Mage_Eav_Model_Entity_Setup` variant.

## Area scoping: when each block applies

Top-level XML nodes inside `<config>` are *areas*. Order matters at merge time:

- `<global>` — always loaded. Models, blocks, helpers, resources, events that fire in any context.
- `<frontend>` — storefront only. Routers, layout updates, frontend-only events, frontend translations.
- `<adminhtml>` — backend only. Admin layout, admin events, admin translations, ACL/menu (commonly split into `etc/adminhtml.xml`).
- `<crontab>` — cron runner only. Job declarations live here.
- `<install>` — installer only.
- `<default>` — default values for `core_config_data` (system config). Read via `Mage::getStoreConfig`.

A frontend-only observer goes under `<frontend><events>`, not `<global><events>`, or it will fire during admin actions and cron runs too. Same for `<crontab><events>`.

## Module dependencies

`<depends>` in the manifest controls load order *and* hard-fails activation if a dependency is missing. Use it whenever your `etc/config.xml` references aliases or rewrite targets owned by another module — without `<depends>`, a load-order race can leave your `<rewrite>` overridden by the other module's later merge. Modules are sorted topologically by `<depends>` only; modules with no dependency relation keep their input order (Mage modules use the `MAGE_MODULES` constant order, custom modules use filesystem glob order).

## Class autoload: underscore = directory separator

Magento's autoloader (`Varien_Autoload`) maps `Mage_Catalog_Model_Product` to `Mage/Catalog/Model/Product.php`, searched across all enabled `codePool` directories in the order `local` → `community` → `core`. This is why a class in `app/code/local/Mage/Catalog/Model/Product.php` overrides core without any config — but in *this* repo the BC rule is to fix in `app/code/core/Mage/...` directly (see `AGENTS.md`).

Two notable exceptions to the convention:

- **Controllers** are *not* PSR-0 autoloaded. `Mage_Catalog_ProductController` lives at `app/code/core/Mage/Catalog/controllers/ProductController.php` — note the lowercase `controllers/`. They're loaded by the dispatcher via classmap. See `openmage-controllers-routing`.
- **Setup scripts** are loaded by filename, not class name. See `openmage-db-setup-scripts`.

## Minimal new module checklist

1. `app/etc/modules/<Vendor>_<Module>.xml` with `<active>true</active>`, `<codePool>local</codePool>` (or `community`), `<depends>` for any module whose aliases you reference or rewrite.
2. `app/code/<pool>/<Vendor>/<Module>/etc/config.xml` with `<modules>/<Vendor_Module>/<version>` and the `<global>/<models|blocks|helpers>` aliases you'll use.
3. PHP classes following the underscore-to-directory convention.
4. If shipping schema: `<global><resources><vendor_module_setup>` + `sql/vendor_module_setup/install-X.Y.Z.php`.
5. If shipping events: `<global><events>` (or area-scoped) — see `openmage-events-observers`.
6. If shipping URLs: `<frontend><routers>` or admin controller registration — see `openmage-controllers-routing`.

## Common pitfalls

- Editing the version in `app/etc/modules/*.xml` instead of `etc/config.xml` — setup scripts won't run.
- Dropping observers in `<global><events>` when they should be `<frontend>` or `<adminhtml>`-scoped — they fire in cron and admin too, often with a missing session.
- Forgetting `<depends>` on a module whose alias you `<rewrite>` — your rewrite silently loses to load-order chance.
- Renaming a class alias (`catalog/product` → `catalog/item`). Aliases are public API; keep the old key as a passthrough.
- Putting helpers under a non-`data` suffix and calling `Mage::helper('foo')` — that always resolves to `foo/data`. Other helpers need the explicit suffix: `Mage::helper('foo/url')`.
