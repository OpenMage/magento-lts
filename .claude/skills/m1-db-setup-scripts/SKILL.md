---
name: m1-db-setup-scripts
description: Magento 1 install/upgrade scripts under sql/ and data/, version-bumped DB migrations, EAV attribute setup. Use when adding a column or table, writing a migration, bumping a module version, editing files under **/sql/*_setup/*.php or **/data/*_setup/*.php, or adding an EAV attribute.
---

# M1 DB Setup Scripts

Versioned schema and data migrations. The only sanctioned way to change the DB in OpenMage — direct DDL anywhere else breaks BC for merchants and extensions.

## How they run

`Mage_Core_Model_Resource_Setup::applyAllUpdates()` runs at bootstrap when any module's `<modules><Foo><version>` in `etc/config.xml` is newer than the version recorded in the `core_resource` table (columns: `code`, `version`, `data_version`). For each resource the setup class:

1. Picks the matching script for the current → target version walk (install once, then chained upgrades).
2. Wraps the script's `$this` as `Mage_Core_Model_Resource_Setup` (or the EAV/customer subclass declared in `<resources><foo_setup><setup><class>`).
3. Records the new version in `core_resource` only if the script returns without throwing.

Scripts are **immutable after release**. Once a version is in `core_resource` on any merchant's DB, that file will never run again on that DB. To change behavior, write a *new* script at a higher version and bump `<modules><Mage_Foo><version>` in `etc/config.xml`. See `m1-module-structure` for where the version block lives.

Schema first (`sql/`), then data (`data/`). All `sql/*_setup/*` scripts run for every module before any `data/*_setup/*` script runs. Don't query data you've just inserted from a `sql/` script — put it in `data/`.

## File locations and naming

```
app/code/core/Mage/<Module>/sql/<setup_name>/
    install-X.Y.Z.php
    upgrade-X.Y.Z-X.Y.Z+1.php
    mysql4-install-X.Y.Z.php           # legacy, pre-1.6, still honored
    mysql4-upgrade-X.Y.Z-X.Y.Z+1.php   # legacy
app/code/core/Mage/<Module>/data/<setup_name>/
    data-install-X.Y.Z.php
    data-upgrade-X.Y.Z-X.Y.Z+1.php
```

`<setup_name>` matches the resource declared in `etc/config.xml`:

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

If `<class>` is omitted, the default `Mage_Core_Model_Resource_Setup` is used. Catalog uses `Mage_Catalog_Model_Resource_Setup` (an EAV setup); Customer uses `Mage_Customer_Model_Entity_Setup`. Pick the right base class — adding an EAV attribute requires an EAV-aware setup.

The version chain must be unbroken: if you ship `1.6.0.0.10`, you must provide either `install-1.6.0.0.10.php` or a path of `upgrade-X-Y` files reaching it from any prior installed version. Pick the predecessor by reading the highest `<version>` shipped in the previous tag.

## Schema script — canonical envelope

Plain `Mage_Core_Model_Resource_Setup` script. `$this` is the setup; alias as `$installer` per repo convention:

```php
<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer  = $this;
$connection = $installer->getConnection();

$connection->addColumn($installer->getTable('catalog/product_attribute_group_price'), 'is_percent', [
    'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default'  => '0',
    'comment'  => 'Is Percent',
]);
```

`startSetup()` / `endSetup()` toggle SQL mode and FK checks; required for any script that creates tables or adds FKs. Pure `addColumn` calls work without it but it's harmless to wrap:

```php
$installer->startSetup();
// ... DDL ...
$installer->endSetup();
```

Always resolve table names via `$installer->getTable('catalog/product')` — never hardcode `catalog_product_entity`. The alias respects the `db_prefix` install option.

## Creating a table

Use the `Varien_Db_Ddl_Table` builder via `$connection->newTable(...)` and `$connection->createTable($table)`:

```php
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_attribute_group_price'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ], 'Value ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false,
        'default'  => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable' => false,
        'default'  => '0.0000',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_attribute_group_price',
            ['entity_id', 'customer_group_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'customer_group_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    );
$installer->getConnection()->createTable($table);
```

Use `$installer->getIdxName(...)` and `$installer->getFkName(...)` so identifiers fit MySQL's 64-char limit and follow the repo convention.

## DDL helpers — real signatures

From `lib/Varien/Db/Adapter/Pdo/Mysql.php`:

```php
public function addColumn($tableName, $columnName, $definition, $schemaName = null)
public function newTable($tableName = null, $schemaName = null)   // returns Varien_Db_Ddl_Table
public function addIndex(
    $tableName,
    $indexName,
    $fields,
    $indexType = Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
    $schemaName = null
)
public function addForeignKey(
    $fkName,
    $tableName,
    $columnName,
    $refTableName,
    $refColumnName,
    $onDelete = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
    $onUpdate = Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
    $purge = false,
    $schemaName = null,
    $refSchemaName = null
)
```

Other commonly used helpers on the same adapter: `dropTable`, `dropColumn`, `dropForeignKey`, `dropIndex`, `changeColumn`, `modifyColumn`, `tableColumnExists`, `isTableExists`. The `INDEX_TYPE_*` constants live on `Varien_Db_Adapter_Interface` (`INDEX`, `UNIQUE`, `PRIMARY`, `FULLTEXT`); the `TYPE_*` constants for column types live on `Varien_Db_Ddl_Table`.

## EAV: adding an attribute

Customer / catalog attributes go through the EAV setup, never raw DDL. The setup class is wired in `etc/config.xml` (`Mage_Customer_Model_Entity_Setup` for customers, `Mage_Catalog_Model_Resource_Setup` for catalog). Real example from `Mage/Customer/sql/customer_setup/upgrade-1.6.2.0.5-1.6.2.0.6.php`:

```php
<?php

declare(strict_types=1);

/** @var Mage_Customer_Model_Entity_Setup $this */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'password_created_at', [
    'label'    => 'Password created at',
    'visible'  => false,
    'required' => false,
    'type'     => 'int',
]);

