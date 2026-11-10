---
name: mage-module-product-types
description: OpenMage product types — Bundle, Configurable (+Swatches), Grouped, Downloadable. Option/selection/link/sample tables, dynamic vs fixed pricing, super-attributes, add-to-cart form shape per type. Use when editing under Mage_Bundle/Mage_Catalog (product types)/Mage_ConfigurableSwatches/Mage_Downloadable, building add-to-cart for composites, or debugging type-specific cart/order behavior.
---

# Mage Product Types

Composite / typed-pricing products in OpenMage. Simple/Virtual covered by `mage-module-catalog`; this skill is the four typed children. See also `mage-module-catalog`, `openmage-eav`.

## Type registry

`<global><catalog><product><type>` in each module's `config.xml` registers a `type_id` → model + composite/index flags. Resolve via `Mage_Catalog_Model_Product_Type::factory($product)`.

| type_id        | Model class                                          | Composite | Virtual-capable |
|----------------|------------------------------------------------------|-----------|-----------------|
| `simple`       | `Mage_Catalog_Model_Product_Type_Simple`             | no        | no              |
| `virtual`      | `Mage_Catalog_Model_Product_Type_Virtual`            | no        | yes             |
| `configurable` | `Mage_Catalog_Model_Product_Type_Configurable`       | yes       | yes (children)  |
| `grouped`      | `Mage_Catalog_Model_Product_Type_Grouped`            | yes       | no              |
| `bundle`       | `Mage_Bundle_Model_Product_Type`                     | yes       | yes (when all selections virtual) |
| `downloadable` | `Mage_Downloadable_Model_Product_Type` (ext Virtual) | no        | yes (always)    |

## Abstract — `Mage_Catalog_Model_Product_Type_Abstract`

Concrete types override these. Read it before patching cart behavior.

- `prepareForCart($buyRequest, $product)` → `prepareForCartAdvanced(..., processMode)` → `_prepareProduct()` → `_prepareOptions()`. Returns array of cart items (parent + children) or string error.
- `processBuyRequest($product, $buyRequest)` — extracts the canonical option payload from POST. Each type returns its own keys (`super_attribute`, `super_group`, `bundle_option*`, `links`).
- `getOrderOptions($product)` — what goes onto the *order item* (denormalized; survives child product deletion).
- `getRelationInfo()` — describes parent/child link tables for the indexer.
- `getChildrenIds($parentId, $required)` / `getParentIdsByChild($childId)` — used by stock/price/url indexers and `_filter` blocks.
- `getSku($product)` / `getOptionSku()` / `getWeight($product)`.
- `isVirtual()`, `isComposite()`, `isSalable()`, `hasOptions()`, `hasRequiredOptions()`, `canConfigure()`.
- Process modes: `PROCESS_MODE_FULL` (frontend buy), `PROCESS_MODE_LITE` (admin/wishlist; skips required-option enforcement).
- Calc constants: `CALCULATE_CHILD = 0`, `CALCULATE_PARENT = 1` — drives whether totals iterate parent or children.
- Shipment constants: `SHIPMENT_SEPARATELY = 1`, `SHIPMENT_TOGETHER = 0` (Bundle only setting today).
- Custom options on quote item: `addCustomOption('info_buyRequest', serialize(...))` — round-trip source of truth for re-add-to-cart.

## Configurable — `Mage_Catalog_Model_Product_Type_Configurable`

Parent has no own price; price = first/selected child's price. Children are independent simple products.

**Tables.**
| Table                                    | Role                                              |
|------------------------------------------|---------------------------------------------------|
| `catalog_product_super_attribute`        | which EAV attributes drive selection              |
| `catalog_product_super_attribute_label`  | per-store attribute label override                |
| `catalog_product_super_attribute_pricing`| optional per-option-value price delta (legacy)    |
| `catalog_product_super_link`             | parent product_id ↔ child product_id              |
| `catalog_product_relation`               | generic parent-child (also written by Grouped/Bundle) |

Resource model: `Mage_Catalog_Model_Resource_Product_Type_Configurable`. Attribute resource: `..._Configurable_Attribute`.

**Constraints.** Only attributes that are global-scope, visible, configurable, source-modeled, and user-defined can be super-attributes — `canUseAttribute()` enforces. Children must share the parent's attribute set (or compatible attribute set with the super-attributes). Children with `status=disabled` or out-of-stock are filtered by `getUsedProducts()`.

