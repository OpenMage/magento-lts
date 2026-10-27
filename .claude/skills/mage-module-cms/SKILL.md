---
name: mage-module-cms
description: OpenMage CMS — pages, static blocks, widgets, WYSIWYG/TinyMCE directives ({{block}}, {{store url}}, {{media url}}), Cms_Helper_Page routing, widget.xml declarations. Use when editing under Mage_Cms/ or Mage_Widget/, working with cms/* aliases, debugging WYSIWYG directive parsing, or registering a widget type.
---

# Mage_Cms / Mage_Widget

Three storage models, one directive parser, one widget registry. Edits to `Mage_Cms_*`, `Mage_Widget_*`, `cms/*` aliases, `widget.xml`, or WYSIWYG content land here.

## Three storage models

| Concept       | Alias                  | Class                               | Tables                                                | Routing                |
|---------------|------------------------|-------------------------------------|-------------------------------------------------------|------------------------|
| Page          | `cms/page`             | `Mage_Cms_Model_Page`               | `cms_page`, `cms_page_store`                          | URL = `identifier`     |
| Static block  | `cms/block`            | `Mage_Cms_Model_Block`              | `cms_block`, `cms_block_store`                        | none — referenced      |
| Widget instance | `widget/widget_instance` | `Mage_Widget_Model_Widget_Instance` | `widget_instance`, `widget_instance_page`, `widget_instance_page_layout` | bound to layout handles |

Pages and blocks are store-scoped via the `*_store` join table (multi-select on save). Widget instances scope via `store_ids` (CSV) and a `page_groups` array of associative entries ({group, layout_handle, block_reference, for, entities, template}).

## Routing CMS pages

- Default router: `Mage_Cms_Controller_Router` matches a request path to `cms_page.identifier` and dispatches to `cms/page/view` with `page_id` (the home page goes through `cms/index/index`).
- Special pages live in core config and are resolved by `Mage_Cms_Helper_Page`:
  - `web/default/cms_home_page` → home page (`XML_PATH_HOME_PAGE`)
  - `web/default/cms_no_route` → 404 (`XML_PATH_NO_ROUTE_PAGE`, page id `no-route` constant `Mage_Cms_Model_Page::NOROUTE_PAGE_ID`)
  - `web/default/cms_no_cookies` → cookie-disabled landing
- Direct render from a controller action:
  ```php
  Mage::helper('cms/page')->renderPage($this, $pageId);     // frontend
  Mage::helper('cms/page')->renderPageExtended($this, $id, $renderLayout = true); // admin / preview (pass false to skip renderLayout)
  ```
- Layout handles added by `_renderPage()`: `default`, `cms_page`, plus the page's `root_template` handle and any `custom_layout_update_xml`. Event: `cms_page_render`.

## Directive parsing (WYSIWYG / email)

`Mage_Cms_Model_Template_Filter extends Mage_Core_Model_Email_Template_Filter`. Same syntax everywhere CMS content is rendered (pages, blocks, transactional emails). `Filter::filter($value)` runs the directive sweep over WYSIWYG output before it hits the template.

Common directives (each implemented as `<name>Directive($construction)` on the filter):

```
{{block type="catalog/product_new" template="catalog/product/new.phtml"}}
{{block id="footer_links"}}                       Static block by identifier (cms/block)
{{widget type="cms/widget_page_link" page_id="3"}}
{{store url="customer/account/login"}}            Frontend URL on current store
{{store direct_url="some-page"}}
{{media url="wysiwyg/banner.jpg"}}                Resolves under /media/
{{skin url="images/logo.gif"}}                    Theme skin URL
{{config path="general/store_information/name"}}  Store config value
{{var customer.name}}                             Email var
{{htmlescape var=$something}}
{{protocol}} {{depend}} {{if}}                    Flow-control directives
```

`Mage_Cms_Model_Block::_beforeSave()` blocks self-recursion (`block_id="N"` inside block N).

## WYSIWYG / TinyMCE

- Toggle: `cms/wysiwyg/enabled` (`Mage_Cms_Model_Wysiwyg_Config::WYSIWYG_CONFIG_ENABLED`). Values: `enabled`, `hidden`, `disabled`.
- Config assembled in `Mage_Cms_Model_Wysiwyg_Config::getConfig()`; event `cms_wysiwyg_config_prepare` lets observers extend it.
- Image picker writes to `media/wysiwyg/` (`Mage_Cms_Helper_Wysiwyg_Images`); admin endpoint `*/cms_wysiwyg_images/index`.
- Directive preview round-trip endpoint: `*/cms_wysiwyg/directive` (decodes a base64 directive, renders, returns image/html).

## Widgets

Two layers — declaration vs instance.

### Declaration: `etc/widget.xml`

Skeleton (one `<widgets>` per module, one entry per type):

```xml
<widgets>
    <cms_static_block type="cms/widget_block" translate="name description" module="cms">
        <name>CMS Static Block</name>
        <description>Contents of a Static Block</description>
        <!-- cms_static_block in core has no <is_email_compatible> tag (only cms_page_link does) -->
        <parameters>
            <block_id type="complex" translate="label">
                <visible>1</visible>
                <required>1</required>
                <label>Block</label>
                <type>label</type>
                <helper_block>                            <!-- chooser popup -->
                    <type>adminhtml/cms_block_widget_chooser</type>
                </helper_block>
            </block_id>
            <template translate="label">
                <type>select</type>
                <value>cms/widget/static_block/default.phtml</value>
                <values><default><value>cms/widget/static_block/default.phtml</value><label>Default</label></default></values>
            </template>
        </parameters>
    </cms_static_block>
