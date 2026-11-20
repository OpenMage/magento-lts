---
name: openmage-eav
description: OpenMage EAV — entity/attribute/attribute set anatomy, Mage_Eav_Model_Entity_Setup, source/backend/frontend models, store-scoped values, collection helpers (addAttributeToSelect/Filter, joinAttribute), flat tables. Use when editing under Catalog/, Customer/, Eav/; adding an attribute via setup script; working with addAttributeToSelect/joinAttribute; or asking about store-scoped attribute values and useDefault semantics.
---

# openmage-eav

EAV (Entity-Attribute-Value) is how `Mage_Catalog`, `Mage_Customer`, and a few other modules store sparse, per-attribute, optionally store-scoped data. It is the single largest source of "why is this so weird" moments in OpenMage.

This skill covers the wiring. For setup-script mechanics see `openmage-db-setup-scripts`; for catalog-specific concerns see `mage-module-catalog`; for customer-specific concerns see `mage-module-customer`.

## Core terms

- **Entity type** — a row in `eav_entity_type` (e.g. `catalog_product`, `catalog_category`, `customer`, `customer_address`). Owns a default attribute set, a resource model, and a set of value tables.
- **Attribute** — a row in `eav_attribute` (+ EAV-extended row in `catalog_eav_attribute` for products/categories). Has a backend type (`int|varchar|text|decimal|datetime|static`) that picks which value table its values live in.
- **Attribute set** — a named bundle of attributes assigned to an entity type. Every product belongs to exactly one set. Stored in `eav_attribute_set` + `eav_entity_attribute`.
- **Attribute group** — a grouping of attributes inside a set, used for the admin form tabs ("General", "Prices", "Meta"…). Stored in `eav_attribute_group`.
- **Source / backend / frontend models** — pluggable hooks (see below) referenced by class alias on the attribute row.

## Table layout

```
eav_entity_type ─┬─ eav_attribute_set ── eav_attribute_group
                 │                            │
                 │                            │ (eav_entity_attribute = set×attr×group)
                 │                            │
                 └─ eav_attribute ────────────┘
                       │
                       │ backend_type picks one:
                       ▼
   ┌────────────────────────────────────────────────────────┐
   │ <entity>_entity                  ← row per entity      │
   │ <entity>_entity_int              ← int attribute vals  │
   │ <entity>_entity_varchar          ← varchar             │
   │ <entity>_entity_text             ← longtext            │
   │ <entity>_entity_decimal          ← decimal             │
   │ <entity>_entity_datetime         ← datetime            │
   └────────────────────────────────────────────────────────┘
   Per value row: (entity_type_id, attribute_id, store_id, entity_id, value)
   Composite UNIQUE on `(entity_id, attribute_id, store_id)` for catalog/eav value tables; customer value tables narrow it to `(entity_id, attribute_id)` (no store-scoped customer attributes).
```

For `catalog_product` the tables are `catalog_product_entity`, `catalog_product_entity_int`, etc. `customer` follows the same pattern: `customer_entity`, `customer_entity_int`, …

`backend_type='static'` means the attribute is a real column on `<entity>_entity` (e.g. `sku`, `created_at`) — no value-table row, no per-store value.

## Adding an attribute — setup scripts only

The only stable way to register an attribute is via `Mage_Eav_Model_Entity_Setup::addAttribute()`. Editing `eav_attribute` directly skips set-membership wiring and breaks on cache flush.

`addAttribute()` signature:

```php
public function addAttribute($entityTypeId, $code, array $attr)
```

Real example from `app/code/core/Mage/Catalog/sql/catalog_setup/upgrade-1.6.0.0-1.6.0.0.1.php`:

```php
/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp_enabled', [
    'group'         => 'Prices',
    'backend'       => 'catalog/product_attribute_backend_msrp',
    'frontend'      => '',
    'label'         => 'Apply MAP',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => 'adminhtml/catalog_product_helper_form_msrp_enabled',
    'visible_on_front' => false,
    'used_in_product_listing' => true,
]);
```

Key keys in the `$attr` array:

- `type` — backend type (`int`, `varchar`, `text`, `decimal`, `datetime`, `static`). Defaults to `varchar`. Determines which value table the data lives in.
- `input` — form widget (`text`, `select`, `multiselect`, `date`, `price`, `media_image`, `textarea`, `boolean`, `gallery`, …).
- `backend` / `source` / `frontend` — class aliases for hook models (see next section). Empty string means "use the default for that input type".
- `global` — scope. Constants on `Mage_Catalog_Model_Resource_Eav_Attribute`: `SCOPE_GLOBAL`, `SCOPE_WEBSITE`, `SCOPE_STORE`. Drives whether the value can vary per store.
- `group` — attribute-group name. If present, `addAttribute()` adds the attribute to *every* attribute set of that entity type, in that group, creating the group if needed. Without `group`, the attribute is only inserted (no set membership) unless `user_defined` is false (then it's added to the default group of every set).
- `apply_to` — comma-separated product types this attribute applies to (catalog only).
- `used_in_product_listing` — must be `true` for the attribute to land in the catalog flat tables.
- `visible_on_front`, `visible`, `required`, `default`, `unique`, `searchable`, `filterable`, `comparable`, `is_html_allowed_on_front`, `used_for_sort_by`, `position` — additional flags read by the catalog admin form / collection layer.

`addAttribute()` is idempotent — calling it again with the same code calls `updateAttribute()` instead of erroring. Setup scripts still only run once per `<modules>` version bump (see `openmage-db-setup-scripts`); to reapply changes, ship a new upgrade script.

## Source models — option lists

A source model returns the list of selectable values for `select`/`multiselect` inputs. Implements `getAllOptions()` and (optionally) `toOptionArray()`. Real example from `Mage/Catalog/Model/Product/Attribute/Source/Boolean.php`:

```php
class Mage_Catalog_Model_Product_Attribute_Source_Boolean
    extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    #[Override]
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => Mage::helper('catalog')->__('Yes'),        'value' => 1],
                ['label' => Mage::helper('catalog')->__('No'),         'value' => 0],
                ['label' => Mage::helper('catalog')->__('Use config'), 'value' => 2],
            ];
        }
        return $this->_options;
    }
}
```

Reference by alias on the attribute (`'source' => 'catalog/product_attribute_source_boolean'`). For attributes with options stored in `eav_attribute_option` (admin-managed dropdowns), use `eav/entity_attribute_source_table` — no custom source model needed.

## Backend models — validation and storage hooks

Backend models extend `Mage_Eav_Model_Entity_Attribute_Backend_Abstract` and run on save/load. Override points: `validate($object)`, `beforeSave($object)`, `afterSave($object)`, `beforeLoad`, `afterLoad`. Real example from `Mage/Catalog/Model/Product/Attribute/Backend/Sku.php`:

```php
class Mage_Catalog_Model_Product_Attribute_Backend_Sku
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public const SKU_MAX_LENGTH = 64;

    #[Override]
    public function validate($object)
    {
        $helper = Mage::helper('core/string');
        if ($helper->strlen($object->getSku()) > self::SKU_MAX_LENGTH) {
            Mage::throwException(
                Mage::helper('catalog')->__('SKU length should be %s characters maximum.', self::SKU_MAX_LENGTH),
            );
        }
        return parent::validate($object);
    }
}
```

Common built-ins: `eav/entity_attribute_backend_datetime`, `catalog/product_attribute_backend_price`, `catalog/product_attribute_backend_boolean`, `catalog/product_attribute_backend_media`. Use these unless you actually need custom validation/serialization — they handle store-scoped writes correctly.

## Frontend models — input renderers

Frontend models render attribute output for the storefront. Most attributes use `Mage_Eav_Model_Entity_Attribute_Frontend_Default` (a near-empty subclass of `Frontend_Abstract`):

```php
// app/code/core/Mage/Eav/Model/Entity/Attribute/Frontend/Default.php
class Mage_Eav_Model_Entity_Attribute_Frontend_Default
    extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract {}
```

Override only when you need custom rendering of `getValue($object)` (e.g. price formatting, image URL composition). Reference by alias: `'frontend' => 'catalog/product_attribute_frontend_image'`.

## Default vs store-scoped values

Value rows are keyed by `(entity_id, attribute_id, store_id)`:

- `store_id = 0` is the **admin / default** value.
- `store_id > 0` is the **store-scoped override**.

Read flow: load default first, overlay store-specific row if present. This is why in admin you see the "Use Default Value" checkbox next to store-view-scoped attributes.

When the form posts with **Use Default** checked, the controller calls `$product->setData($code, false)` and `Mage_Catalog_Model_Resource_Abstract::_isAttributeValueEmpty()` triggers the resource to **delete** the per-store row, so the next read falls back to default. On load, attributes that have a non-default-store value row are flagged via `setExistsStoreValueFlag()` (stored in `_storeValuesFlags`). The Use-Default checkbox handling is separate: `ProductController::_initProductSave()` reads `use_default[]` from the post and calls `$product->setData($code, false)`; the resource `_saveAttribute` path then deletes the per-store row when `_isAttributeValueEmpty()` returns true.

`global` scope on the attribute decides whether it has store-specific rows at all:

- `SCOPE_GLOBAL` (1) — only ever a `store_id = 0` row.
- `SCOPE_WEBSITE` (2) — one row per website (admin uses website's default store_id).
- `SCOPE_STORE` (0) — one row per store view, defaulting to admin.

## EAV models — the `_init` convention

Models that work with EAV entities extend `Mage_Catalog_Model_Abstract` (which extends `Mage_Core_Model_Abstract`) and bind to a resource via the alias in `_construct()`:

```php
protected function _construct()
{
    $this->_init('catalog/product');
}
```

The string is a model alias resolved via `<global><models>` in `etc/config.xml` to a resource class (e.g. `Mage_Catalog_Model_Resource_Product`). Don't rename — aliases are public surface (see `AGENTS.md`). `$_eventPrefix` (`'catalog_product'`) and `$_eventObject` (`'product'`) drive `dispatchEvent($prefix . '_save_before', [$obj => $this])` etc.

## Collection idioms

EAV collections (`Mage_Eav_Model_Entity_Collection_Abstract` + subclasses like `Mage_Catalog_Model_Resource_Product_Collection`) load the entity table, then JOIN value tables for every attribute you ask for.

```php
$products = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect(['name', 'sku', 'price'])
    ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->addAttributeToFilter('visibility', ['neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE])
    ->addStoreFilter();
```

Key methods:

- `addAttributeToSelect($attribute, $joinType = false)` — add one attribute (string) or many (array). Use `'*'` to load all. Each non-static attribute becomes a LEFT JOIN against its value table. **Don't load attributes you won't use** — every extra attribute is another join.
- `addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')` — `$condition` is either a scalar (equality) or a Varien condition array: `['eq' => 1]`, `['neq' => 0]`, `['in' => [1,2]]`, `['like' => 'foo%']`, `['null' => true]`, `['gt' => 0]`, etc.
- `joinAttribute($alias, $attribute, $bind, $filter = null, $joinType = 'inner', $storeId = null)` — pull an attribute from a *different* entity into this collection's rowset. `$alias` is the column name in the result; `$attribute` is `entity/attribute_code` (e.g. `'catalog_category/name'`); `$bind` is the join field on the parent (e.g. `'category_id'`). Used heavily in admin grids.
- `addFieldToFilter` on EAV collections is just an alias for `addAttributeToFilter`. Static attributes (`backend_type='static'`) skip the join in either method; non-static attributes get a value-table join either way.

Always set the store before loading attribute values: `->setStoreId($storeId)` (or `->addStoreFilter()` for products). Without it, you get admin-default values.

## Flat-table pitfall (catalog only)

When **product flat is enabled** and the collection is on the frontend (`isEnabledFlat()` returns true), `addAttributeToSelect` no longer joins EAV value tables — it reads columns from `catalog_product_flat_<store_id>` instead. From `Mage/Catalog/Model/Resource/Product/Collection.php`:

```php
public function addAttributeToSelect($attribute, $joinType = false)
{
    if ($this->isEnabledFlat()) {
        // ... pull columns from the flat table ...
        return $this;
    }
    return parent::addAttributeToSelect($attribute, $joinType);
}
```

Consequence: an attribute that isn't flagged `used_in_product_listing` (or `used_for_sort_by`) is **not in the flat table**, so `addAttributeToSelect('my_attr')` silently returns nothing on the frontend even though it works in admin. To fix, set the flag in a setup script:

```php
$installer->updateAttribute('catalog_product', 'my_attr', 'used_in_product_listing', 1);
```

Then `php shell/indexer.php --reindex catalog_product_flat`. Category flat has the same shape with `is_filterable`/`is_anchor` flags.

Static attributes (`backend_type='static'`) and the entity table's own columns are always available — they don't go through the join machinery either way.

## Common gotchas

- Setup scripts only run once per version. To re-run an attribute change, bump the module version in `etc/config.xml` and ship a new `upgrade-X.Y-X.Z.php`. See `openmage-db-setup-scripts`.
- `Mage::getModel('catalog/product')->load($id)` loads default values only unless you `setStoreId()` first.
- `addAttributeToSort('foo', 'asc')` requires the attribute already be selected (or filtered), or it silently does nothing.
- `addAttributeToFilter` on a multiselect attribute compares the comma-joined string in the value column — use `['finset' => $value]` instead of `['eq' => $value]`.
- `eav_attribute.is_user_defined = 0` marks an attribute as system-managed; the admin set-edit UI won't let users remove it from a set.
- New attribute? Update the `@method` docblock on the entity class (see `AGENTS.md`).

## Cross-references

- `openmage-db-setup-scripts` — setup-script naming, version bumps, the `Mage_Eav_Model_Entity_Setup` superclass.
- `mage-module-catalog` — product/category specifics, indexers (`catalog_product_flat`, `catalog_product_attribute`), URL rewrites, `apply_to` semantics.
- `mage-module-customer` — customer / customer_address EAV entities, default attribute set, address-attribute conventions.