**Price.** `Mage_Catalog_Model_Product_Type_Configurable_Price`. Indexed by `Mage_Catalog_Model_Resource_Product_Indexer_Price_Configurable` from cheapest salable child + super-attribute pricing surcharges.

**Add-to-cart payload.**
```
super_attribute[<product_super_attribute_id>] = <option_value_id>
super_attribute[<product_super_attribute_id>] = <option_value_id>
qty = N
```
`processBuyRequest()` returns `['super_attribute' => [...]]`. `_prepareProduct()` resolves to the child via `getProductByAttributes()` and returns `[$parent, $child]` — both quote items, parent visible, child hidden (`getParentItem()`).

**ConfigurableSwatches.** `Mage_ConfigurableSwatches`. Hooks via `catalog_product_load_after`/`catalog_block_product_list_collection` observers; renders color/text swatches in PLP and PDP. No new tables — reads existing super-attribute config and attribute media galleries on child products via `Mage_ConfigurableSwatches_Helper_Mediafallback`.

## Grouped — `Mage_Catalog_Model_Product_Type_Grouped`

Parent is a *display group only*. No own price, no own SKU on the order, never added to cart itself. The form posts qty per associated child; each child becomes its own quote item.

**Tables.** Reuses `catalog_product_link` with `link_type_id = 3` (`Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED`) and `catalog_product_link_attribute*` for `position` / `qty`.

