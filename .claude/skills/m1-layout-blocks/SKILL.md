---
name: m1-layout-blocks
description: Magento 1 layout XML, blocks, .phtml templates, design fallback chain, escaping discipline. Use when editing files under app/design/**/layout/*.xml, Block/**/*.php, or template/**/*.phtml; adding a block or template; working with layout handles like catalog_product_view; or fixing PHPStan errors in .phtml files.
---

# Magento 1 Layout, Blocks, Templates

Layout XML wires named blocks into handles; blocks are PHP classes (alias-resolved); `.phtml` templates render block data. Variables in templates come from the **block** via magic getters — *not* from globals. PHPStan analyzes `.phtml` files, so the `@method` discipline on Block classes is load-bearing, not cosmetic.

## Layout handles, references, blocks

A handle is a top-level XML element matched by route, action, or explicit `addHandle()`. The `catalog_product_view` handle (every product page) is in `app/design/frontend/base/default/layout/catalog.xml`:

```xml
<catalog_product_view translate="label">
    <label>Catalog Product View (Any)</label>
    <reference name="root">
        <action method="setTemplate"><template>page/2columns-right.phtml</template></action>
    </reference>
    <reference name="content">
        <block type="catalog/product_view" name="product.info" template="catalog/product/view.phtml">
            <block type="catalog/product_view_media" name="product.info.media" as="media"
                   template="catalog/product/view/media.phtml"/>
            <block type="catalog/product_view" name="product.info.addtocart" as="addtocart"
                   template="catalog/product/view/addtocart.phtml"/>
        </block>
    </reference>
</catalog_product_view>
```

Key distinctions:

- `<block>` declares a new block. `<reference name="X">` mutates an already-declared block named `X` (typically inserts children or calls actions).
- `name=` is the global, layout-wide id (`product.info.addtocart`). Must be unique; how `getBlock()`/`<reference>` find it.
- `as=` is the **child alias** within the parent (`addtocart`). How the parent fetches via `$this->getChildHtml('addtocart')`. Multiple blocks can share an `as` across different parents.
- `<action method="X">` calls `$block->X(...)` after instantiation — each child element of `<action>` is one positional argument. Used for `setTemplate`, `addJs`, `setColumnCount`, etc.
- `<update handle="other_handle"/>` pulls another handle's instructions into the current one. `<remove name="X"/>` deletes a block from the tree.
- Layout handles, block names, and block types (`catalog/product_view`) are **public surface** per AGENTS.md — don't rename without a passthrough.

## Block resolution: alias to class

`<block type="catalog/product_view" .../>` resolves through the alias declared in `Mage_Catalog`'s `etc/config.xml` under `<global><blocks><catalog><class>Mage_Catalog_Block</class></blocks></global>` plus the suffix → `Mage_Catalog_Block_Product_View` at `app/code/core/Mage/Catalog/Block/Product/View.php`:

```php
/**
 * @method string getCustomAddToCartPostUrl()
 * @method int    getProductId()
 * @method bool   hasCustomAddToCartPostUrl()
 * @method $this  setCustomAddToCartUrl(string $value)
 */
class Mage_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_Abstract
{
    #[Override]
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('catalog/breadcrumbs');
        $headBlock = $this->getLayout()->getBlock('head');
        // ... set title/keywords/description from product
        return parent::_prepareLayout();
    }

    #[Override]
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }
}
```

`_prepareLayout()` runs after the block is added to the layout but before render — the place to fetch siblings (`getLayout()->getBlock('head')`), inject children, or set page-level data. Do not put rendering logic here.

## Structural vs visual blocks

- **Structural** blocks (`root`, `head`, `content`, `left`, `right`, `footer`) are declared in `page.xml` and exist in every handle. You attach to them via `<reference>`.
- **Visual** blocks (`catalog/product_view`, anything with a template) are leaves you add. Visual blocks render via `getChildHtml('alias')` on the parent template, or `getChildChildHtml`, or `<action method="append">`/`<action method="insert">` from layout.
- `setChild('alias', $blockInstance)` and `unsetChild('alias')` are the runtime equivalents. Layout XML is the preferred declarative path; PHP-side child manipulation is for cases where the children are dynamic (e.g. payment method blocks).

## Templates: variables come from the block

In a `.phtml`, `$this` **is** the block instance. There are no template-scoped globals — every value comes from a block accessor. The first lines of `app/design/frontend/base/default/template/catalog/product/view.phtml` declare the contract:

```php
<?php
/**
 * @see Mage_Catalog_Block_Product_View
 * @var Mage_Catalog_Block_Product_View $this
 */
?>
<?php $_helper = $this->helper('catalog/output'); ?>
<?php $_product = $this->getProduct(); ?>
<div class="product-view">
    <div class="product-name">
        <h1><?php echo $_helper->productAttribute($_product, $_product->getName(), 'name') ?></h1>
    </div>
    <?php echo $this->getChildHtml('alert_urls') ?>
    <?php if ($this->canEmailToFriend()): ?>
        <p><a href="<?php echo $this->helper('catalog/product')->getEmailToFriendUrl($_product) ?>">
            <?php echo $this->__('Email to a Friend') ?></a></p>
    <?php endif ?>
</div>
```

`$this->getProduct()` and `$this->canEmailToFriend()` are real methods on the block. `$this->getProductId()` would be a magic `__call` accessor — and that's exactly why the `@method int getProductId()` line on the block class above exists: it tells PHPStan and IDEs the accessor is real.

When you add a new template variable, you do not "pass" it — you either:
1. Add a method to the block class, or
2. Set a data key on the block (`$block->setFoo($x)`) and add `@method` to the docblock so `$this->getFoo()` is typed.

