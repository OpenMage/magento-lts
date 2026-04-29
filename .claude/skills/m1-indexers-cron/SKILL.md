---
name: m1-indexers-cron
description: Magento 1 indexers and cron — index_event table, Mage_Index_Model_Indexer_Abstract, reindex modes, <crontab> jobs in etc/config.xml, schedule lifecycle, cron.php and shell/indexer.php. Use when registering an indexer, adding a cron job, editing files under Index/** or Cron/** or <crontab>/<indexer> XML config blocks.
---

# m1-indexers-cron

Indexers rebuild denormalised data; cron schedules deferred work. Both wire through `etc/config.xml` and share a process model: events accumulate in a queue, then a worker (admin button, `shell/indexer.php`, or cron) drains it.

Cross-refs: `m1-module-structure` (where these XML blocks live), `m1-events-observers` (matched on entity save events).

## Indexer registration

`<global><index><indexer>` in the module's `etc/config.xml`. Key is the indexer code (`catalog_url`, `cataloginventory_stock`, …), `<model>` is the alias.

```xml
<!-- app/code/core/Mage/Catalog/etc/config.xml -->
<global>
    <index>
        <indexer>
            <catalog_product_attribute><model>catalog/product_indexer_eav</model></catalog_product_attribute>
            <catalog_product_price>    <model>catalog/product_indexer_price</model></catalog_product_price>
            <catalog_url>              <model>catalog/indexer_url</model></catalog_url>
            <catalog_product_flat>     <model>catalog/product_indexer_flat</model></catalog_product_flat>
            <catalog_category_flat>    <model>catalog/category_indexer_flat</model></catalog_category_flat>
            <catalog_category_product> <model>catalog/category_indexer_product</model></catalog_category_product>
        </indexer>
    </index>
</global>
```

`<depends>` orders execution (CatalogInventory makes `catalog_product_price` depend on `cataloginventory_stock`):

```xml
<catalog_product_price>
    <depends><cataloginventory_stock/></depends>
</catalog_product_price>
```

### Core indexer codes

| code | model alias | module |
|---|---|---|
| `catalog_url` | `catalog/indexer_url` | Mage_Catalog |
| `catalog_product_flat` | `catalog/product_indexer_flat` | Mage_Catalog |
| `catalog_product_price` | `catalog/product_indexer_price` | Mage_Catalog |
| `catalog_product_attribute` | `catalog/product_indexer_eav` | Mage_Catalog |
| `catalog_category_flat` | `catalog/category_indexer_flat` | Mage_Catalog |
| `catalog_category_product` | `catalog/category_indexer_product` | Mage_Catalog |
| `cataloginventory_stock` | `cataloginventory/indexer_stock` | Mage_CatalogInventory |
| `catalogsearch_fulltext` | `catalogsearch/indexer_fulltext` | Mage_CatalogSearch |
| `catalogrule_product` | `catalogrule/indexer_rule` | Mage_CatalogRule |
| `tag_summary` | `tag/indexer_summary` | Mage_Tag |

## Indexer class

Extend `Mage_Index_Model_Indexer_Abstract`. Required overrides: `getName()`, `_registerEvent()`, `_processEvent()`. `reindexAll()` defaults to `_getResource()->reindexAll()`.

```php
abstract class Mage_Index_Model_Indexer_Abstract extends Mage_Core_Model_Abstract
{
    protected $_matchedEntities = [];     // entity ENTITY const => [TYPE_SAVE, TYPE_DELETE, ...]
    protected $_isVisible = true;         // hide from admin Index Management list

    abstract public function getName();
    abstract protected function _registerEvent(Mage_Index_Model_Event $event);
    abstract protected function _processEvent(Mage_Index_Model_Event $event);

    public function matchEntityAndType($entity, $type) { /* dispatch table lookup */ }
    public function reindexAll() { $this->_getResource()->reindexAll(); }
}
```

Real example (`Mage_Catalog_Model_Indexer_Url`):

```php
protected $_matchedEntities = [
    Mage_Catalog_Model_Product::ENTITY        => [Mage_Index_Model_Event::TYPE_SAVE],
    Mage_Catalog_Model_Category::ENTITY       => [Mage_Index_Model_Event::TYPE_SAVE],
    Mage_Core_Model_Store::ENTITY             => [Mage_Index_Model_Event::TYPE_SAVE],
    Mage_Core_Model_Store_Group::ENTITY       => [Mage_Index_Model_Event::TYPE_SAVE],
    Mage_Core_Model_Config_Data::ENTITY       => [Mage_Index_Model_Event::TYPE_SAVE],
];
```

Override `matchEvent()` when the default entity/type table isn't enough — Url checks which config path changed and which store-group columns flipped.

## Reindex modes

`Mage_Index_Model_Process` constants:

- `MODE_REAL_TIME` — process every matching event synchronously on save ("Update on Save").
- `MODE_MANUAL` — log events to `index_event` only; admin/CLI must drain.
- `MODE_SCHEDULE` — same logging as manual, but periodic cron triggers `reindexAll`.

Process states: `STATUS_PENDING`, `STATUS_RUNNING`, `STATUS_REQUIRE_REINDEX` (events queued and need processing), default = ready.

The admin **System → Index Management** page reads `index_process` rows and lets you change mode, mass-reindex, or reindex per row. It calls `$process->reindexEverything()` (the same path `shell/indexer.php` uses).

### Schema

`index_setup/install-1.6.0.0.php` creates three tables:

- `index_event` — `event_id`, `entity` (`catalog_product`, …), `type` (`save`/`delete`/`mass_action`), serialized `data_object`, `created_at`.
- `index_process` — one row per registered indexer code; tracks `status`, `mode`, `ended_at`.
- `index_process_event` — N:M between processes and events, with per-process `status`.

When `Mage_Index_Model_Indexer::registerEvent()` fires, an `index_event` row is created and one `index_process_event` row per indexer that matches via `_matchedEntities`. Real-time processes drain immediately; manual/schedule leave the rows for later.

## Cron declaration

`<crontab><jobs>` in the module's `etc/config.xml`. Three forms of `<schedule>`, plus `<run>` to dispatch:

```xml
<!-- 1. inline cron expression — Mage_Catalog -->
<crontab>
    <jobs>
        <catalog_product_index_price_reindex_all>
            <schedule><cron_expr>0 2 * * *</cron_expr></schedule>
            <run><model>catalog/observer::reindexProductPrices</model></run>
        </catalog_product_index_price_reindex_all>
    </jobs>