**Behavior.** `getRelationInfo()` returns the link table filtered by `link_type_id=3`. `_prepareProduct()` walks `super_group` and calls `$subProduct->getTypeInstance()->_prepareProduct()` for each child with qty > 0; the parent is *not* added to the cart (children carry `product_type=grouped` custom option referencing the group's id for display only).

**Add-to-cart payload.**
```
super_group[<child_product_id>] = <qty>
super_group[<child_product_id>] = <qty>
```
`processBuyRequest()` → `['super_group' => [...]]`.

## Bundle — `Mage_Bundle_Model_Product_Type`

Parent product with one or more **options** (groups), each containing **selections** (concrete simple/virtual products). Options have a UI input type and a required flag.

**Tables.**
| Table                              | Role                                               |
|------------------------------------|----------------------------------------------------|
| `catalog_product_bundle_option`    | option (group) per parent; `required`, `position`, `type` |
| `catalog_product_bundle_option_value` | per-store title for option                      |
| `catalog_product_bundle_selection` | option_id → child product_id; qty, price, can_change_qty |
| `catalog_product_bundle_selection_price` | per-website price overrides for fixed-price bundles |
| `catalog_product_bundle_stock_index` | stock rollup                                     |

**Option input types** (`Mage_Bundle_Model_Source_Option_Type`): `select`, `radio`, `checkbox`, `multi`. `select`/`radio` = single, `checkbox`/`multi` = multi. `Option::isMultiSelection()`.

**Pricing — `price_type` attribute on parent.**
- `PRICE_TYPE_DYNAMIC = 0`: parent has no stored price; cart price = sum of selection (child) prices × qty. Special price / tier price applied as a percent of the dynamic sum.
- `PRICE_TYPE_FIXED = 1`: parent's `price` is the base; each selection contributes a fixed delta from `catalog_product_bundle_selection_price`. Tier/special apply to the fixed parent price.

Constants in `Mage_Bundle_Model_Product_Price`. Same axis applies to `sku_type` and `weight_type` attributes (dynamic = derived from selections, fixed = use parent value).

**Dynamic SKU.** When `sku_type = DYNAMIC` (0), `getSku()` (`Mage_Bundle_Model_Product_Type::getSku`) returns `parent_sku-childA_sku-childB_sku` — `implode('-', [$parent, ...$selectionSkus])`. When fixed, parent SKU only.

**Dynamic weight.** Same pattern: dynamic sums salable selection weights.

**Shipment type.** `shipment_type` attribute (`SHIPMENT_TOGETHER` / `SHIPMENT_SEPARATELY`) — drives whether all selections ship as one parcel or each selection ships independently. Stored on order item options.

**Add-to-cart payload.**
```
bundle_option[<option_id>]      = <selection_id>      // single
bundle_option[<option_id>][]    = <selection_id>      // multi
bundle_option_qty[<option_id>]  = <qty>
qty = N
```
`processBuyRequest()` → `['bundle_option' => [...], 'bundle_option_qty' => [...]]`. `_prepareProduct()` validates required options, builds a parent quote item plus one hidden child quote item per selection. `bundle_option_ids` and `bundle_selection_ids` get serialized into custom options for re-order.

**Salability.** `isSalable()` overrides require *every required option* to have at least one salable selection (and respects child stock).

## Downloadable — `Mage_Downloadable_Model_Product_Type`

Extends `Mage_Catalog_Model_Product_Type_Virtual` (always virtual: no shipping, no weight). Adds **links** (purchaseable downloads) and **samples** (free previews).

**Tables.**
| Table                          | Role                                               |
|--------------------------------|----------------------------------------------------|
| `downloadable_link`            | per-product link: file/URL, price, max_downloads, is_shareable, sort_order |
| `downloadable_link_title`      | per-store title                                    |
| `downloadable_link_price`      | per-website price                                  |
| `downloadable_sample`          | per-product sample: file/URL, sort_order           |
| `downloadable_sample_title`    | per-store title                                    |
| `downloadable_link_purchased`  | one row per order (group of purchased links)       |
| `downloadable_link_purchased_item` | one row per purchased link, with download counter and access hash |

**File storage.** Files live on disk, only the relative path is in DB:
- Links: `media/downloadable/files/links/...` (`Mage_Downloadable_Model_Link::getBasePath()`)
- Link samples: `media/downloadable/files/link_samples/...` (`getBaseSamplePath()`)
- Samples: `media/downloadable/files/samples/...` (`Mage_Downloadable_Model_Sample::getBasePath()`)
- Tmp upload paths under `media/downloadable/tmp/...`; admin "Save" moves from tmp → permanent.

`Mage_Downloadable_Helper_Download::LINK_TYPE_FILE` vs `LINK_TYPE_URL` discriminates per-link.

**Shareable.** `Mage_Downloadable_Model_Link::LINK_SHAREABLE_{YES,NO,CONFIG}`. Non-shareable forces customer login before the download URL works.

**Pricing.** Parent product has its own price like a simple. If `links_purchased_separately = 1`, each selected link adds its own price on top of the parent. If `0`, all links are granted with the product at the parent price; customer doesn't pick.

**Add-to-cart payload.**
```
links[] = <link_id>
links[] = <link_id>
qty = N
```
`processBuyRequest()` returns `['links' => $links]` (overridden in `Mage_Downloadable_Model_Product_Type`). `_prepareProduct()` validates required link selection when `getLinksPurchasedSeparately()`, stores `downloadable_link_ids` as a comma-joined custom option on the quote item; virtuality is inherited from `Mage_Catalog_Model_Product_Type_Virtual::isVirtual()`. Order placement creates `downloadable_link_purchased` + `_item` rows; `Mage_Downloadable_Model_Observer` flips them to "available" on `sales_order_save_commit_after` once the order item reaches a configurable status (default `Mage_Sales_Model_Order_Item::STATUS_INVOICED = 9`).

**Download endpoint.** `Mage_Downloadable_DownloadController::linkAction` streams via `Mage_Downloadable_Helper_Download` and increments `number_of_downloads_used`.

## Cross-cutting

- **`info_buyRequest` custom option** is the canonical record of what the customer chose; preserved on quote item, copied to order item, used by re-order. Don't mutate after add-to-cart.
- **`product_type` custom option** on a child quote item identifies its parent type (`bundle`, `configurable`, `grouped`) — totals collectors and order printouts branch on it.
- **`getRelationInfo()`** is what lets `Mage_Catalog_Model_Indexer_Url`, the price indexer, and the stock indexer walk parent↔child without hardcoding type checks.
- **`canConfigure()`** controls "Edit Configuration" in cart. Configurable/Bundle/Grouped/Downloadable (when `linksPurchasedSeparately`) return true.
- **Adding a new type** = new model extending Abstract + `<global><catalog><product><type>` registration + price model + (usually) resource model + admin tab block. Don't subclass `Simple` — start from `Abstract` or `Virtual`.

## Cross-refs

- `mage-module-catalog` — product CRUD, indexers, URL rewrites, image cache.
- `openmage-eav` — super-attributes are EAV attributes; bundle `price_type`/`sku_type`/`weight_type`/`shipment_type` are EAV attributes too.
- `mage-module-sales` — quote item ↔ order item mapping, hidden child items, `getOrderOptions()` consumers.
- `mage-module-checkout` — `info_buyRequest` round-trip on edit-from-cart.
- `openmage-indexers-cron` — price/stock indexers all special-case these types.
