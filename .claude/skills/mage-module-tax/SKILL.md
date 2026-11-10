---
name: mage-module-tax
description: OpenMage tax — tax classes (product/customer), tax rules, tax rates, calculation algorithms (unit/row/total), tax-in-subtotal display matrix, FPT/Weee, catalog-prices-including-tax. Use when editing under app/code/core/Mage/Tax/ or app/code/core/Mage/Weee/, debugging tax calculation, configuring tax classes/rules, or wiring custom tax adjustments.
---

# Mage_Tax

Tax pipeline + Weee/FPT. Aliases `tax/*`, `weee/*`. Source: `app/code/core/Mage/Tax/`, `app/code/core/Mage/Weee/`. Config constants live on `Mage_Tax_Model_Config`; helper-level reads on `Mage_Tax_Helper_Data`.

## Tables and pipeline

```
tax_class (class_type=PRODUCT|CUSTOMER)
        │
        ├── product.tax_class_id ──────┐
        │                              │ joined per-quote-item
        ├── customer_group.tax_class_id┤
        │                              ▼
        │              tax_calculation_rule (priority, position, code)
        │                              │
        │                              ▼
        │              tax_calculation (rule_id, customer_tax_class_id,
        │                               product_tax_class_id, tax_calculation_rate_id)
        │                              │
        └──────────────────────────────▼
                       tax_calculation_rate (country, region_id, postcode, rate)
                       tax_calculation_rate_title (per-store label override)
```

