---
name: mage-promotions
description: Magento 1 promotions — Mage_CatalogRule (catalog price rules) vs Mage_SalesRule (cart rules), Mage_Rule library (condition/action DSL), coupon generation, stop-rules-processing, reindex requirements. Use when editing under CatalogRule/, SalesRule/, or Rule/, adding a price rule, debugging rule application, or generating coupons.
---

# Mage Promotions

Two rule engines, one shared DSL.

- `Mage_CatalogRule` — **catalog price rules**. Rewrite product price. Evaluated at index time, read on the storefront from a precomputed table.
- `Mage_SalesRule` — **cart price rules** (a.k.a. shopping cart rules). Discounts, free shipping, coupon-gated promos. Evaluated at checkout against the quote.
- `Mage_Rule` — shared library: condition tree + action collection + serialization. Both engines extend it.

Aliases owned: `catalogrule/*`, `salesrule/*`, `rule/*`.

## Catalog vs cart — the key split

| Aspect | `Mage_CatalogRule` | `Mage_SalesRule` |
|---|---|---|
| Affects | Product price (catalog) | Cart totals (quote) |
| Evaluated when | Index build (cron / save) | Every quote total collection |
| Read at runtime via | `catalogrule_product_price` table | `Mage_SalesRule_Model_Validator` |
| Conditions match | `Mage_Catalog_Model_Product` | quote address + items |
| Coupons | none | `Mage_SalesRule_Model_Coupon*` |
| Indexer | `catalog_product_price` (and `catalogrule_product` build tables) | none — runtime only |
| Cron job | `catalogrule_apply_all` (daily 01:00) | none |

Catalog rule changes are **not** visible until the catalog rule build runs and the price index reindexes. The admin save flow does this for you; programmatic changes do not — see "Reindex requirements" below.

## The `Mage_Rule` DSL

Every rule has two trees, both saved as PHP-serialized arrays in `*_serialized` columns.

- `conditions_serialized` → tree of `Mage_Rule_Model_Condition_Combine` (root) → `Combine | Condition_Abstract` (recursive). The root combine has an aggregator (`all`/`any`) and a value (`true`/`false`); each leaf is a `field operator value` triple.
- `actions_serialized` → flat `Mage_Rule_Model_Action_Collection`. SalesRule uses it as a *second* condition tree ("Apply the rule only to cart items matching..."). CatalogRule actions are simpler.

Serialization happens in `Mage_Rule_Model_Abstract::_beforeSave()`:

```php
$this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
$this->setActionsSerialized(serialize($this->getActions()->asArray()));
```

Reading goes through `Mage::helper('core/unserializeArray')->unserialize()` — **safe unserialize**; only scalars and arrays. Don't stuff objects into a rule tree; they won't survive a round trip.

Subclasses must implement `getConditionsInstance()` and `getActionsInstance()` — they return the appropriate root combine / action collection for the rule type. CatalogRule returns `catalogrule/rule_condition_combine`; SalesRule returns `salesrule/rule_condition_combine` (with separate `salesrule/rule_condition_product_combine` for the actions tree).

`validate(Varien_Object $object)` on the root combine walks the tree, short-circuiting on `all`/`any`.

## Catalog price rules

`Mage_CatalogRule_Model_Rule extends Mage_Rule_Model_Abstract`.

- `simple_action` ∈ `to_percent | by_percent | to_fixed | by_fixed`.
- `discount_amount` is the operand.
- Sub-action (`sub_simple_action`/`sub_discount_amount`) applies to children of configurable/bundle products.
- `from_date` / `to_date` gate activation; `customer_group_ids` and `website_ids` scope it.
- `sort_order` controls evaluation order; `stop_rules_processing` halts further rules for the matched product (see below).

Application pipeline (`Mage_CatalogRule_Model_Rule::applyAll`):

1. Walk all rules, write matched `(rule_id, product_id, website_id, customer_group_id, from_time, to_time, action_*, sort_order)` rows to `catalogrule_product`.
2. `applyAllRules()` rolls those rows up by date into `catalogrule_product_price` (effective price per product / website / customer group / date).
3. Invalidates the cache types listed under `<global><catalogrule><related_cache_types>`.
4. Triggers `catalog_product_price` reindex.

Storefront price lookup goes through `calcProductPriceRule()` → `getRulesFromProduct()` → reads `catalogrule_product_price`. **Nothing happens at request time** beyond a table read.

`catalogrule_apply_all` cron (`0 1 * * *`) re-runs daily so date-bounded rules activate/deactivate without a manual reindex — but only after the cron fires.

## Cart price rules

`Mage_SalesRule_Model_Rule extends Mage_Rule_Model_Abstract`.

