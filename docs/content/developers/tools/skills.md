---
tags:
- Development
- Documentation
---

# OpenMage skills

OpenMage ships 27 agent skills under `.claude/skills/<name>/SKILL.md`. These files stay in the repository and auto-load as project skills when contributors open `OpenMage/magento-lts` in Claude Code.

The repository also exposes the same skills as the `openmage-skills` Claude Code plugin marketplace package. Use the marketplace when you want the OpenMage skills in another project without cloning the full `magento-lts` repository.

## Install in Claude Code with the marketplace

Inside an interactive Claude Code session, run:

```text
/plugin marketplace add OpenMage/magento-lts
/plugin install openmage-skills@openmage
```

From a shell, run the same installation non-interactively:

```bash
claude plugin marketplace add OpenMage/magento-lts
claude plugin install openmage-skills@openmage
```

To update the marketplace metadata and install the newest package revision, run:

```text
/plugin marketplace update
```

Each commit on `main` counts as a new package version because the plugin manifest does not pin a `version` value.

## Install in other agents

[`vercel-labs/skills`](https://github.com/vercel-labs/skills) reads the same `SKILL.md` files and links them into the skill layout used by supported agents, including Cursor, Codex, OpenCode, and Claude Code. Use this method when your agent does not use the Claude Code marketplace, or when you want to install without adding the marketplace.

Preview the available skills:

```bash
bunx skills add OpenMage/magento-lts --list
```

Install all OpenMage skills:

```bash
bunx skills add OpenMage/magento-lts --all
```

Install one skill:

```bash
bunx skills add OpenMage/magento-lts --skill openmage-eav
```

Target specific agents instead of using auto-detection:

```bash
bunx skills add OpenMage/magento-lts -a cursor -a claude-code
```

If you do not use Bun, replace `bunx` with `npx` in the same commands.

## Included skills

| Skill | Focus |
| --- | --- |
| `mage-module-adminhtml` | Admin UI blocks, grids, forms, tabs, mass actions, notifications, form keys, serializers, and dependent fieldsets. |
| `mage-module-catalog` | Catalog products, categories, URL rewrites, layered navigation, flat tables, image cache, and indexing. |
| `mage-module-checkout` | Onepage and multishipping checkout, quote session behavior, login-time quote merge, agreements, and quote-to-order conversion. |
| `mage-module-cms` | CMS pages, static blocks, widgets, WYSIWYG directives, CMS routing, and widget declarations. |
| `mage-module-customer` | Customer EAV data, addresses, sessions, password hashing, customer groups, and login flow. |
| `mage-module-payment-methods` | Payment method lifecycle hooks, payment method configuration, 3-D Secure, and IPN handlers. |
| `mage-module-product-types` | Bundle, configurable, grouped, and downloadable product behavior, pricing, options, selections, and add-to-cart shape. |
| `mage-module-promotions` | Catalog price rules, cart rules, conditions, actions, coupons, stop-rules-processing, and reindex requirements. |
| `mage-module-sales` | Quote, order, invoice, shipment, credit memo lifecycle, order states and statuses, totals collectors, and address conversion. |
| `mage-module-shipping-carriers` | Shipping carrier rate collection, tracking, system configuration, allowed countries and methods, and free-shipping interaction. |
| `mage-module-tax` | Tax classes, rules, rates, calculation algorithms, tax display configuration, fixed product tax, and inclusive catalog prices. |
| `openmage-acl-adminhtml` | Admin ACL resources, menu permissions, `adminhtml.xml`, and `_isAllowed()` checks. |
| `openmage-api-soap-rest` | SOAP, XML-RPC, REST, OAuth token flow, API ACL, and WSDL wiring. |
| `openmage-caching` | Cache types, cache tags, invalidation, block cache keys, lifetimes, and `Mage::app()->getCache()`. |
| `openmage-controllers-routing` | Frontend and admin controllers, router configuration, action methods, form-key validation, and layout rendering. |
| `openmage-db-setup-scripts` | Versioned install and upgrade scripts, schema changes, data changes, and EAV attribute setup. |
| `openmage-eav` | EAV entities, attributes, attribute sets, source/backend/frontend models, store scope, and EAV collections. |
| `openmage-events-observers` | Event dispatch, observer configuration, area scoping, observer method signatures, and common event names. |
| `openmage-indexers-cron` | Indexers, index events, reindex modes, cron jobs, schedule lifecycle, and CLI entry points. |
| `openmage-layout-blocks` | Layout XML, blocks, templates, theme fallback, escaping, layout handles, and PHPStan-safe templates. |
| `openmage-module-structure` | Module manifests, `config.xml`, class aliases, version bumps, area scoping, and alias resolution. |
| `openmage-system-config` | `system.xml`, configuration scopes, backend/source/frontend models, encrypted fields, config paths, and defaults. |
| `openmage-translations` | Locale CSV files, PHP and template translation helpers, JavaScript translations, and inline translation. |
| `phpstan-magento1` | PHPStan level 8, Magento alias resolution, strict-rules behavior, split baselines, and baseline regeneration. |
| `phpunit-openmage-tests` | OpenMage PHPUnit base classes, final tests, `#[Override]`, Subject aliases, data providers, and per-module suites. |
| `rector-openmage` | Rector presets, custom migration rules, skip list behavior, `#[Override]` enforcement, and Rector command usage. |
| `vendor-patches` | Composer dependency patch workflow, `patches.json`, `patches.lock.json`, and `.vendor-patches/`. |
