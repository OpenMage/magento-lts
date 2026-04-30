---
name: openmage-controllers-routing
description: OpenMage frontend and adminhtml controllers, router config, action methods, form-key validation, request lifecycle (preDispatch/dispatch/postDispatch), loadLayout/renderLayout. Use when adding a controller or action, editing files under */controllers/**/*.php, configuring <routers> in config.xml, or wiring URL routes.
---

# OpenMage Controllers and Routing

Controllers live in lowercase `controllers/` directories, are classmap-autoloaded (NOT PSR-0), and are wired to URLs through `<routers>` blocks in `etc/config.xml`. Three URL segments — `frontName/controller/action` — map to `Vendor_Module_<Controller>Controller::<action>Action`.

## URL → controller resolution

For `https://example.com/catalog/product/view/id/42`:

1. `catalog` is the **frontName** declared in some module's `<routers>` block.
2. `product` selects `controllers/ProductController.php`.
3. `view` resolves to `viewAction()` (suffix added by `getActionMethodName($action) => $action . 'Action'`).
4. `id/42` becomes `$this->getRequest()->getParam('id')`.

Default frontend route (when URL has no path) comes from `<frontend><default><router>...</router></default>` — Catalog sets it to `catalog`, hence `/` lands on the CMS-home-or-catalog dispatcher.

## Frontend router config

`app/code/core/Mage/Catalog/etc/config.xml`:

```xml
<frontend>
    <routers>
        <catalog>
            <use>standard</use>
            <args>
                <module>Mage_Catalog</module>
                <frontName>catalog</frontName>
            </args>
        </catalog>
    </routers>
    <default>
        <router>catalog</router>
    </default>
</frontend>
```

- `<use>standard</use>` = frontend router (the only other in-tree value is `admin`).
- `<module>` is the **class prefix**, not a `<modules>` alias. Multiple modules can extend an existing frontName by adding a `<modules>` child with `<before>`/`<after>` to inject controllers.
- `<frontName>` is the URL segment.

## Adminhtml router config

`app/code/core/Mage/Adminhtml/etc/config.xml`:

```xml
<admin>
    <routers>
        <adminhtml>
            <use>admin</use>
            <args>
                <module>Mage_Adminhtml</module>
                <frontName>admin</frontName>
            </args>
        </adminhtml>
    </routers>
</admin>
```

Note the wrapping element is `<admin>`, not `<adminhtml>`. The `<adminhtml>` area block in the same file is used for other things (events, layout, ACL); routers go under `<admin>`.

To add admin controllers from another module, declare the router with the same key:

```xml
<admin>
    <routers>
        <adminhtml>
            <args>
                <modules>
                    <my_module before="Mage_Adminhtml">My_Module_Adminhtml</my_module>
                </modules>
            </args>
        </adminhtml>
    </routers>
</admin>
```

Then place controllers under `controllers/Adminhtml/...` with class prefix `My_Module_Adminhtml_...`.

## Classmap autoload nuance

The directory is **lowercase** `controllers/` but the class name uses the **module's underscore prefix without** a `Controllers_` segment:

| File path                                                | Class name                                |
|----------------------------------------------------------|-------------------------------------------|
| `Mage/Catalog/controllers/ProductController.php`         | `Mage_Catalog_ProductController`          |
| `Mage/Catalog/controllers/Product/CompareController.php` | `Mage_Catalog_Product_CompareController`  |
| `Mage/Adminhtml/controllers/Cms/PageController.php`      | `Mage_Adminhtml_Cms_PageController`       |

There is no `Controllers` segment in the class name even though the directory is `controllers/`. This is special-cased in the routing layer; the standard `Mage_Foo_Model_Bar` ↔ `Mage/Foo/Model/Bar.php` autoloader does **not** apply.

## Frontend action method

Real example, `Mage_Catalog_ProductController::viewAction()`:

```php
class Mage_Catalog_ProductController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        $categoryId     = (int) $this->getRequest()->getParam('category', false);
        $productId      = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        $viewHelper = Mage::helper('catalog/product_view');
        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $exception) {
            // ...
            $this->_forward('noRoute');
        }
    }
}
```

For a hand-rolled action use `loadLayout()` then `renderLayout()`:

```php
public function fooAction()
{
    $this->loadLayout();                       // build blocks from layout XML
    $this->_initLayoutMessages('core/session'); // optional: pull session messages
    $this->getLayout()->getBlock('head')->setTitle($this->__('Foo'));
    $this->renderLayout();                     // emit response body
}
```

`loadLayout()` accepts handle overrides (e.g. `loadLayout(['default', 'mymodule_foo'])`) and dispatches `controller_action_layout_load_before`, `controller_action_layout_generate_xml_before`, `controller_action_layout_generate_blocks_before/after`. `renderLayout()` dispatches `controller_action_layout_render_before` and `controller_action_layout_render_before_<full_action>`.

## Adminhtml controller and `_isAllowed()`

Default `_isAllowed()` in `Mage_Adminhtml_Controller_Action`:

```php
protected function _isAllowed(): bool
{
    if (is_bool(static::ADMIN_RESOURCE)) {
        return static::ADMIN_RESOURCE;
    }
    return Mage::getSingleton('admin/session')->isAllowed(static::ADMIN_RESOURCE);
}
```

Set `public const ADMIN_RESOURCE = 'cms/page';` on the controller class for the simple case. Override `_isAllowed()` only when the ACL resource depends on the action (e.g. read vs write):