- `simple_action` constants: `TO_PERCENT_ACTION`, `BY_PERCENT_ACTION`, `TO_FIXED_ACTION`, `BY_FIXED_ACTION`, `CART_FIXED_ACTION`, `BUY_X_GET_Y_ACTION`.
- `coupon_type` ∈ `COUPON_TYPE_NO_COUPON (1)`, `COUPON_TYPE_SPECIFIC (2)`, `COUPON_TYPE_AUTO (3)`.
- `simple_free_shipping` ∈ `FREE_SHIPPING_ITEM (1)`, `FREE_SHIPPING_ADDRESS (2)`.
- Per-rule throttles: `uses_per_coupon`, `uses_per_customer`.
- Two trees: `conditions` (does the address/quote match?) and `actions` (which items get the discount?).

Application (`Mage_SalesRule_Model_Validator`) is invoked by quote address total collectors (`Mage_SalesRule_Model_Quote_Discount`, `Freeshipping`). For each item, `Validator::process()` walks active rules in `sort_order`, validates rule conditions against the address, validates the actions tree against the item, then mutates `discount_amount` / `base_discount_amount` / `applied_rule_ids` on the item, address, and quote.

## Stop-rules-processing and priority

- `sort_order` (lower runs first) determines evaluation order. **It is not a "priority" in the lockout sense** — every matching rule runs unless explicitly stopped.
- `stop_rules_processing = 1` on a rule that matched halts further rules. Three independent loops in `Validator.php` (`processFreeShipping`, item discount `process`, address-level totals) each check `$rule->getStopRulesProcessing()` and `break` out.
- Catalog rules have the same flag (`action_stop` column on `catalogrule_product`); `calcProductPriceRule()` `break`s when it sees one.
- Combined with sort_order: order rules by exclusivity, set stop on the "winning" rule. Two rules with the same sort_order have undefined relative order.

## Coupon generation

`Mage_SalesRule_Model_Coupon_Massgenerator` (singleton, `salesrule/coupon_massgenerator`).

Inputs (data keys on the model): `rule_id`, `qty`, `length`, `format` (alphanumeric / alphabetical / numeric — see `Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_*`), `prefix`, `suffix`, `dash` (split every N chars), `uses_per_coupon`, `uses_per_customer`, `to_date`.

Algorithm:

1. Compute charset size from `format`. Compute probability `qty / charset^length`.
2. If probability > `MAX_PROBABILITY_OF_GUESSING (0.25)`, **auto-grow `length`** until below threshold. The model rewrites its own `length` via `setLength()`.
3. For each of `qty` codes: pick chars with `random_int`, splice in delimiter every `dash` chars, retry up to `MAX_GENERATE_ATTEMPTS (10)` if the resource layer reports collision via `exists($code)`.
4. Save each as a `salesrule/coupon` row with type `COUPON_TYPE_SPECIFIC_AUTOGENERATED`, parent `rule_id`, usage limits, expiration.

`generateCode()` returns one code; `generatePool()` writes `qty` rows. `validateData($data)` is the admin-form gate.

The rule must have `coupon_type = COUPON_TYPE_SPECIFIC` and `use_auto_generation = 1` to belong to a generated pool. The "primary" coupon (single hardcoded code on the rule itself) is in `Mage_SalesRule_Model_Coupon` and is loaded in `Rule::_afterLoad()`.

## Reindex requirements

After a catalog rule change:

- Admin save flow: `Mage_CatalogRule_Model_Rule::applyAll()` runs synchronously and triggers `catalog_product_price` reindex. Observers handle it.
- Programmatic save (e.g. from a setup script or a script that calls `$rule->save()`): you **must** call `Mage::getModel('catalogrule/rule')->applyAll()` yourself, or the rule is in the table but never compiled into `catalogrule_product_price`. Storefront prices won't reflect it.
- Daily cron (`catalogrule_apply_all`) reapplies all rules at 01:00 — fixes date-bounded rules going live overnight, but is too slow for "I just edited a rule and want it live now."
- Manual fallback: `php shell/indexer.php --reindex catalog_product_price` (after `applyAll()` ran).

After a cart rule change: nothing to reindex. Next quote totals collection picks it up. Cache busting on `salesrule_rule_save_commit_after` clears block_html / config caches as needed.

**Pitfall (catalog).** Editing a CatalogRule row directly in the DB or via a script that bypasses `Mage_CatalogRule_Model_Rule::save()` leaves the index stale forever. Always go through the model, then `applyAll()`. Same applies to bulk imports — don't write `catalogrule` rows directly.

## See also

- `mage-catalog` — the price index (`catalog_product_price`) that catalog rules feed; product collection joins for matched-by-rule.
- `mage-sales` — quote totals collection order; `Mage_SalesRule_Model_Quote_Discount` runs as one of the totals.
- `m1-indexers-cron` — `catalog_product_price` indexer mechanics, `catalogrule_apply_all` cron job, `index_event` lifecycle.
- `m1-events-observers` — `catalogrule_rule_save_commit_after`, `salesrule_rule_save_commit_after`, `catalog_product_get_final_price`.
