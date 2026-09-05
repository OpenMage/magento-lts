---
name: mage-module-catalog
description: OpenMage Mage_Catalog — products (simple/virtual/configurable/bundle/grouped/downloadable), categories, URL rewrites, layered nav, flat tables, image cache, indexing chain. Use when editing under app/code/core/Mage/Catalog/, working with catalog/* aliases (catalog/product, catalog/category, catalog/url), adding product attributes, or debugging URL rewrites or layered nav.
---

# mage-module-catalog

Catalog is the largest OpenMage module and the EAV poster child. Almost every other commerce module hangs off it: sales/quote items reference `catalog/product`, promotions index against catalog rules, search reads from the catalog flat tables. Edits here ripple — be deliberate.

This skill covers the things you need to know to safely edit `Mage_Catalog`. For framework mechanics it leans on:
- `openmage-eav` — attributes, source/backend models, the flat-table read switch
- `openmage-indexers-cron` — `Mage_Index` event log, reindex modes, cron declarations
- `mage-module-product-types` — Bundle, Configurable+Swatches, Grouped, Downloadable internals

## Aliases owned

`catalog/*`. The big ones:

- `catalog/product`, `catalog/product_type`, `catalog/product_type_*` (simple, virtual, configurable, grouped — Bundle/Downloadable live in their own modules)
- `catalog/category`, `catalog/category_flat`
- `catalog/url` — URL rewrite generator
- `catalog/layer`, `catalog/layer_filter_*` — layered navigation
- `catalog/product_image`, `catalog/image` (helper) — image cache pipeline
- `catalog/observer` — central observer class for the module's own events

## Product model

`Mage_Catalog_Model_Product` (`app/code/core/Mage/Catalog/Model/Product.php`):

```php
public const ENTITY    = 'catalog_product';
public const CACHE_TAG = 'catalog_product';
protected $_eventPrefix = 'catalog_product';
protected $_eventObject = 'product';
```

So save/load fire `catalog_product_save_before/after`, `catalog_product_load_after`, etc., with `['product' => $this]`. On save the model self-registers a reindex via `$indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE)`.

`Mage::getModel('catalog/product')->load($id)` is store-scoped — pass `setStoreId()` before `load()` to read non-default store values, otherwise you get default scope.

## Product types

Type constants on `Mage_Catalog_Model_Product_Type`:

| Type | Constant | Type model alias | Module |
|---|---|---|---|
| Simple | `TYPE_SIMPLE = 'simple'` | `catalog/product_type_simple` | Mage_Catalog |
| Virtual | `TYPE_VIRTUAL = 'virtual'` | `catalog/product_type_virtual` | Mage_Catalog |
| Configurable | `TYPE_CONFIGURABLE = 'configurable'` | `catalog/product_type_configurable` | Mage_Catalog |
| Grouped | `TYPE_GROUPED = 'grouped'` | `catalog/product_type_grouped` | Mage_Catalog |
| Bundle | `TYPE_BUNDLE = 'bundle'` | `bundle/product_type` | Mage_Bundle |
| Downloadable | `TYPE_DOWNLOADABLE = 'downloadable'` | `downloadable/product_type` | Mage_Downloadable |

Types are registered under `<global><catalog><product><type>` in each module's `etc/config.xml`. Each entry has `<label>`, `<model>`, `<composite>` (1 = has children/options), `<index_priority>`, optional `<allow_product_types>` for parent linkage. `Mage_Catalog_Model_Product_Type::factory($product)` reads `type_id` and instantiates the matching type model.

Composite types: configurable, grouped, bundle. They wrap child simples; `getChildrenIds()` / `getParentIdsByChild()` on the type model are how relationships are resolved. Configurable's super-attribute mapping lives in `catalog_product_super_*` tables; Bundle uses `catalog_product_bundle_*`; Grouped uses the generic `catalog_product_link` table with link type 3.

ConfigurableSwatches (`Mage_ConfigurableSwatches`) is a separate module that observes catalog product/list events to decorate configurable products with swatch images and a colored option pickers — see `Mage_ConfigurableSwatches/etc/config.xml` events block. Touch points: it overrides product blocks via layout and hooks `catalog_block_product_list_collection`. The module ships no setup scripts; it reuses media-gallery via `Mage_ConfigurableSwatches_Helper_Mediafallback`.

## Category model

`Mage_Catalog_Model_Category` (`app/code/core/Mage/Catalog/Model/Category.php`):

```php
public const ENTITY        = 'catalog_category';
public const TREE_ROOT_ID  = 1;
public const CACHE_TAG     = 'catalog_category';
protected $_eventPrefix = 'catalog_category';
protected $_eventObject = 'category';
```

Tree mechanics use materialized path on `catalog_category_entity`:

- `path` — slash-delimited entity IDs from root, e.g. `1/2/45/123`. The category is its own last segment.
- `level` — int; equals `count(explode('/', $path)) - 1`. Root is level 0; default store root is level 1.
- `position` — int sort within parent.
- `parent_id` — denormalized parent (also the second-to-last segment of `path`).
- `children_count` — count of all descendants.

`TREE_ROOT_ID = 1` is the global root; each store's "Root Category" is a level-1 child of it. Don't move categories by hand — use `Mage_Catalog_Model_Category::move()` so `path`, `level`, and `position` stay consistent and `catalog_category_move_before/after` fire.

**Anchor flag** (`is_anchor` int attribute): when set, a category aggregates products from all descendant categories into its layered-nav listing. Anchor membership is resolved through the `catalog_category_product` indexer into `catalog_category_product_index`. Toggling anchor invalidates layered-nav and category-product indexes.

## URL rewrites

Table: `core_url_rewrite` (resource `core/url_rewrite`). Columns of interest: `request_path`, `target_path`, `is_system`, `category_id`, `product_id`, `store_id`, `id_path` (e.g. `category/45`, `product/123/45`). Catalog-managed rows are `is_system=1`; merchant-created redirects go through `Mage_Core_Model_Url_Rewrite` directly with `is_system=0`.

`Mage_Catalog_Model_Url` (`app/code/core/Mage/Catalog/Model/Url.php`) maintains catalog rows. Entry points:

- `refreshRewrites($storeId = null)` — full rebuild for a store (or all stores). Walks the category tree from each store root, then refreshes products. This is what the `catalog_url` indexer calls for a "Reindex Data" action.
- `refreshCategoryRewrite($categoryId, $storeId = null, $refreshProducts = true)` — single category branch.
- `refreshProductRewrite($productId, $storeId = null)` — single product across visible categories.
- `refreshProductRewrites($storeId)` — all products in a store.

When the URL refresh runs:

1. **Save observer.** Product/category save fires the `catalog_url` indexer event; if the indexer is real-time, rewrites refresh immediately. If manual, an `index_event` row queues until reindex.
2. **Admin "Reindex Data".** System → Index Management → Catalog URL Rewrites runs `refreshRewrites()`.
3. **Cron / shell.** `shell/indexer.php --reindex catalog_url`. There is no dedicated cron job for URL rewrite reindex by default — only `catalog_product_index_price_reindex_all` runs nightly (see `etc/config.xml` `<crontab>`).

URL suffix (`.html`) is config: `catalog/seo/product_url_suffix`, `catalog/seo/category_url_suffix`. Changing them invalidates the rewrite index and requires a refresh.

Pitfall: bulk product attribute updates that don't go through `Mage_Catalog_Model_Product::save()` won't trigger reindex. Either dispatch `catalog_product_save_after` manually or call `Mage::getSingleton('index/indexer')->processEntityAction(...)`.

## Indexing chain

Catalog registers six indexers under `<global><index><indexer>` in `etc/config.xml`:

```xml
<catalog_product_attribute>  catalog/product_indexer_eav
<catalog_product_price>      catalog/product_indexer_price
<catalog_url>                catalog/indexer_url
<catalog_product_flat>       catalog/product_indexer_flat
<catalog_category_flat>      catalog/category_indexer_flat
<catalog_category_product>   catalog/category_indexer_product
```

Plus two more registered by sibling modules that reindex against catalog data:

```xml
<cataloginventory_stock>     cataloginventory/indexer_stock   (Mage_CatalogInventory)
<catalogsearch_fulltext>     catalogsearch/indexer_fulltext   (Mage_CatalogSearch)
```

Dependency-aware order when reindexing all (the order admin runs them, roughly):

1. `catalog_product_attribute` (EAV index → `catalog_product_index_eav*`)
2. `catalog_product_price` (price index → `catalog_product_index_price*`)
3. `catalog_url` (rewrites → `core_url_rewrite`)
4. `catalog_product_flat` (flat product → `catalog_product_flat_<store_id>`)
5. `catalog_category_flat` (flat category → `catalog_category_flat_store_<store_id>`)
6. `catalog_category_product` (anchor/product membership → `catalog_category_product_index`)
7. `catalogsearch_fulltext` (fulltext → `catalogsearch_fulltext`)
8. `cataloginventory_stock` (stock status → `cataloginventory_stock_status*`)

Real-time indexers update on save; manual indexers queue rows in `index_event` until a reindex runs. Toggle in System → Index Management. See `openmage-indexers-cron` for the `Mage_Index` event log mechanics and the matrix code.

## Flat tables vs EAV

When "Use Flat Catalog Product/Category" is enabled (`catalog/frontend/flat_catalog_product`, `catalog/frontend/flat_catalog_category`):

- `Mage_Catalog_Model_Resource_Product_Collection` swaps its main table to `catalog_product_flat_<store_id>` on the **frontend only**. Admin still reads EAV.
- `addAttributeToSelect('foo')` reads `foo` directly from the flat column — fast — **but only if the attribute is flagged "Used in Product Listing" or "Used for Sorting"** so the flat indexer included it. Attributes without those flags fall back to a JOIN into `catalog_product_entity_*` and silently work in admin while erroring on frontend.
- `Mage_Catalog_Model_Category::_construct()` checks `Mage_Catalog_Helper_Category_Flat::isAccessible()` and switches the resource model to `catalog/category_flat`. Same frontend-only behavior.

When you add a product attribute, decide whether it's needed in listings. If yes, set `used_in_product_listing = 1` in the EAV setup call and reindex `catalog_product_flat`. See `openmage-eav` for the full flat pitfall.

Don't write to flat tables directly. They are derived. Edit EAV → reindex.

## Layered navigation

Frontend block tree under `Mage_Catalog_Block_Layer_View` renders the sidebar. Each filter is a child block:

- `Mage_Catalog_Block_Layer_Filter_Category` — subcategory drill-down
- `Mage_Catalog_Block_Layer_Filter_Attribute` — generic attribute filters (driven by `is_filterable` on the attribute)
- `Mage_Catalog_Block_Layer_Filter_Price` — price-bucket filter
- `Mage_Catalog_Block_Layer_Filter_Decimal` — numeric range filter

Each block delegates to its `Mage_Catalog_Model_Layer_Filter_*` peer, which builds the actual SQL against the EAV/price index and the optional category-product index. State is held in `Mage_Catalog_Model_Layer_State` and serialized to query string.

For an attribute to appear, its admin "Use in Layered Navigation" must be set and the attribute must be reindexed (`catalog_product_attribute`). Decimal/price filters require the price indexer to be current.

## Image cache pipeline

Entry point is the helper, used in templates:

```php
echo Mage::helper('catalog/image')->init($product, 'small_image')->resize(200);
```

`Mage_Catalog_Helper_Image::init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)` configures a `Mage_Catalog_Model_Product_Image` with the source attribute (`image`, `small_image`, `thumbnail`, or any media-image attribute). Chainable setters: `resize($w, $h)`, `keepAspectRatio($flag)`, `keepFrame($flag)`, `keepTransparency($flag)`, `constrainOnly($flag)`, `backgroundColor([r,g,b])`, `watermark(...)`. The helper implements `Stringable` — calling it in string context (`(string)$img` or echo) triggers `__toString()` which generates the URL and writes the file if missing.

Cache lives under `media/catalog/product/cache/<store_id>/<attr>/<dimensions>/.../<filename>`. Cache key includes every transform parameter so two different `resize(200)` vs `resize(200,200)` calls produce different files. Wiping `media/catalog/product/cache/` is safe; everything regenerates lazily on next request.

The originals live at `media/catalog/product/<f>/<o>/<filename>` (`Varien_File_Uploader::getDispretionPath()` creates one directory per character).

## Common edits

**Add a product attribute.** EAV setup script under `Catalog/sql/catalog_setup/upgrade-X.Y.Z-X.Y.Z+1.php` calling `$installer->addAttribute('catalog_product', 'my_attr', [...])`. Bump `<modules><Mage_Catalog><version>` — **don't** bump core's version; create your own module if attributes belong to a third-party feature. If listing-visible, set `used_in_product_listing => 1` and reindex `catalog_product_flat`. See `openmage-eav` and `openmage-db-setup-scripts`.

**Add a category attribute.** Same flow with entity `catalog_category`. Reindex `catalog_category_flat`.

**Override a product type.** Don't subclass `Mage_Catalog_Model_Product_Type_Simple` — register a new type under `<global><catalog><product><type>` with a new id and your own type model. See `mage-module-product-types`.

**Add a layered-nav filter for a custom attribute.** Set `is_filterable=1` on the attribute (1 = with results, 2 = without results). Attribute must be of input type `select`, `multiselect`, or `price`. Reindex `catalog_product_attribute`.

**Force a URL refresh after a bulk import.** `Mage::getModel('catalog/url')->refreshRewrites($storeId)` from a shell script, or queue via `index_event` and run `php shell/indexer.php --reindex catalog_url`.

## Pitfalls

- Saving a product **without** `setStoreId(0)` (admin scope) will write store-specific overrides instead of the default value. Most admin code calls `setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)` for you; custom shell scripts often don't.
- `getCategoryIds()` only returns categories the product is directly assigned to. Anchor categories above are computed via the index — query `catalog_category_product_index` for the resolved set.
- `$product->getPrice()` is the catalog price; final price (catalog rules + tier prices) requires `$product->getFinalPrice()`. Tax/store currency is applied higher up.
- Flat-table reads bypass EAV backend model logic. If something works in admin and breaks on frontend, suspect flat coverage of the attribute first (e.g. attribute not flagged `used_in_product_listing`/`used_for_sort_by`).
- Bundle and downloadable price calculations differ enough that observers tied to `catalog_product_get_final_price` can't assume a single shape.

## Cross-references

- `openmage-eav` — attribute setup, source/backend models, flat-table flags, store-scoped value semantics
- `openmage-indexers-cron` — `Mage_Index` event log, matrix mode, cron declaration, shell/indexer.php
- `mage-module-product-types` — Bundle / Configurable / Grouped / Downloadable internals; option/selection/link/sample tables
- `mage-module-promotions` — catalog price rules read here; rule changes need a `catalogrule_product` reindex (separate indexer)
- `mage-module-tax` — final-price calculation hooks
- `openmage-db-setup-scripts` — where to put EAV attribute installers
- `openmage-events-observers` — `catalog_product_*`, `catalog_category_*`, `catalog_block_product_list_collection` are heavily observed