$installer->endSetup();
```

For product attributes, pass `Mage_Catalog_Model_Product::ENTITY` (`'catalog_product'`) as the entity and supply the catalog-flavored keys (`group`, `input`, `source`, `backend`, `frontend`, `global`, `used_in_product_listing`, `apply_to`, `is_configurable`, `is_searchable`, `is_filterable`, etc.).

Companion EAV ops:
- `$installer->updateAttribute($entity, $code, $field, $value)` — change one field on an existing attribute (e.g. swap a `backend_model`).
- `$installer->removeAttribute($entity, $code)` — drop an attribute and its values.
- `$installer->addAttributeGroup`, `addAttributeSet`, `addAttributeToGroup`, `addAttributeToSet` — attribute set / group composition.
- `$installer->getAttribute($entity, $code)` — fetch the row to read `attribute_id` / `attribute_table`.

See `m1-eav` for the entity / attribute / set / group model and source/backend/frontend conventions.

## Data scripts

`data/<setup>/data-*.php` runs after all `sql/` scripts of all modules have completed for the cycle. Use these for DML — seeding rows, transforming existing data, fixups that rely on the schema being final. Same `$installer = $this;` envelope. Real example from `Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.4-1.6.0.0.5.php`:

```php
<?php

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;

$eavResource = Mage::getResourceModel('catalog/eav_attribute');
$multiSelectAttributeCodes = $eavResource->getAttributeCodesByFrontendType('multiselect');

foreach ($multiSelectAttributeCodes as $attributeCode) {
    $attribute = $installer->getAttribute('catalog_product', $attributeCode);
    if (!$attribute) {
        continue;
    }
    $attributeTable = $installer->getAttributeTable('catalog_product', $attributeCode);
    $select = $installer->getConnection()->select()
        ->from(['e' => $attributeTable])
        ->where('e.attribute_id=?', $attribute['attribute_id'])
        ->where('e.value LIKE "%,,%"');
    foreach ($installer->getConnection()->fetchAll($select) as $row) {
        $row['value'] = preg_replace('/,{2,}/', ',', $row['value'], -1, $n);
        if ($n) {
            $installer->getConnection()->update(
                $attributeTable,
                ['value' => $row['value']],
                'value_id=' . $row['value_id'],
            );
        }
    }
}
```

Note `core_resource` tracks `version` (sql) and `data_version` (data) independently — the data walk is its own version chain.

## Workflow for a new migration

1. Decide schema vs data. Adding a column / table / index / FK → `sql/`. Backfilling, transforming rows, seeding configuration → `data/`.
2. Read the current `<version>` from `app/code/core/Mage/<Module>/etc/config.xml`. Bump the *last* segment by one (e.g. `1.6.0.0.19.1.7` → `1.6.0.0.19.1.8`).
3. Create `sql/<setup>/upgrade-<old>-<new>.php` (and/or `data/<setup>/data-upgrade-<old>-<new>.php`). Match the predecessor naming: if the previous script is `mysql4-upgrade-...`, *don't* perpetuate that — use the modern `upgrade-...` form for new files.
4. Use the canonical envelope. Resolve tables via `$installer->getTable('alias/name')`. Use `$installer->getIdxName` / `getFkName`. Wrap structural changes in `startSetup()` / `endSetup()`.
5. Update `<modules><Mage_Foo><version>` in `etc/config.xml` to the new version.
6. Run the migration: clear `var/cache/`, bump the merchant DB row in `core_resource` if testing repeatedly, hit any URL to trigger `applyAllUpdates`. The shell shortcut: `php shell/log.php` (any `Mage::app()` boot triggers it).
7. Once merged and released, that file is frozen. Bugfix = new script at a higher version.

## Pitfalls

- Don't query / mutate data from a `sql/` script — schema for other modules may not yet exist.
- Don't reference PHP classes that may move or be deleted later (e.g. concrete model classes by FQCN). The script lives forever; classes don't. Stick to setup APIs and `$installer->getConnection()`.
- Don't put DDL inside conditionals based on environment data — it must be deterministic. If you need an idempotent guard, use `$installer->getConnection()->tableColumnExists(...)` before `addColumn`.
- Don't reuse a version number across schema and data — the chain walks them separately but the file *names* must be unique per directory.
- A script that throws aborts the run and the version is not recorded; the next bootstrap retries from the same start version. Make the script safe to re-enter from a partial failure.
- Legacy `mysql4-` files exist in older modules (e.g. `Mage/Customer/sql/customer_setup/mysql4-upgrade-1.6.0.0-1.6.1.0.php`). They still execute — leave them alone. New files use the modern `install-` / `upgrade-` / `data-install-` / `data-upgrade-` prefixes.

## Cross-references

- `m1-module-structure` — where `<modules><version>` and the `<resources>/<setup>` wiring live, module activation manifest.
- `m1-eav` — entity / attribute / set / group model; source/backend/frontend conventions for `addAttribute`.
