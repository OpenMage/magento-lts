---
name: openmage-caching
description: OpenMage caching — built-in cache types (config, layout, block_html, collections, eav, translate, config_api, config_api2), tag-based invalidation, block-level caching (getCacheKeyInfo/Tags/Lifetime), Mage::app()->getCache(). Use when adding a cache type, busting cache on save, tagging custom caches, debugging stale block output, or working with Mage_Core_Block_Abstract::_loadCache.
---

# openmage-caching

OpenMage cache system: a `Zend_Cache_Core` frontend wrapping a backend (`Zend_Cache_Backend_File` by default at `var/cache/`, Redis via `Cm_Cache_Backend_Redis` in production). Wrapped by `Mage_Core_Model_Cache` and surfaced through `Mage::app()`.

Cross-refs: `openmage-events-observers`, `openmage-layout-blocks`.

## Cache types

Registered under `<global><cache><types>` in each module's `etc/config.xml`. Each type has a `<tags>` value used as the master tag for `cleanType()`. Built-ins:

| Type | Tag | Module |
|---|---|---|
| `config` | `CONFIG` | Core |
| `layout` | `LAYOUT_GENERAL_CACHE_TAG` | Core |
| `block_html` | `BLOCK_HTML` | Core |
| `translate` | `TRANSLATE` | Core |
| `collections` | `COLLECTION_DATA` | Core |
| `eav` | `eav` | Eav |
| `config_api` | `CONFIG_API` | Api |
| `config_api2` | `CONFIG_API2` | Api2 |
<!-- full_page (FPC) is EE-only and not registered as a built-in cache type in OpenMage; see Backends section -->
| ~~`full_page`~~ | ~~`FPC`~~ | ~~PageCache~~ |

Cache types must be **enabled in admin → System → Cache Management** for `useCache($type)` to return true. A flushed type stays empty until the next read populates it; a *disabled* type bypasses caching entirely. Both states make caching look "broken" — check Cache Management first.

### Registering a custom type

```xml
<!-- app/code/community/Vendor/Module/etc/config.xml -->
<config>
    <global>
        <cache>
            <types>
                <vendor_module translate="label,description" module="vendor_module">
                    <label>Vendor Module Data</label>
                    <description>Custom thing we cache.</description>
                    <tags>VENDOR_MODULE</tags>
                </vendor_module>
            </types>
        </cache>
    </global>
</config>
```

The node name (`vendor_module`) becomes the type code passed to `useCache()`, `cleanType()`, and shown as a row on Cache Management. Translate the label via the module's locale CSV.

## Mage::app() cache API

Surface from `Mage_Core_Model_App` (`app/code/core/Mage/Core/Model/App.php`):

```php
Mage::app()->useCache('block_html');           // bool when typeCode given (returns array|false otherwise)
Mage::app()->loadCache($id);                   // false|string
Mage::app()->saveCache($data, $id, $tags, $lifeTime);
Mage::app()->removeCache($id);
Mage::app()->cleanCache(['CATALOG_PRODUCT']);  // wipe by tag(s); empty = flush
Mage::app()->getCache();                       // raw Zend_Cache_Core frontend
Mage::app()->getCacheInstance();               // Mage_Core_Model_Cache wrapper
```

`cleanCache` dispatches `application_clean_cache` with the tags. Wrap own caches behind `useCache($type)` so a disabled type causes a graceful fall-through to a fresh compute.

```php
$key = 'vendor_module_thing_' . $id;
if (Mage::app()->useCache('vendor_module') && ($data = Mage::app()->loadCache($key))) {
    return unserialize($data);
}
$data = $this->_compute($id);
if (Mage::app()->useCache('vendor_module')) {
    Mage::app()->saveCache(serialize($data), $key, ['VENDOR_MODULE', 'VENDOR_MODULE_' . $id], 3600);
}
return $data;
```

Default lifetime when none is passed: `Mage_Core_Model_Cache::DEFAULT_LIFETIME = 7200` (2h).

## Block caching

Defined on `Mage_Core_Block_Abstract` (`app/code/core/Mage/Core/Block/Abstract.php`). The block is cached only when `getCacheLifetime()` returns non-null **and** the `block_html` type is enabled (`useCache(self::CACHE_GROUP)`). When a block sets an explicit `cache_key`, it's prefixed `BLOCK_`; otherwise the default key is `sha1(implode('|', getCacheKeyInfo()))` with no prefix.

The default `getCacheKey()` hashes `getCacheKeyInfo()` joined by `|`. The base implementation only adds `getNameInLayout()` — *anything else that varies output (store, theme, customer group, current entity) must be added by the subclass*. The framework does **not** add design package/theme/store/customer-group automatically — the convention to do so lives in subclasses like `Mage_Catalog_Block_Navigation` below.