`tax_calculation` is the **junction**: one row per `(rule, customer_class, product_class, rate)` tuple. Resolution given a request `(country/region/postcode, customer_class_id, product_class_id)` walks `tax_calculation` → returns matching rate rows ordered by rule `priority` (rates with the same priority sum; different priorities compound when rule's `calculate_subtotal` is on). See `Mage_Tax_Model_Resource_Calculation::_getRates` and `getCalculationProcess`.

Class types are an enum on `Mage_Tax_Model_Class`:

```php
public const TAX_CLASS_TYPE_CUSTOMER = 'CUSTOMER';
public const TAX_CLASS_TYPE_PRODUCT  = 'PRODUCT';
```

Shipping has its own product tax class — `tax/classes/shipping_tax_class` (`Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS`). The "None" product tax class (id 0) short-circuits to no tax.

## Calculation request

`Mage_Tax_Model_Calculation::getRateRequest($shipping, $billing, $customerTaxClass, $store)` builds a `Varien_Object` with `country_id`, `region_id`, `postcode`, `customer_class_id`, `store`. The `tax/calculation/based_on` config (`shipping`/`billing`/`origin`/`default`) selects which address. Falls back through default-billing → default-shipping → store-default → `tax/defaults/{country,region,postcode}` if the chosen address lacks a country.

`getRate($request)` (request also carries `product_class_id`) returns the summed/compounded percent. `getAppliedRates($request)` returns the per-rate breakdown used to build `applied_taxes` rows.

`Mage_Tax_Model_Calculation::calcTaxAmount($price, $taxRate, $priceIncludeTax = false, $round = true)` is the primitive (`$taxRate` is the percent — divided by 100 internally):

```php
$taxRate /= 100;
$amount = $priceIncludeTax ? $price * (1 - 1 / (1 + $taxRate)) : $price * $taxRate;
```

Use this — don't reinvent. `round()` defers to `Mage::app()->getStore()->roundPrice()` (half-up, two decimals).

## Algorithms

`tax/calculation/algorithm` (`Mage_Tax_Model_Config::XML_PATH_ALGORITHM`). Constants on `Mage_Tax_Model_Calculation`:

| Constant | Value | Semantic |
|---|---|---|
| `CALC_UNIT_BASE` | `UNIT_BASE_CALCULATION` | tax = round(unitPrice × rate) × qty. Each item taxed independently per unit, then multiplied. Smallest rounding granularity. |
| `CALC_ROW_BASE` | `ROW_BASE_CALCULATION` | tax = round((unitPrice × qty) × rate). Rounds once per line. |
| `CALC_TOTAL_BASE` | `TOTAL_BASE_CALCULATION` (default) | tax = round(Σ(rowTotal) × rate) per rate. Rounds once per address per rate, after summing all matching items. EU-friendly. |

The dispatch is in `Mage_Tax_Model_Sales_Total_Quote_Tax::collect()` (around lines 184–196):

```php
case CALC_UNIT_BASE:  $this->_unitBaseCalculation(...);
case CALC_ROW_BASE:   $this->_rowBaseCalculation(...);
case CALC_TOTAL_BASE: $this->_totalBaseCalculation(...);
```

Switching algorithm changes line-level vs cart-level totals by cents — invoices issued under one algorithm aren't safely re-totaled under another. Treat as set-once-per-store.

Rounding deltas: `_deltaRound` accumulates fractional rounding errors per rate per `$type` (`regular`/`base`/`tax_before_discount`/`tax_before_discount_base`, plus per-direction `incl`/`excl` suffix) and folds them into the next round so a 5×$0.999 line doesn't lose a cent.

## Totals chain

Registered in `Mage_Tax/etc/config.xml` under `<global><sales><quote><totals>`:

```xml
<tax_subtotal>  <after>subtotal,nominal,shipping,freeshipping</after> <before>tax,discount</before>
<tax_shipping> <after>shipping,tax_subtotal</after>                  <before>tax,discount</before>
<tax>          <after>subtotal,shipping</after>                       <before>grand_total</before>
```

Three collectors, one module:

- `tax/sales_total_quote_subtotal` — recomputes `subtotal`/`base_subtotal` and `subtotal_incl_tax`/`base_subtotal_incl_tax` based on the **catalog-prices-include-tax** flag, before discount runs.
- `tax/sales_total_quote_shipping` — splits shipping into pre-tax / tax / incl-tax components.
- `tax/sales_total_quote_tax` — main collector. Walks items, applies algorithm, fills `tax_amount`, `applied_taxes`, `hidden_tax_amount` (the "phantom" tax already in the displayed price when prices include tax but customer rate differs from store rate).

`Mage_Weee` (FPT) registers `weee/total_quote_weee` with `<after>subtotal,tax_subtotal</after> <before>shipping,tax,discount</before>` — sits **between** subtotal and tax, so FPT amounts are themselves taxable when configured to be (`FPT_TAXED`). Weee writes `weee_tax_applied`, `weee_tax_applied_amount`, `weee_tax_applied_row_amount` plus `*_disposition` rows onto items.

Item field map after tax collection:

```
row_total           — pre-tax line total
tax_amount          — tax portion
discount_amount     — pre-tax (or post-tax, see apply_after_discount)
hidden_tax_amount   — tax that was inside the catalog price but isn't owed at this destination
row_total_incl_tax  — display: row_total + tax_amount
price_incl_tax      — per-unit equivalent (used during conversion to order item)
weee_tax_applied_row_amount  — FPT
```

The `<fieldsets>` block in `Tax/etc/config.xml` is what carries `*_incl_tax` and FPT fields across `quote → order → invoice → shipment → creditmemo` during conversion (`sales_convert_*`). Adding a new tax-related column means extending the fieldset, not just the schema.

## Catalog prices including tax

`tax/calculation/price_includes_tax` (`Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX`). Read via `Mage_Tax_Helper_Data::priceIncludesTax($store)` which primarily delegates to `Mage_Tax_Model_Config::priceIncludesTax` (with a fallback to `getNeedUseShippingExcludeTax()`). There's a separate `_needUsePriceExcludeTax` runtime flag on `Mage_Tax_Model_Config` that the `tax_subtotal` collector flips to read the raw catalog price during sub-total recomputation — never set this from outside the collector.

When **on**:

- Catalog prices in `catalog_product_entity_decimal` are stored gross (incl. store tax at `tax/defaults/*`).
- Reading the price for a customer at a different rate requires **stripping** the store's tax then **re-applying** the customer's tax. `Mage_Tax_Helper_Data::getPrice` (and downstream `getProductWeeeAttributes`) handle the strip/re-apply; the leftover difference is `hidden_tax_amount`.
- Cross-border-trade (`tax/calculation/cross_border_trade_enabled`, `CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED`) **disables** the strip/re-apply: gross price stays fixed regardless of destination rate, and the tax line absorbs the difference. Use this when the catalog price is the legal price (EU "fixed shelf price").
- Without CBT, two customers see different gross prices for the same product if rates differ.

`tax/calculation/shipping_includes_tax` is the analogue for carrier rates and threads through `_calculateShippingTax` / `_calculateShippingTaxByRate`.

## Display matrix

Three independent display contexts, each with its own setting:

| Context | Config path | Helper |
|---|---|---|
| Catalog | `tax/display/type` (`CONFIG_XML_PATH_PRICE_DISPLAY_TYPE`) | `displayPriceIncludingTax()` / `displayPriceExcludingTax()` / `displayBothPrices()` |
| Cart / checkout | `tax/cart_display/{price,subtotal,shipping,discount,grandtotal,full_summary,zero_tax}` | `displayCartPriceInclTax()` etc. |
| Order / invoice / pdf | `tax/sales_display/{...}` (same keys) | `displaySalesPriceInclTax()` etc. |

Display constants (`Mage_Tax_Model_Config`):

```php
DISPLAY_TYPE_EXCLUDING_TAX = 1
DISPLAY_TYPE_INCLUDING_TAX = 2
DISPLAY_TYPE_BOTH          = 3
```

"Tax in subtotal" = `tax/cart_display/subtotal` (and `tax/sales_display/subtotal`). Display-only — the underlying `subtotal` / `subtotal_incl_tax` columns are always written. Switching it never re-runs collection; it only flips which column the renderer reads. The `tax/checkout_subtotal` and `tax/checkout_grandtotal` totals **renderers** registered under `<sales><quote><totals>` (with `admin_renderer` siblings) implement that switch.

Edge cases:

- `cart_display/full_summary=1` shows each per-rate row (using `applied_taxes`); `=0` shows one combined "Tax" line.
- `cart_display/zero_tax=0` hides the tax row when `tax_amount === 0.0` even if `full_summary` would show it.
- `cart_display/grandtotal=1` switches to "Grand Total (Excl. Tax)" + "Grand Total (Incl. Tax)" rendering.

## Apply-after-discount and discount-on-prices

Two flags interacting:

- `tax/calculation/apply_after_discount` (`CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT`, 0/1).
- `tax/calculation/discount_tax` (`CONFIG_XML_PATH_DISCOUNT_TAX`, 0/1 — does the discount itself act on incl-tax or excl-tax price).

Combined identifier on `Mage_Tax_Model_Calculation`:

```php
CALC_TAX_BEFORE_DISCOUNT_ON_EXCL = '0_0'
CALC_TAX_BEFORE_DISCOUNT_ON_INCL = '0_1'
CALC_TAX_AFTER_DISCOUNT_ON_EXCL  = '1_0'
CALC_TAX_AFTER_DISCOUNT_ON_INCL  = '1_1'
```

US norm: `0_0` (tax then discount, on excl). EU norm: `1_1` (discount then tax, both gross). Mismatches between this and `discount_tax` produce off-by-cents on every coupon and are usually merchant-config bugs, not code bugs.

## FPT / Weee

`Mage_Weee` adds **fixed** per-product fees (e.g. battery levy, eco tax) via an EAV attribute of `frontend_input=weee` (see `Mage_Weee_Model_Attribute_Backend_Weee_Tax`). Each weee attribute carries `(country, state, value, website_id)` rows in `weee_tax` (`state` defaults to '*').

Per-store FPT modes (`tax/weee/`):

| Constant | Behavior |
|---|---|
| `FPT_NOT_TAXED = 0` | FPT shown but not added to taxable base. |
| `FPT_TAXED = 1` | FPT added to taxable base **before** tax is applied. |
| `FPT_LOADED_DISPLAY_WITH_TAX = 2` | FPT value already includes its tax; displayed gross. |

Display modes (per-context: catalog/cart/sales/email) on `Mage_Weee_Model_Tax`:

```php
DISPLAY_INCL            = 0  // include FPT in price, no separate line
DISPLAY_INCL_DESCR      = 1  // include in price + show description
DISPLAY_EXCL_DESCR_INCL = 2  // separate line, final price includes
DISPLAY_EXCL            = 3  // separate line, final price excludes
```

Discounting interaction is governed by `tax/weee/discount` (`Mage_Weee_Helper_Data::isDiscounted`) — when off, FPT bypasses cart rules.

`Mage_Tax_Model_Sales_Total_Quote_Tax::_calculateWeeeAmountInclTax` / `_calculateWeeeTax` / `_calculateRowWeeeTax` are the entry points where FPT enters the per-algorithm tax math.

## Common pitfalls

- Adding a new product tax class via raw insert into `tax_class` orphans the rules — register via setup script and update `tax_calculation` mappings, or it never participates in calculation.
- Calling `Mage::getModel('tax/calculation')->getRate(...)` with `product_class_id = 0` always returns `0` — that's the "None" sentinel.
- After changing rates/rules, `tax_calculation` and the catalog-price-rule index both need refresh; `tax_calculation` is read at request time (no reindex). Catalog rules do need their own reindex.
- Multishipping splits the cart across shipping addresses; the tax collector runs **per address** — a custom tax-touching collector that reads `$quote` instead of `$address` will double-count.
- The `hidden_tax_amount` field is non-zero only when catalog prices include tax and the customer's rate differs from the store default; if you see unexpected hidden-tax rows, check the rate request's `country_id` resolution.
- `Mage_Weee_Model_Tax::getProductWeeeAttributes` re-reads the rate via `tax/calculation` per call — cache at the caller level for hot paths (collection load).
- Order conversion drops any custom item field unless declared in the `sales_convert_quote_item` / `sales_convert_order_item` fieldset in `Tax/etc/config.xml` (or your own module). Custom tax columns need a fieldset entry.

## Cross-refs

- `mage-module-sales` — totals collectors slot into the quote totals chain; `applied_taxes` and `*_incl_tax` ride conversion via `<fieldsets>`.
- `mage-module-catalog` — `tax_class_id` is a product attribute; price reads go through `Mage_Tax_Helper_Data::getPrice` when catalog prices include tax.
- `openmage-system-config` — every behavior here is a `tax/*` config field; constants on `Mage_Tax_Model_Config`.
- `openmage-events-observers` — `tax/observer` reacts to `sales_quote_collect_totals_before`, `catalog_product_collection_load_after`, `catalog_prepare_price_select`, `sales_order_save_after`.
- `mage-module-promotions` — discount-vs-tax ordering is governed by `apply_after_discount` + `discount_tax`; salesrule collector slots adjacent to the tax collectors.