</widgets>
```

`type=` is a block alias resolving to a class extending `Mage_Core_Block_Abstract` (typically `Mage_Core_Block_Template`) that also implements `Mage_Widget_Block_Interface`. Parameters are passed in via `setData()` before render.

Built-in CMS widgets: `cms/widget_page_link`, `cms/widget_block`. Reports/Catalog/Sales/Newsletter ship their own `widget.xml` files.

### Instances: `widget_instance` table

Created in admin → CMS → Widgets. Each row binds:
- `type` (the `widget.xml` key, e.g. `cms_static_block`)
- `widget_parameters` (serialized array, the `<parameters>` values)
- `store_ids` (CSV)
- `page_groups[]` rows in `widget_instance_page` — one of `pages`, `all_pages`, `anchor_categories`, `notanchor_categories`, or `<type>_products` (e.g. `all_products`, `simple_products`, ...). Each carries a `layout_handle` (e.g. `cms_index_index`, `catalog_product_view`) and `block_reference` (where to inject; e.g. `content`, `sidebar.additional`).
- Persisted layout updates land in `core_layout_update` joined via `widget_instance_page_layout`.

The inline `{{widget ...}}` directive bypasses the instance table and renders ad-hoc from parameters.

## ACL split

`Mage_Cms` declares its own ACL subtree (`app/code/core/Mage/Cms/etc/adminhtml.xml`):

```
admin/cms/page/save
admin/cms/page/delete
admin/cms/block/save
admin/cms/block/delete
admin/cms/media_gallery
```

A role can grant CMS authoring without granting `system/config` or `catalog`. Adminhtml controllers (`Mage_Adminhtml_Cms_PageController`, `Mage_Adminhtml_Cms_BlockController`) check via `_isAllowed()` using these node paths. Widget instance editing lives under `admin/cms/widget_instance` ACL (see `Mage_Widget`'s `adminhtml.xml`).

## Common tasks

- **Add a new widget type:** create `etc/widget.xml` with a `<my_module_thing type="mymodule/widget_thing">` block; create the block class extending `Mage_Core_Block_Template implements Mage_Widget_Block_Interface`; the `<parameters>` show up in the chooser automatically.
- **Add a directive:** subclass `Mage_Cms_Model_Template_Filter` (or `Mage_Core_Model_Email_Template_Filter` for email-only), add `fooDirective($construction)`, register via `<global><cms><page><tempate_filter>` (yes, the typo is in core — see `Mage_Cms_Helper_Data::XML_NODE_PAGE_TEMPLATE_FILTER`); subclass + add `xxxDirective()` methods. Pattern: `{{foo bar="baz"}}` → method receives `$construction[2]` of params.
- **Programmatic block render:** `Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('footer_links')->toHtml();`
- **Programmatic page URL:** `Mage::helper('cms/page')->getPageUrl($pageId)`.
- **Bust cache after content edits:** pages/blocks tag with `cms_page` / `cms_block` (`CACHE_TAG` constants); FPC and `block_html` invalidate via `_cacheTag = 'cms_page'`/`'cms_block'` on the page/block models (and `Mage_PageCache` observers when EE FPC is present); `Mage_Cms_Model_Observer` only handles `noRoute`/`noCookies`.

## Gotchas

- Static block content containing `{{block id="X"}}` referencing block X is rejected at save (`_beforeSave`).
- `Cms_Helper_Page::_renderPage()` calls `addActionLayoutHandles()` *and* `cms_page` — your custom layout XML for `<cms_page>` always applies; per-page customization comes from `cms_page.layout_update_xml` / `custom_layout_update_xml` (no per-page handles are added).
- Widget parameters with `helper_block` need a backend chooser block under `Block/Adminhtml/.../Widget/Chooser/`. Without it the field renders blank.
- The directive parser runs *once* on output. Nested directives in DB content work; directives in widget *parameter values* don't get a second pass — pre-render them or use a typed parameter.
- `is_email_compatible=1` is required for a widget to be usable inside `{{widget}}` in transactional email content (no admin chooser there).
- Widget instance `store_ids` is stored as a CSV string but `getStoreIds()` returns an array — see `Instance::_beforeSave()`.

## Cross-refs

- `openmage-layout-blocks` — block aliases, layout handles, `_prepareLayout`. Widget instance binding leans on these.
- `openmage-acl-adminhtml` — the split ACL tree above; `_isAllowed()` mechanics.
- `openmage-caching` — `cms_page` / `cms_block` cache tags and FPC interaction.
- `openmage-translations` — CMS content is *not* run through `__()` automatically; translate at authoring time or via a directive.