### Real subclass — `Mage_Catalog_Block_Navigation`

```php
protected function _construct()
{
    $this->addData(['cache_lifetime' => false]);   // false => use default cache lifetime
    $this->addCacheTag([
        Mage_Catalog_Model_Category::CACHE_TAG,    // 'catalog_category'
        Mage_Core_Model_Store_Group::CACHE_TAG,
    ]);
}

public function getCacheKeyInfo()
{
    $shortCacheId = [
        'CATALOG_NAVIGATION',
        Mage::app()->getStore()->getId(),
        Mage::getDesign()->getPackageName(),
        Mage::getDesign()->getTheme('template'),
        Mage::getSingleton('customer/session')->getCustomerGroupId(),
        'template' => $this->getTemplate(),
        'name'     => $this->getNameInLayout(),
        $this->getCurrenCategoryKey(),
    ];
    // ...
    return $cacheId;
}
```

`getCacheTags()` on the block merges `addCacheTag()` data with the global `block_html` tag — see `Mage_Core_Block_Abstract::getCacheTags()` (~L1404). The session ID is replaced with a placeholder before save and restored on load via `_getSidPlaceholder()` so cached HTML doesn't leak SIDs across users (`_loadCache`/`_saveCache` ~L1481/1508).

Lifetime semantics on a block:

- `cache_lifetime = null` (default) — block is **not** cached. `_loadCache()` short-circuits.
- `cache_lifetime = false` — falls through to backend default (2h).
- `cache_lifetime = <int>` — seconds.

## Busting cache on save

Two patterns. Direct from a model's `_afterSave`/`cleanCache` (Catalog Product, `Mage_Catalog_Model_Product::cleanCache`):

```php
public function cleanCache()
{
    if ($this->getId()) {
        Mage::app()->cleanCache('catalog_product_' . $this->getId());
    }
    return $this;
}
```

Or via `Mage_Core_Model_Abstract::cleanModelCache()`, which uses `$this->_cacheTag` + ID:

```php
// in your model
protected $_cacheTag = self::CACHE_TAG;          // string tag, or array, or true

// getCacheTags() returns ['vendor_module', 'vendor_module_42'] for id=42
// cleanModelCache() wipes via Mage::app()->cleanCache($tags)
```

Generic observer that invalidates by tag (admin uses this via the `clean_cache_by_tags` event — `Mage_Core_Model_Observer::cleanCacheByTags`):

```php
public function cleanCacheByTags(Varien_Event_Observer $observer)
{
    $tags = $observer->getEvent()->getTags();
    if (empty($tags)) {
        Mage::app()->cleanCache();
        return $this;
    }
    Mage::app()->cleanCache($tags);
    return $this;
}
```

Wire your own observer to a save event:

```xml
<events>
    <vendor_module_thing_save_after>
        <observers>
            <vendor_module_bust>
                <class>vendor_module/observer</class>
                <method>onThingSaved</method>
            </vendor_module_bust>
        </observers>
    </vendor_module_thing_save_after>
</events>
```

```php
public function onThingSaved(Varien_Event_Observer $o): void
{
    Mage::app()->cleanCache(['VENDOR_MODULE', 'VENDOR_MODULE_' . $o->getThing()->getId()]);
}
```

## Wiping a whole type

```php
Mage::app()->getCacheInstance()->cleanType('block_html');   // single type
Mage::app()->getCacheInstance()->flush();                   // nuke everything
```

`cleanType($code)` looks up the configured `<tags>` for the type and calls `clean()`.

## Backends

Default is `Zend_Cache_Backend_File` writing to `var/cache/` (`Mage_Core_Model_Cache::$_defaultBackend = 'File'`). Production: `Cm_Cache_Backend_Redis`. Configured in `app/etc/local.xml` under `<global><cache>` — see `app/etc/local.xml.additional` for both shapes (Redis snippet around L73). FPC is a separate backend block under `<global><full_page_cache>`.

## Common pitfalls

- "My block won't cache." Either `cache_lifetime` is null, the `block_html` type is disabled, or the block is rendered through a path that bypasses `toHtml()` (e.g. raw `$this->renderView()`).
- "My block shows stale data across stores/customer groups." `getCacheKeyInfo()` doesn't include those dimensions — see the Navigation snippet above. The base implementation does not add them.
- "I cleaned the tag but cache is still stale." `cleanCache(['FOO'])` matches with `Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG` — only entries that were saved **with that exact tag** match. The tag must be in the array passed to `saveCache()` / `addCacheTag()`.
- Block cache stores a JSON copy of the tag list under a sibling key (`_getTagsCacheKey`) so `getCacheTags()` survives across requests — don't hand-edit `var/cache/`.
- Disable caches in `local.xml` for development by setting an unreachable backend, or just keep types disabled in admin.