```php
class Mage_Adminhtml_Cms_PageController extends Mage_Adminhtml_Controller_Action
{
    public const ADMIN_RESOURCE = 'cms/page';

    #[Override]
    protected function _isAllowed(): bool
    {
        $action = strtolower($this->getRequest()->getActionName());
        $aclPath = match ($action) {
            'new', 'save', 'massstatus' => self::ADMIN_RESOURCE . '/save',
            'delete', 'massdelete'      => self::ADMIN_RESOURCE . '/delete',
            default                     => self::ADMIN_RESOURCE,
        };
        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }
}
```

If `_isAllowed()` returns `false`, the framework forwards to `denied` silently — the user sees "Access Denied" with no clue which resource string failed. Always cross-check the ACL node exists in `etc/adminhtml.xml` (see `openmage-acl-adminhtml`).

## Form-key validation (CSRF)

Frontend POST handlers must call `_validateFormKey()`:

```php
public function addAction()
{
    if (!$this->_validateFormKey()) {
        $this->_goBack();
        return;
    }
    // ...mutate cart...
}
```

For admin controllers, `_setForcedFormKeyActions()` is called from `preDispatch()` to enforce form-key on a list of actions even when admin secret-key URLs are disabled:

```php
public function preDispatch()
{
    $this->_setForcedFormKeyActions(['delete', 'massDelete']);
    return parent::preDispatch();
}
```

Action names in this list are matched case-insensitively against the action portion of `getActionName()`. Always include destructive actions (`delete`, `massDelete`, etc.).

## Request lifecycle

`Mage_Core_Controller_Varien_Action::dispatch()`:

1. **`preDispatch()`** — install check, store-active check, session start, form-key/secret-URL checks, dispatches three events:
   ```php
   Mage::dispatchEvent('controller_action_predispatch', ['controller_action' => $this]);
   Mage::dispatchEvent('controller_action_predispatch_' . $routeName, [...]);
   Mage::dispatchEvent('controller_action_predispatch_' . $fullActionName, [...]);
   ```
   `controller_action_predispatch` is the most heavily-trafficked event in OpenMage — every request hits it. Don't subscribe globally without an early-return guard.
2. **`<action>Action()`** — your method runs only if `$request->isDispatched()` and `FLAG_NO_DISPATCH` is unset. `preDispatch()` can short-circuit by calling `setFlag('', self::FLAG_NO_DISPATCH, true)` and `_redirect(...)`/`_forward(...)`.
3. **`postDispatch()`** — three more events (mirroring predispatch):
   ```php
   Mage::dispatchEvent('controller_action_postdispatch_' . $fullActionName, [...]);
   Mage::dispatchEvent('controller_action_postdispatch_' . $routeName, [...]);
   Mage::dispatchEvent('controller_action_postdispatch', [...]);
   ```

Event taxonomy (in firing order for predispatch):
- **Generic:** `controller_action_predispatch` — every controller everywhere.
- **Route-scoped:** `controller_action_predispatch_<route>` — e.g. `controller_action_predispatch_catalog`.
- **Action-scoped:** `controller_action_predispatch_<full_action>` — e.g. `controller_action_predispatch_catalog_product_view`. `getFullActionName()` is `<route>_<controller>_<action>`, all lowercase, underscore-joined.

Postdispatch fires in reverse specificity (action → route → generic).

## `_redirect` and `_forward` semantics

- `_redirect($path, $arguments = [])` — sends a `Location:` header. `$path` is a URL path or a 3-segment route shorthand:
  - `'*/*/edit'` = same module/route, same controller, action `edit`. Stars are replaced with the **current** route/controller/action.
  - `'adminhtml/cms_page/edit'` = absolute. Hyphens vs underscores in the controller segment matter — `cms_page` matches `Cms/PageController`.
  - `_redirect('*/*/edit', ['id' => $id])` appends `/id/<id>/`.
- `_forward($action, $controller = null, $module = null, $params = null)` — internal redispatch, **no HTTP redirect, no new request**. Same `$_GET`/`$_POST`. Used heavily for `noRoute` / `denied` fallbacks. The next iteration re-runs `preDispatch()` for the new action.

## Pitfalls

- **Action suffix.** Method must be `fooAction()`, not `foo()`. The router calls `getActionMethodName($action)` which appends `Action`. A method without the suffix is invisible to routing.
- **`_isAllowed()` returning false silently.** Forwards to `denied`. Diff against the ACL XML if a freshly-added admin controller 403s.
- **`_redirect('*/*/edit')` from inside a redirect target.** The stars resolve against the **current** request, which after a forward may not be what you expect. Prefer absolute paths from `preDispatch()`.
- **Lowercase `controllers/` directory.** Mixed-case (`Controllers/`) breaks classmap autoload on case-sensitive filesystems even though it works on macOS/Windows.
- **Controller class prefix is the module's underscore name, not `Vendor_Module_Controllers_`.** `Mage_Catalog_ProductController` is correct; `Mage_Catalog_Controllers_ProductController` is not.
- **Form key on GET-driven destructive actions.** The admin secret-key URL feature can disable form-key checks; `_setForcedFormKeyActions()` is the safety net — use it for any destructive action.
- **Subscribing to `controller_action_predispatch` globally.** Fires on *every* request including AJAX, IPN endpoints, and the install wizard. Either scope to `_<route>` / `_<full_action>`, or guard early.

## Cross-references

- `openmage-acl-adminhtml` — `etc/adminhtml.xml`, ACL resource strings, the menu/resources tree that `_isAllowed()` checks against.
- `openmage-layout-blocks` — layout handles, what `loadLayout()`/`renderLayout()` actually do, block resolution.
- `openmage-events-observers` — observer wiring for the `controller_action_*` events documented above.
- `openmage-module-structure` — where `<routers>` config lives in the module XML hierarchy and how multi-module router merging works.
- `mage-module-adminhtml` — admin grid/form/container conventions, mass-action wiring, form-key generation in the admin UI.
