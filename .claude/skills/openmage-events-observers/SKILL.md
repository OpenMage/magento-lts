---
name: openmage-events-observers
description: OpenMage events and observers — Mage::dispatchEvent, observer wiring in etc/config.xml, area scoping, observer method signatures, common high-traffic event names. Use when adding an observer, hooking into core behavior, editing <events> config blocks, working with Observer.php files, or asking how to react to sales_order_save_after / checkout_cart_save_after / similar events.
---

# OpenMage Events and Observers

How to subscribe to (and rarely, dispatch) events in this OpenMage / magento-lts codebase. Read this before adding a new event name, wiring an observer block in `etc/config.xml`, or writing an `Observer.php` method.

Cross-refs:
- `openmage-module-structure` — where the `<events>` block lives in `etc/config.xml`, area scoping rules, alias wiring for `<class>`.
- `openmage-controllers-routing` — controller-action events (`controller_action_predispatch`, etc.) and `<frontend>`/`<adminhtml>` area resolution.

## Mental model

`Mage::dispatchEvent($name, $args)` walks every registered observer for that event in every active area, instantiates the observer class, and calls the configured method with a `Varien_Event_Observer`. Observers are wired entirely in XML — there is no runtime registration in normal application code.

There are roughly **~300 distinct event names dispatched in core**. Before inventing a new one, grep for an existing event that fires at the right moment — almost any model save/load/delete and most controller/admin lifecycle points already have one.

```bash
grep -rh "Mage::dispatchEvent(" app/code/core/Mage/ \
  | grep -oP "dispatchEvent\('[^']+'" | sort -u
```

## Observer registration (XML)

The `<events>` block lives inside an **area** (`<global>`, `<frontend>`, `<adminhtml>`, `<crontab>`) inside the module's `etc/config.xml`. The area determines when the observer fires (see "Area scoping" below).

Real example from `app/code/core/Mage/Sales/etc/config.xml`:

```xml
<global>
    <events>
        <sales_order_place_after>
            <observers>
                <sales_vat_request_params_order_comment>
                    <class>sales/observer</class>
                    <method>addVatRequestParamsOrderComment</method>
                </sales_vat_request_params_order_comment>
            </observers>
        </sales_order_place_after>
    </events>
</global>
```

Schema:

```
<events>
  <EVENT_NAME>                              <!-- the dispatched event name -->
    <observers>
      <UNIQUE_OBSERVER_KEY>                 <!-- arbitrary, must be unique per event across modules -->
        <type>singleton</type>              <!-- optional; default is singleton (see below) -->
        <class>module_alias/observer</class><!-- model alias OR fully qualified class name -->
        <method>methodName</method>         <!-- method on the observer class -->
      </UNIQUE_OBSERVER_KEY>
    </observers>
  </EVENT_NAME>
</events>
```

Notes:
- `<class>` accepts a model alias (`sales/observer` → `Mage_Sales_Model_Observer`) or a literal class name. Aliases are wired in `<global><models>` — see `openmage-module-structure`.
- The observer key (`<sales_vat_request_params_order_comment>` above) must be unique across all merged config for that event. Collisions silently overwrite the earlier one. Convention: `<vendor>_<module>_<purpose>`.
- `<type>disabled</type>` turns an inherited observer off — used to suppress a core observer from a custom module by re-declaring the **same observer key** with type `disabled`.

## Area scoping

The `<events>` block must be nested inside one of these top-level areas. The area determines when the observer is loaded:

| Area          | Fires for                                                                                |
| ------------- | ---------------------------------------------------------------------------------------- |
| `<global>`    | Always — frontend, admin, API, CLI, cron.                                                |
| `<frontend>`  | Storefront requests only (anything routed through `index.php` to a frontend controller). |
| `<adminhtml>` | Admin panel requests only.                                                               |
| `<crontab>`   | Cron jobs only — events dispatched while running inside `cron.php`.                      |

If you put a `<frontend>` observer registration on a `model_save_after` event, it will not fire when an admin user saves or when a CLI script does. Common bug.

When in doubt, use `<global>`. Use `<frontend>` / `<adminhtml>` only when the work specifically must not happen in the other area (e.g. session manipulation, redirect logic).

## Observer class and method

Convention: `app/code/core/Mage/<Module>/Model/Observer.php`, class `Mage_<Module>_Model_Observer`, alias `<module>/observer`. Instance methods take a `Varien_Event_Observer`:

```php
public function addVatRequestParamsOrderComment(Varien_Event_Observer $observer)
{
    /** @var Mage_Sales_Model_Order $orderInstance */
    $orderInstance = $observer->getOrder();
    // equivalently: $observer->getEvent()->getOrder();
    // or:           $observer->getEvent()->getData('order');

    // ... mutate $orderInstance, dispatch follow-on work, etc.
}
```

Data extraction:
- `$observer->getEvent()->getFoo()` — args passed to `dispatchEvent('name', ['foo' => $x])` are exposed as magic getters on the event object.
- `$observer->getFoo()` — `Varien_Event_Observer` mirrors event data onto itself (and merges in any `<args>` from the XML block) for convenience.
- `$observer->getEvent()->getData()` returns the raw array.

Return value is ignored. To "abort" an event-driven flow, mutate the passed object (e.g. `$transport->setIsValid(false)`, `$product->setIsSalable(false)`) or throw — there is no early-stop signal from a return value.

## `singleton` vs `model` (vs `object`)

The `<type>` element controls observer instantiation per dispatch (resolved in `Mage_Core_Model_App::dispatchEvent`):

