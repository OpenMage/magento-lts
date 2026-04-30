---
name: openmage-translations
description: OpenMage translations — locale CSV files at app/locale/<locale>/Mage_<Module>.csv, __() helper/block translation, jstranslator.xml for JS strings, inline translation. Use when adding translatable strings, editing CSV files under app/locale/, working with __() in PHP/.phtml, or wiring jstranslator.xml.
---

# openmage-translations

OpenMage translations are per-module CSV files. Every translatable string flows through a `__()` call that resolves to the **module helper's** CSV. Missing translations fall through to the original string — no warning, no error.

## CSV files

One file per locale per module:

```
app/locale/<locale>/Mage_<Module>.csv
```

Format: UTF-8, one `"original","translation"` row per line. Quote everything; escape literal `"` by doubling it.

```csv
"Add to Cart","Add to Cart"
"%s Item(s)","%s Item(s)"
"<strong style=""color:red"">Warning!</strong> Applying MAP by default will hide all product prices on the frontend.","<strong style=""color:red"">Warning!</strong> Applying MAP by default will hide all product prices on the frontend."
```

`en_US` files mostly map strings to themselves — they exist so the translator doesn't have to round-trip through the locale layer for English (and so `inchoo/translation-tools` can find them).

## Helper translation

In any PHP file outside a block:

```php
Mage::helper('catalog')->__('Hello %s', $name);
```

`Mage::helper('catalog')` returns `Mage_Catalog_Helper_Data`, which extends `Mage_Core_Helper_Abstract` — no special translation method needed:

```php
class Mage_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
```

`Mage_Core_Helper_Abstract::__()` (in `app/code/core/Mage/Core/Helper/Abstract.php`) wraps the args in a `Mage_Core_Model_Translate_Expr` tagged with `_getModuleName()` (derived from the helper class — `Mage_Catalog_Helper_Data` → `Mage_Catalog`). The translator searches `Mage_Catalog.csv` first.

```php
public function __()
{
    $args = func_get_args();
    $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->_getModuleName());
    array_unshift($args, $expr);
    return Mage::app()->getTranslator()->translate($args);
}
```

## Block / template translation

In a block class or `.phtml`:

```php
$this->__('Quick Overview');
$this->__('Hello %s', $name);
```

`Mage_Core_Block_Abstract::__()` (in `app/code/core/Mage/Core/Block/Abstract.php`) tags the expression with `getModuleName()` — derived from the **block** class (`Mage_Catalog_Block_Product_View` → `Mage_Catalog`), so the lookup goes through `Mage_Catalog.csv` first.

```php
public function __()
{
    $args = func_get_args();
    $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->getModuleName());
    array_unshift($args, $expr);
    return $this->_getApp()->getTranslator()->translate($args);
}
```

Real `.phtml` (`app/design/frontend/base/default/template/catalog/product/view.phtml`):

```php
<h2><?php echo $this->__('Quick Overview') ?></h2>
<span class="or"><?php echo $this->__('OR') ?></span>
<a href="<?php echo $this->helper('catalog/product')->getEmailToFriendUrl($_product) ?>">
    <?php echo $this->__('Email to a Friend') ?>
</a>
```

### Translating via a different module's CSV

`$this->__()` always uses **the block's** module. To pull a string from another module's CSV, call its helper explicitly:

```php
// In a Mage_Catalog block, but the string lives in Mage_Sales.csv:
echo $this->helper('sales')->__('Order # %s', $orderId);
```

A block's `module_name` data key can also be overridden in layout XML to redirect lookups, but prefer the explicit helper call.

## Fallthrough behavior

If a string is not found in the module CSV, the translator returns the original string unchanged. This means:

- A new English string works immediately with no CSV edit.
- Add the row to `Mage_<Module>.csv` to make it translatable per locale.
- Typos in the original silently ship — there's no validation that the source string matches a known key.

## jstranslator.xml — JS strings

JS code can't read CSVs directly. Each module declares its translatable JS strings in `etc/jstranslator.xml`; they get serialized into the page and resolved by `Translator.translate(...)` client-side.

`app/code/core/Mage/Adminhtml/etc/jstranslator.xml`:

```xml
<jstranslator>
    <validate-special-price translate="message" module="adminhtml">
        <message>The Special Price is active only when lower than the Actual Price.</message>
    </validate-special-price>
    <sales-order-create-addproducts translate="message" module="adminhtml">
        <message>Add Products</message>
    </sales-order-create-addproducts>
</jstranslator>
```

Then in JS (admin and frontend):

```javascript
Translator.translate('Add Products');
// e.g. js/mage/adminhtml/sales.js:
var searchButton = new ControlButton(Translator.translate('Add Products'));
```

The `module="adminhtml"` attribute pins the lookup to `Mage_Adminhtml.csv`. The `<message>` text must appear verbatim in the CSV's first column to be translated.

## Inline translation

`Mage_Core_Model_Translate_Inline` (`app/code/core/Mage/Core/Model/Translate/Inline.php`) wraps each `__()` output in a `{{{result}}{{translated}}{{text}}{{module}}}` marker (four fields: result/translated/text/module) when enabled. Toggled per area:

- Frontend: `dev/translate_inline/active`
- Admin: `dev/translate_inline/active_admin`

When active, admin users get inline editors (`mage/translate_inline.js` + `.css`) over every translated string. Edits land in the `core_translate` DB table, which takes precedence over CSV files for that store. Disable in production.

## Common gotchas

- **CSV quoting.** Every field is quoted; a stray unescaped `"` corrupts the file. Double the quote (`""`) to escape.
- **Module name binding is by class, not file.** A block subclass under a different module gets that module's helper unless you set `module_name` data.
- **Don't translate variables.** `__($dynamicLabel)` won't appear in any CSV — translate the literal at definition time, or pre-register strings via dummy `__()` calls the translation harvester can find.
- **Cache.** Translations are cached under the `translate` cache type. Flush after editing a CSV.
- **Helpers are singletons.** `Mage::helper('catalog')` returns the same instance every call; don't mutate it.

## Cross-references

- `openmage-layout-blocks` — how blocks are constructed; `module_name` data key, `helper()` method.
- `openmage-module-structure` — where `etc/jstranslator.xml` lives, module name → helper alias mapping.