</crontab>
```

```xml
<!-- 2. config_path — schedule lives in system.xml/store config — Mage_Core -->
<core_clean_cache>
    <schedule><config_path>system/cache/flush_cron_expr</config_path></schedule>
    <run><model>core/observer::cleanCache</model></run>
</core_clean_cache>
```

```xml
<!-- 3. always — runs every cron tick (no schedule row) — Mage_Core -->
<core_email_queue_send_all>
    <schedule><cron_expr>*/1 * * * *</cron_expr></schedule>
    <run><model>core/email_queue::send</model></run>
</core_email_queue_send_all>
```

`<run><model>alias::method</model></run>` is parsed by `Mage_Cron_Model_Observer::REGEX_RUN_MODEL` (`#^([a-z0-9_]+/[a-z0-9_]+)::([a-z0-9_]+)$#i`). The method receives the `Mage_Cron_Model_Schedule` instance as its only argument.

`config_path` overrides `cron_expr` when set (`Mage_Cron_Model_Observer::_generateJobs()`). Use `config_path` so admins can edit the expression in System Config without code changes.

### Cron expression

Standard 5-field `min hour dom mon dow`. `0 2 * * *` = 02:00 daily, `*/15 * * * *` = every 15 min, `0 0 * * 0` = Sundays at midnight. The literal string `always` is special — handled via the `<always>` event area, not the schedule queue.

## Schedule lifecycle

`cron_schedule` table, rows owned by `Mage_Cron_Model_Schedule`. Status constants:

- `STATUS_PENDING` — generated ahead, not yet due (or due and waiting for a worker).
- `STATUS_RUNNING` — `tryLockJob()` won the atomic CAS from PENDING.
- `STATUS_SUCCESS` — callback returned cleanly.
- `STATUS_MISSED` — still PENDING after `system/cron/schedule_lifetime` minutes elapsed past `scheduled_at` — never picked up.
- `STATUS_ERROR` — callback threw. `messages` column holds the exception.

Generation is incremental: `Mage_Cron_Model_Observer::generate()` runs every `system/cron/schedule_generate_every` minutes (cached in `cron_last_schedule_generate_at`), populating the next `system/cron/schedule_ahead_for` minutes of pending rows. Cleanup prunes rows older than `system/cron/history_success_lifetime` / `history_failure_lifetime`.

`Mage_Cron_Model_Observer::dispatch()` is the worker — invoked by the `default` cron event. It loads pending schedules, matches each `job_code` against `crontab/jobs` then `default/crontab/jobs`, and calls `_processJob()` which runs `tryLockJob()` (atomic PENDING → RUNNING) before invoking the callback.

## Entrypoints

### `cron.php`

Repo-root entry. Bootstraps `Mage::app('admin')`, then dispatches the `default` and `always` cron events:

```php
Mage::getConfig()->init()->loadEventObservers('crontab');
Mage::app()->addEventArea('crontab');
// $cronMode is 'default' or 'always' depending on -m flag / branch
Mage::dispatchEvent($cronMode);
```

`cron.sh` re-invokes `cron.php` twice: `-mdefault` (schedule queue) and `-malways` (per-tick jobs). Production sets one cron tab entry hitting `cron.sh` every minute.

### `shell/indexer.php`

CLI for indexers (`Mage_Shell_Indexer`). Common invocations:

```bash
php shell/indexer.php --info                                  # list indexer codes
php shell/indexer.php --status                                # show all statuses
php shell/indexer.php --status catalog_product_flat,catalog_url
php shell/indexer.php --mode                                  # show all modes
php shell/indexer.php --mode-realtime catalog_product_price   # set "Update on Save"
php shell/indexer.php --mode-manual catalog_product_price     # set "Manual Update"
php shell/indexer.php --reindex catalog_product_flat
php shell/indexer.php --reindexall                            # all visible indexers
php shell/indexer.php --reindexallrequired                    # only those in STATUS_PENDING
```

Reindex calls `$process->reindexEverything()`, which clears the matching `index_event` rows on success and dispatches `<code>_shell_reindex_after`.

## Common pitfalls

- After config rule changes, catalog rules need `catalogrule_product` reindex — usually via cron, but manual edits won't take effect on the storefront until the index is rebuilt.
- Switching an indexer to `MODE_MANUAL` stops admin saves from being slow but means stock/price/URL data goes stale until a reindex.
- A new indexer that doesn't appear in **Index Management** is almost always `_isVisible = false` or missing the `<global><index><indexer>` registration — check both.
- `cron.php` requires `Mage::isInstalled()` to return true and exits silently otherwise. Symlinking `cron.php` outside the repo without `chdir(__DIR__)` working correctly is a common silent-failure cause.
- Cron jobs whose callback alias doesn't match the `^[a-z0-9_]+/[a-z0-9_]+::[a-z0-9_]+$` regex are skipped without error — capitals and dashes in `<run><model>` will not run.