| `<type>` value | Behavior                                                                                                              |
| -------------- | --------------------------------------------------------------------------------------------------------------------- |
| *(omitted)*    | **Default. Singleton.** `Mage::getSingleton($class)` — shared instance across the request. Almost always correct.     |
| `singleton`    | Same as default.                                                                                                      |
| `model`        | `Mage::getModel($class)` — fresh instance every dispatch. Use only if the observer holds per-call state to discard.   |
| `object`       | Alias for `model`.                                                                                                    |
| `disabled`     | Skip this observer entirely. Used in overrides.                                                                       |

In practice: **omit `<type>` unless you have a specific reason**. `Mage_Sales_Model_Observer` is dispatched dozens of times per request — making it `model` would needlessly construct it each time. If your observer class implements `Mage_Core_Observer_Interface`, the dispatcher calls `execute($observer)` instead of the configured `<method>`.

## Common high-traffic events

The events you'll actually want to subscribe to. All are stable public API per the BC rules in `AGENTS.md`.

**Generic model lifecycle** (fires for every `Mage_Core_Model_Abstract` subclass):
- `model_load_before`, `model_load_after`
- `model_save_before`, `model_save_after`, `model_save_commit_after`
- `model_delete_before`, `model_delete_after`, `model_delete_commit_after`
- Plus per-entity variants via `$_eventPrefix`: `sales_order_save_before`/`_after`, `catalog_product_save_after`, `customer_save_after`, `sales_order_delete_after`, etc. Grep the model class for `_eventPrefix` to confirm.

**Controller / request lifecycle:**
- `controller_action_predispatch` (every controller, every action — heavy traffic; keep observers cheap)
- `controller_action_predispatch_<full_action_name>` (e.g. `controller_action_predispatch_checkout_onepage_index`)
- `controller_action_postdispatch`
- `controller_action_layout_render_before`
- `controller_front_init_before`, `controller_front_init_routers`

**Customer:**
- `customer_login`, `customer_logout`
- `customer_register_success`
- `customer_save_after`, `customer_address_save_after`

**Cart / checkout:**
- `checkout_cart_product_add_after`
- `checkout_cart_save_before`, `checkout_cart_save_after`
- `checkout_cart_update_items_before`, `checkout_cart_update_items_after`
- `checkout_onepage_controller_success_action`
- `checkout_submit_all_after` (multi-shipping and onepage; canonical "order placed" hook)
- `checkout_quote_destroy`

**Sales / order:**
- `sales_order_place_before`, `sales_order_place_after`
- `sales_order_save_before`, `sales_order_save_after`
- `sales_order_invoice_save_after`, `sales_order_shipment_save_after`, `sales_order_creditmemo_save_after`
- `sales_order_payment_capture`, `sales_order_payment_refund`, `sales_order_payment_void`
- `order_cancel_after`

**Catalog:**
- `catalog_product_save_after`, `catalog_product_delete_before`, `catalog_product_delete_after`
- `catalog_product_is_salable_after` (set `$observer->getSalable()->setIsSalable(false)` to override)
- `catalog_category_save_after`

**Admin / config:**
- `admin_user_authenticate_before`, `admin_user_authenticate_after`
- `admin_session_user_login_success`, `admin_session_user_login_failed`
- `admin_system_config_section_save_after`
- `adminhtml_controller_action_predispatch_start`

**Block rendering** (use sparingly — fires per block):
- `core_block_abstract_to_html_before`, `core_block_abstract_to_html_after`
- `adminhtml_block_html_before`

When you need a custom event, prefix it with the module short name (`mymodule_thing_happened`) and treat the name as public API once shipped — see BC rules in `AGENTS.md`.

## When to use observer vs. config rewrite vs. editing core

This repo *is* upstream OpenMage. The decision tree differs from a typical merchant store:

1. **Bug in `Mage_*` / `Varien_*` code** → fix it in place in `app/code/core/Mage/...`. Do not paper over with an observer or rewrite. See `AGENTS.md` "Project" / BC sections.
2. **New behavior cleanly attached to a lifecycle moment** (e.g. "send a webhook on order place") → observer on the relevant existing event.
3. **Need to change the result of a method, not just react to it** → config rewrite (`<global><models><sales><rewrite>`) for a downstream module, or — when fixing a core defect — edit the method directly (per #1).
4. **Need a new hook point that doesn't exist** → first grep `dispatchEvent(` exhaustively. If it really doesn't exist, add the dispatch call in core (this repo's prerogative) and document the new event name as public API.

Do not introduce new observers in `app/code/local/` or `app/code/community/` for behavior that belongs in core — that's an integrator pattern, not an upstream fix.

## Quick reference: adding an observer

1. Pick the right event (grep core; usually one already exists).
2. Decide the area: `<global>` unless there's a reason for `<frontend>` / `<adminhtml>` / `<crontab>`.
3. Add to your module's `etc/config.xml`:
   ```xml
   <global>
     <events>
       <sales_order_place_after>
         <observers>
           <vendor_module_notify>
             <class>vendor_module/observer</class>
             <method>onOrderPlaced</method>
           </vendor_module_notify>
         </observers>
       </sales_order_place_after>
     </events>
   </global>
   ```
4. Implement `Vendor_Module_Model_Observer::onOrderPlaced(Varien_Event_Observer $observer)`. Pull data via `$observer->getEvent()->getOrder()` (matching the keys passed to `dispatchEvent`).
5. Clear the config cache (`var/cache/`, or admin → Cache Management → Configuration). Observer wiring is config-cached.
6. If the observer must run on the storefront only, swap `<global>` for `<frontend>`. Same for `<adminhtml>` / `<crontab>`.
