---
name: mage-module-checkout
description: OpenMage Mage_Checkout — onepage and multishipping flow, quote session, login-time quote merge, agreements, persistent-cart hand-off. Use when editing under app/code/core/Mage/Checkout/, working with checkout/* aliases (checkout/cart, checkout/onepage, checkout/session), debugging checkout step actions, or modifying the quote → order conversion path.
---

# Mage_Checkout

Quote-driven checkout. Two top-level flows: **onepage** (default) and **multishipping**. Both end in `Mage_Sales_Model_Service_Quote::submitAll()`. The shopping cart (`checkout/cart`) is a thin wrapper around the same active quote.

## Aliases owned

- `checkout/session` — `Mage_Checkout_Model_Session` (singleton; per-website quote ID)
- `checkout/cart` — `Mage_Checkout_Model_Cart` (cart actions on the current quote)
- `checkout/onepage` — `Mage_Checkout_Model_Type_Onepage`
- `checkout/type_multishipping` — `Mage_Checkout_Model_Type_Multishipping`
- `checkout/agreement` — terms-and-conditions records
- `checkout/cart_*`, `checkout/onepage_*` — block aliases for cart and onepage layout

Frontend route: `/checkout/onepage/*`, `/checkout/cart/*`, `/checkout/multishipping/*`.

## The `getCheckout()` helper pattern

Multishipping blocks (`Block/Multishipping/Abstract.php` + subclasses) and the Onepage model expose `getCheckout()` returning the singleton `checkout/session`; cart blocks call `Mage::getSingleton('checkout/session')` directly. Don't `Mage::getSingleton('checkout/session')` repeatedly — call `$this->getCheckout()` from controllers, blocks (`Mage_Checkout_Block_*`), and the onepage model. It's the same instance everywhere. Step state (`getStepData`/`setStepData`) and transient flags (`setRedirectUrl`, `setGotoSection`, `setLastOrderId`, `setLastRealOrderId`) live on it.

## Onepage step sequence

JS posts each step to `Mage_Checkout_OnepageController`; the controller delegates to `Mage_Checkout_Model_Type_Onepage` and JSON-encodes the result via `_prepareDataJSON` (`Mage::helper('core')->jsonEncode($response)`).

| # | Step             | Controller action          | Onepage method                              | Step flag set                          |
|---|------------------|----------------------------|---------------------------------------------|----------------------------------------|
| 0 | login/method     | `saveMethodAction`         | `saveCheckoutMethod($method)`               | `billing.allow=true`                   |
| 1 | billing          | `saveBillingAction`        | `saveBilling($data, $customerAddressId)`    | `billing.complete`, `shipping.allow`   |
| 2 | shipping         | `saveShippingAction`       | `saveShipping($data, $customerAddressId)`   | `shipping.complete`, `shipping_method.allow` |
| 3 | shipping method  | `saveShippingMethodAction` | `saveShippingMethod($shippingMethod)`       | `shipping_method.complete`, `payment.allow` |
| 4 | payment          | `savePaymentAction`        | `savePayment($data)`                        | `payment.complete`, `review.allow`     |
| 5 | review / place   | `saveOrderAction`          | `saveOrder()`                               | redirects to success / 3rd-party       |

Step state lives in `checkout/session::setStepData($step, 'allow'|'complete', bool)`. The JS-side accordion checks these flags before unlocking a step. Skip a step by faking `complete=true` only if you know what you're doing.

Each `save*` method returns:
- `[]` (empty array) on success, or
- `['error' => 1|−1, 'message' => ...]` on validation failure.

`savePaymentAction` additionally returns `goto_section` + `update_section.html` (a re-rendered HTML fragment) so the JS can re-render the review block. `_expireAjax()` short-circuits when the session has expired.

## `saveOrder()` — quote → order

`Mage_Checkout_Model_Type_Onepage::saveOrder()`:

1. `validate()` — guards against multishipping mix-up and disallowed guest checkout.
2. Branches on `getCheckoutMethod()`:
   - `METHOD_GUEST` → `_prepareGuestQuote()` (sets `customer_is_guest`, NOT_LOGGED_IN_ID group).
   - `METHOD_REGISTER` → `_prepareNewCustomerQuote()` (creates a `customer/customer` from billing/shipping; password from `quote->getPasswordHash()`).
   - default → `_prepareCustomerQuote()` (logged-in customer; saves new addresses if `save_in_address_book`).
3. **Hands off to sales**: `Mage::getModel('sales/service_quote', $this->getQuote())->submitAll()`. This is the single conversion entry point — see `mage-module-sales`.
4. `submitAll` creates the order, dispatches `sales_model_service_quote_submit_before`/`_success`, and inactivates the quote.
5. New customers: `_involveNewCustomer()` either sends confirmation email or `loginById()`.
6. Stashes `last_quote_id` and `last_success_quote_id`, then dispatches `checkout_type_onepage_save_order_after` (legacy event).
7. Stashes `last_order_id`, `redirect_url` (third-party gateway), and `last_real_order_id`, then dispatches `checkout_submit_all_after` (canonical, also dispatched by multishipping).

The success page reads `last_success_quote_id`, `last_quote_id`, and `last_order_id` and redirects to cart if any are missing.

## `Mage_Checkout_Model_Session`

`init('checkout')` namespace. Quote ID is **scoped per website**: stored as `quote_id_{websiteId}` (`_getQuoteIdKey`) so customers switching websites don't cross-contaminate.

`getQuote()` lifecycle:
1. Dispatches `custom_quote_process` (the `Mage_Persistent` hook into quote lookup).
2. If `_quote` cached, returns it.
3. Loads by stored quote ID via `loadActive` (or `load` when `_loadInactive` is set, e.g. admin reviewing a cart).
4. If no quote ID and customer is logged in, `loadByCustomer($customer)` and store the ID.
5. If currency drifted (currency switcher), reloads after `collectTotals()->save()`.
6. Sets `setStore()` and remote IP (`x_forwarded_for`).

Don't call `getQuote()` and then mutate without `save()` — totals collection is required after most mutations.

### Quote merging on login

`customer_login` observer: `checkout/observer::loadCustomerQuote` (delegates to `checkout/session::loadCustomerQuote()`).

```
guest quote (current session) + customer's persisted quote
  → customerQuote->merge(guestQuote)->collectTotals()->save()
  → setQuoteId(customerQuote.id)
  → guestQuote->delete()
```

Wired in `Mage/Checkout/etc/config.xml` under `<frontend><events><customer_login>`. If you observe `customer_login` and read the cart, run **after** `loadCustomerQuote` (sort_order) or you'll see the pre-merge guest quote.

## `Mage_Checkout_Model_Cart`

Thin façade over the active quote. Use it from controllers instead of poking the quote directly.

- `getQuote()` — same as `checkout/session::getQuote()` but caches on the cart instance.
- `addProduct($productInfo, $requestInfo)` — accepts a product ID, model, or buy-request array; runs through the type instance's `prepareForCart`.
- `addProductsByIds([...])`, `addOrderItem($orderItem, $qtyFlag = null)` (reorder).
- `updateItems($data)` — `[itemId => ['qty' => N]]`; dispatches `checkout_cart_update_items_before/after`.
- `updateItem($itemId, $requestInfo, $updatingParams)` — single-item edit (configurable options).
- `removeItem($itemId)`, `truncate()`.
- `save()` — `collectTotals` then `saveQuote()`; dispatches `checkout_cart_save_before/after`.

Events worth knowing: `checkout_cart_product_add_after`, `checkout_cart_update_item_complete`, `checkout_cart_save_after`, plus `checkout_quote_init` and `custom_quote_process`.

## Multishipping

`Mage_Checkout_Model_Type_Multishipping`. Activated by `quote->setIsMultiShipping(true)` and the `/checkout/multishipping/*` controllers. Onepage's `initCheckout()` explicitly resets the flag — the two flows are mutually exclusive.

- `setShippingItemsInformation([[$quoteItemId => ['qty' => N, 'address' => $addressId]], ...])` splits each quote item across N quote addresses (one per shipping destination). Each address gets its own subset of items.
- `setShippingMethods([addressId => methodCode, ...])` — one method per address.
- `setPaymentMethod($payment)` — single payment for all sub-orders.

`createOrders()`:
1. `_validate()` — every shipping address must validate and have a rate.
2. Iterate `getQuote()->getAllShippingAddresses()`; for each, `_prepareOrder($address)` builds a fresh `sales/order` from that address's items via `sales/convert_quote`.
3. If the quote has virtual items, the billing address joins the loop as another order.
4. Dispatches `checkout_type_multishipping_create_orders_single` per order.
5. After all are prepared, loops and `$order->place()->save()` each (saves are intentionally per-order — one bad gateway call rolls only that order). On exception during the place loop, the `catch` block dispatches `checkout_multishipping_refund_all` then rethrows.
6. Inactivates the quote, stashes `orderIds` in `core/session`, dispatches `checkout_submit_all_after` with the **list** of orders (onepage dispatches with a single `order`).

Result: N rows in `sales_flat_order`, one quote, N independent shipments. Promotions/discounts that depend on cart-level totals can behave surprisingly here — each sub-order is rebuilt independently.

## Agreements (terms & conditions)

`checkout/agreement` entity. `Mage::helper('checkout')->getRequiredAgreementIds()` returns IDs gated by `checkout/options/enable_agreements`. `saveOrderAction` rejects the post if the agreement IDs in `request.agreement[]` don't cover the required set. Layout block `checkout.onepage.agreements` injects them into the review step. Backend grid lives in `Mage_Adminhtml/Block/Checkout/Agreement` and `Mage_Adminhtml/controllers/Checkout/AgreementController`.

## Persistent cart hand-off (`Mage_Persistent`)

Independent module that hooks into checkout via observers (`Mage/Persistent/etc/config.xml`):

- `controller_action_predispatch` → `persistent/observer::emulateQuote` and `emulateCustomer` — on every page load, if a persistent cookie is present and the user is a guest, the quote/customer is "emulated" (loaded from `persistent_session` table, not the PHP session).
- `custom_quote_process` (dispatched inside `checkout/session::getQuote()`) → `persistent/observer::setLoadPersistentQuote` flips `_loadInactive=true` so an inactive persisted quote can still be loaded.
- `customer_login` / `customer_logout` → `persistent/observer_session::synchronizePersistentOnLogin/Logout`.
- `checkout_allow_guest` → `persistent/observer::disableGuestCheckout` — persistent users are forced to register/log in (configurable).
- `sales_quote_save_before` → tags the quote with persistent metadata so it can be re-found.

Config: `persistent/options/enabled`, `lifetime`, `remember_default`, `shopping_cart`, `logout_clear`. Cookie name `persistent_shopping_cart`. Cron `persistent_clear_expired` runs daily.

If checkout is misbehaving for users with the "remember me" cookie, suspect this module first — the quote you see in the session may be the persistent one, not the one you just modified.

## Common modification points

- **Add a step**: layout XML adds an accordion section + child block; JS `Checkout` class needs a section entry; controller gets a `saveFooAction`; `Onepage::saveFoo` mutates the quote and toggles step flags. Don't forget `_isAjax`/`_expireAjax`/form-key.
- **Inject data into review**: observe `checkout_controller_onepage_save_shipping_method` or extend `Mage_Checkout_Block_Onepage_Review_Info`.
- **Block guest checkout conditionally**: observe `checkout_allow_guest` and `setIsAllowed(false)` on the transport.
- **Custom quote validation pre-place-order**: observe `sales_model_service_quote_submit_before` (in `mage-module-sales`) — `Mage_Core_Exception` aborts the order with a clean error.
- **Side-effect after order place**: observe `checkout_submit_all_after` (fires for both flows). `checkout_type_onepage_save_order_after` is onepage-only and legacy.

## Pitfalls

- `getCheckout()->setStepData(...)` does **not** persist the underlying steps array unless the session is later written. The session writes on shutdown — fine for normal HTTP, surprising in CLI/tests.
- `Onepage::initCheckout()` resets `is_multi_shipping`, customer balance, and reward points. Hitting `/checkout/onepage` mid-multishipping wipes the multishipping setup.
- `loadActive` filters `is_active=1`. A quote inactivated by `submitAll()` will silently fail to load — set `setLoadInactive(true)` to debug.
- The success page redirect uses `last_success_quote_id`; `last_quote_id` survives a failed payment retry. Don't conflate the two.
- `Onepage::saveBilling` with `use_for_shipping=1` *clones* billing into shipping but skips fields the customer already touched in shipping — driven by a per-key null check, not by form state.
- The `customer_password` field is captured into `quote->password_hash` (encrypted) during billing and decrypted in `_prepareNewCustomerQuote`. The quote table actually stores hashed passwords briefly.

## Cross-refs

- `mage-module-sales` — `Mage_Sales_Model_Service_Quote::submitAll()`, totals collectors, order/invoice/shipment lifecycle.
- `mage-module-customer` — login flow, `customer/session`, address book, `customer_address` EAV.
- `openmage-events-observers` — for the persistent observer wall and `customer_login`/`checkout_submit_all_after` consumers.
- `openmage-controllers-routing` — frontend router config, `_validateFormKey`, AJAX dispatch.
- `mage-module-payment-methods` — `savePayment` and order-place redirect for offsite gateways (Paypal Standard, etc.).
- `mage-module-promotions` — coupons (`_setCartCouponCode`) and totals interaction.