The `@var Mage_Catalog_Block_Product_View $this` PHPDoc at the top of templates is the hint that lets PHPStan resolve `$this->...` calls. Always include it on new templates.

## Escaping in .phtml

Block accessors return raw data — escape at the template boundary:

- `$this->escapeHtml($value)` — text inside HTML
- `$this->escapeHtml($value, $allowedTags)` — limited HTML allowed
- `$this->escapeUrl($url)` — `href`/`src` values
- `$this->escapeUrlAttribute($url)` — same context, double-encoded for attribute value (newer helper)
- `$this->escapeJsQuote($value)` — inline JS strings
- `$this->__('Translate %s', $name)` — localized; auto-escapes its arguments via sprintf-style substitution but the result is **not** HTML-escaped, so wrap in `escapeHtml` if it contains user data.

A lot of legacy core templates use bare `<?php echo $foo->getBar() ?>` — not a template to copy. New code escapes; if you're already touching an unsafe line, fix it.

## Design fallback chain

`Mage_Core_Model_Design_Fallback::getFallbackScheme()` decides where to look. Two modes coexist:

**Inherited (declared via `theme.xml`).** If `app/design/<area>/<package>/<theme>/etc/theme.xml` declares `<parent>pkg/theme</parent>`, the chain follows parents. Adminhtml uses this:

- `app/design/adminhtml/openmage/default/etc/theme.xml` → `<parent>default/default</parent>`
- `app/design/adminhtml/default/default/etc/theme.xml` → `<parent>base/default</parent>`
- `app/design/adminhtml/base/default/etc/theme.xml` → `<parent />` (root)

So an admin lookup with `package=openmage, theme=default` walks: `openmage/default` → `default/default` → `base/default`.

**Legacy (no `<parent>` declared).** `_getLegacyFallbackScheme()` returns the current package's configured-fallback theme then `base/default`:

```php
return [
    [],
    ['_theme' => $this->_getFallbackTheme()],          // e.g. 'default' under current package
    ['_theme' => Mage_Core_Model_Design_Package::DEFAULT_THEME],  // 'default'
];
```

Combined with the `_package`/`_theme` defaults that surround it, the effective frontend chain when no `theme.xml` parent is declared is `<package>/<theme>` → `<package>/default` → `base/default`. The frontend `rwd/default` and `base/default` themes both use `<parent />` — i.e. legacy mode — so frontend themes typically still rely on the implicit `<package>/default` → `base/default` tail.

When a file (layout XML, template, skin asset) is missing in the requested theme, the package model walks the scheme until it finds a hit; final fallback is always `base/default`. **Never put fixes only in `base/default`** if a package-specific override exists higher up — the higher one wins and you'll think your edit didn't load.

## PHPStan and `.phtml`

`.phpstan.dist.neon`:

```neon
parameters:
    level: 8
    fileExtensions:
      - php
      - phtml
    paths:
        - app/design/
```

Consequences:

- Every `.phtml` under `app/design/` is analyzed. Undeclared variables, calls to nonexistent block methods, and bad types fail the build.
- The `@var Mage_Foo_Block_Bar $this` PHPDoc at the top of a template is what gives PHPStan a type for `$this`. Without it, every accessor is unknown.
- Magic accessors (`getFoo`, `setFoo`, `hasFoo`) are only "real" to PHPStan if the block class has a matching `@method` entry. When you add a new data key, add the `@method` line to the block class docblock — `composer run phpstan:test` enforces it.
- Some directories are explicitly baselined in `.phpstan.dist.neon` (`identifier: variable.undefined` is suppressed under `app/design/*/*/template/*`, `errors/*`, `lib/Varien/*`). New code does not get this exemption — write it tight.
- `composer run phpstan:test` is the gate; regenerate split baselines via `composer run phpstan:baseline` after changes that move the count.

## Adding a new template

1. Identify the block class (or pick an existing one like `core/template`).
2. If new keys are needed, add `@method` entries to the block docblock — type each accessor.
3. Place the `.phtml` under `app/design/<area>/<package>/<theme>/template/<module>/...`. Frontend code typically goes in `base/default`; admin overrides go in `default/default` or `openmage/default`.
4. Top of file: 4-line copyright header, then `/** @var BlockClass $this */`.
5. Reference it from layout: `<block type="alias" name="..." template="<module>/<file>.phtml"/>` or `<action method="setTemplate">` on an existing block.
6. Escape at every echo. Translate user-facing strings via `$this->__()`.
7. `composer run phpstan:test` before pushing.

## Adding a new block class

1. Decide the alias and module. New blocks under `Mage_*` follow `app/code/core/Mage/<Module>/Block/...` — `Mage_Foo_Block_Bar_Baz` ↔ `Block/Bar/Baz.php`.
2. Extend `Mage_Core_Block_Template` (templated) or `Mage_Core_Block_Abstract` (logic-only).
3. Document magic accessors with `@method` from day one.
4. Public/protected method signatures count as public API per AGENTS.md BC rules — no breaking changes to existing blocks. Add new methods, don't reshape old ones.
5. Wire via `<global><blocks><foo><class>Mage_Foo_Block</class></blocks></global>` if it's a new module; otherwise the existing alias prefix routes the lookup.

## Cross-references

- `m1-controllers-routing` — `loadLayout()`/`renderLayout()`, where layout handles get added, the dispatch flow.
- `m1-translations` — `__()` resolution, the helper-vs-block translation distinction, `jstranslator.xml`.
- `mage-adminhtml` — admin grid/form/container/tabs block conventions, mass-actions, form key.
