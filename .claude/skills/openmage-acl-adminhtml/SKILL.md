---
name: openmage-acl-adminhtml
description: OpenMage admin ACL — etc/adminhtml.xml, ACL resource tree, _isAllowed() checks, menu and admin permission wiring. Use when editing files under */etc/adminhtml.xml, adding an admin permission or menu entry, implementing _isAllowed() in admin controllers, or registering a new admin page.
---

# openmage-acl-adminhtml

Admin ACL and menu wiring for OpenMage. Every admin page is gated by an ACL resource path; the same `etc/adminhtml.xml` file declares the menu entry and the ACL node, and a controller's `_isAllowed()` joins them to a resource string. Default is **deny** — a user role only sees pages whose resource it has been granted.

## Two trees in `etc/adminhtml.xml`

`<config>` has two top-level sections: `<menu>` (what's visible in the admin nav) and `<acl>/<resources>/<admin>/<children>` (what's permission-gated). They mirror each other but are independent — adding a menu entry without an ACL node leaves the page reachable only by users with `all` permission; adding an ACL node without a menu entry is fine for hidden / linked-from-elsewhere pages.

Top-level skeleton lives in `app/code/core/Mage/Adminhtml/etc/adminhtml.xml`:

```xml
<config>
    <menu>
        <system translate="title" module="adminhtml">
            <title>System</title>
            <sort_order>90</sort_order>
            <children>
                <config translate="title">
                    <title>Configuration</title>
                    <action>adminhtml/system_config</action>
                    <sort_order>110</sort_order>
                </config>
            </children>
        </system>
    </menu>
    <acl>
        <resources>
            <all><title>Allow everything</title></all>
            <admin translate="title" module="adminhtml">
                <title>OpenMage Admin</title>
                <children>
                    <system translate="title">
                        <title>System</title>
                        <children>
                            <config translate="title">
                                <title>Configuration</title>
                                <children>
                                    <!-- system.xml sections nest here -->
                                </children>
                            </config>
                        </children>
                    </system>
                </children>
            </admin>
        </resources>
    </acl>
</config>
```

Module files merge into this tree. From `app/code/core/Mage/Catalog/etc/adminhtml.xml`:

```xml
<config>
    <menu>
        <catalog translate="title" module="catalog">
            <title>Catalog</title>
            <sort_order>30</sort_order>
            <depends><module>Mage_Catalog</module></depends>
            <children>
                <products translate="title" module="catalog">
                    <title>Manage Products</title>
                    <action>adminhtml/catalog_product/</action>
                    <sort_order>0</sort_order>
                </products>
            </children>
        </catalog>
    </menu>
    <acl>
        <resources>
            <admin>
                <children>
                    <system><children><config><children>
                        <catalog translate="title" module="catalog">
                            <title>Catalog</title>     <!-- system.xml section "catalog" -->
                        </catalog>
                    </children></config></children></system>
                    <catalog translate="title" module="catalog">
                        <title>Catalog</title>
                        <sort_order>30</sort_order>
                        <children>
                            <products translate="title"><title>Manage Products</title></products>
                            <categories translate="title"><title>Manage Categories</title></categories>
                            <update_attributes translate="title"><title>Update Attributes</title></update_attributes>
                        </children>
                    </catalog>
                </children>
            </admin>
        </resources>
    </acl>
</config>
```

Note the dual placement: a system-config section gets a node under `admin/system/config/<section>`, and the menu/feature gets its own top-level branch under `admin/<module>`.

## Resource path strings

Resource paths are slash-joined node names, with or without a leading `admin/` (the session normalizes either form — see `Mage_Admin_Model_Session::isAllowed()`):

- `catalog/products` → `admin/catalog/products` ACL node.
- `system/config` → the Configuration menu / page.
- `admin/system/config/catalog` → ACL gate for the **Catalog** section in System → Configuration. This is the form `Mage_Adminhtml_System_ConfigController::_isSectionAllowed()` builds: `"admin/system/config/{$section}"` where `$section` is the `id` of the section in `etc/system.xml`.

A system.xml `<sections><catalog>...` element matches the ACL node at `<acl>/<resources>/<admin>/<children>/<system>/<children>/<config>/<children>/<catalog>`. Without that ACL node, the section page is effectively denied for any non-`all` role.

## `_isAllowed()` in admin controllers

Base class — `app/code/core/Mage/Adminhtml/Controller/Action.php`:

```php
public const ADMIN_RESOURCE = 'admin';     // override per controller

protected function _isAllowed(): bool
{
    if (is_bool(static::ADMIN_RESOURCE)) {
        return static::ADMIN_RESOURCE;
    }
    return Mage::getSingleton('admin/session')->isAllowed(static::ADMIN_RESOURCE);
}
```

`preDispatch()` calls `_isAllowed()` and `_forward('denied')` on false. Subclasses just override the constant:

```php
// Mage_Adminhtml_Catalog_ProductController
class Mage_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    public const ADMIN_RESOURCE = 'catalog/products';
}

// Mage_Adminhtml_PromoController
class Mage_Adminhtml_PromoController extends Mage_Adminhtml_Controller_Action
{
    public const ADMIN_RESOURCE = 'promo';
}
```

When a controller's actions need different resources, override `_isAllowed()` with a `match` on `getActionName()`:

```php
// Mage_Adminhtml_ReportController
#[Override]
protected function _isAllowed(): bool
{
    $action = strtolower($this->getRequest()->getActionName());
    $aclPath = match ($action) {
        'search' => 'report/search',
        default  => 'report',
    };
    return Mage::getSingleton('admin/session')->isAllowed($aclPath);
}
```

## How the role check resolves

`Mage_Admin_Model_Session::isAllowed()` (`app/code/core/Mage/Admin/Model/Session.php`):

```php
public function isAllowed($resource, $privilege = null)
{
    $user = $this->getUser();
    $acl  = $this->getAcl();
    if ($user && $acl) {
        if (!preg_match('/^admin/', $resource)) {
            $resource = 'admin/' . $resource;
        }
        try {
            return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
        } catch (Exception) { /* unknown resource → fall through to false */ }
    }
    return false;
}
```

Role rules are stored as rows in `admin_rule` (one per role × resource), with `permission` = `allow` or `deny` (`Mage_Admin_Model_Rules::RULE_PERMISSION_ALLOWED` / `_DENIED`). `Mage_Admin_Model_Acl_Config` builds the Zend_Acl tree from the merged `<acl>` XML at session load; rules apply on top.

Consequence: **a missing ACL node returns false from `isAllowed`** — the `catch (Exception)` swallows `Zend_Acl_Resource_Not_Found_Exception` and falls through. New admin pages without an ACL node are effectively unreachable for any role except `all`.

## Common task: register a new admin page

A new admin page typically needs four edits — cross-link these skills:

1. **Controller routing** — declare the admin router under the module's `<config><admin><routers>`, then add `controllers/MyController.php`. See `openmage-controllers-routing`.
2. **Menu entry** — `<menu>` block in `etc/adminhtml.xml` with `<action>routerfront/controller/action</action>`.
3. **ACL resource** — matching `<acl><resources><admin><children>` node. Set `ADMIN_RESOURCE` on the controller to its slash-path.
4. **System config (optional)** — if exposing settings, add a section in `etc/system.xml` and a mirror node under `admin/system/config/<section>` in the ACL. See `openmage-system-config`.

The admin Permissions UI (System → Permissions → Roles → Resources tab) auto-discovers the merged ACL tree, so a new node shows up as a checkbox after a config-cache flush.

## Gotchas

- **Default deny.** Don't ship without the ACL node — it's not "open by default".
- **`ADMIN_RESOURCE = true`** opens the controller to anyone logged in (the `is_bool` shortcut). `ADMIN_RESOURCE = false` denies everyone. Use sparingly.
- **System-config sections** need both an `<acl>` node *and* the section's own `<resource>` is **not** declared in `system.xml` itself — the ACL resource string is built from the section id. Section id `foo` → ACL node `admin/system/config/foo`.
- **`module="catalog"` on `<title>`** controls which translation CSV resolves the label. Match the module that owns the entry.
- **Cache flush.** ACL/menu changes are cached in `config`; clear the config cache or `var/cache/` after editing `etc/adminhtml.xml`.
- **Orphaned resources.** System → Permissions → Orphaned Role Resources lists ACL paths granted to a role but no longer present in the merged tree — useful sanity check after renames.

## Cross-references

- `openmage-controllers-routing` — admin router config, `Mage_Adminhtml_Controller_Action`, form-key validation.
- `openmage-system-config` — `etc/system.xml` sections / groups / fields and how their ACL gating works.
- `mage-module-adminhtml` — admin grid / form / container blocks and the Permissions UI.
- `openmage-module-structure` — where `etc/adminhtml.xml` sits in a module and how it merges.
