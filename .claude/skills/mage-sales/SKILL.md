---
name: mage-sales
description: Magento 1 Mage_Sales — quote/order/invoice/shipment/creditmemo lifecycle, order states vs statuses, totals collectors, address conversion. Use when editing under app/code/core/Mage/Sales/, working with sales/* aliases (sales/quote, sales/order, sales/order_invoice), modifying totals collection, or debugging order state transitions.
---

# Mage_Sales

Sales spine: quote (cart) → order → invoice / shipment / creditmemo. Aliases under `sales/*`. Source: `app/code/core/Mage/Sales/`.

## Quote → order conversion

Entry point: `Mage_Sales_Model_Service_Quote::submitAll()` → `submitOrder()`.

`submitAll()` first runs `submitNominalItems()` (recurring profiles), then `submitOrder()` if any normal items remain.

`submitOrder()` does, in order:

1. `_deleteNominalItems()` and `_validate()` (shipping address valid + method+rate set, billing valid, payment method set).
2. `$quote->reserveOrderId()`.
3. Build the order with `Mage_Sales_Model_Convert_Quote`:
   - `addressToOrder($billing|$shipping)` — copies totals/customer fields from the primary address onto the order.
   - `addressToOrderAddress(...)` — billing always; shipping only when not virtual.
   - `paymentToOrderPayment($quote->getPayment())`.
   - `itemToOrderItem($quoteItem)` per item; parent items wired via `getItemByQuoteItemId`.
4. Wraps everything in `Mage::getModel('core/resource_transaction')` with commit callbacks `[$order, 'place']` and `[$order, 'save']`.
5. Dispatches (in order): `checkout_type_onepage_save_order`, `sales_model_service_quote_submit_before`, then `sales_model_service_quote_submit_success` + `sales_model_service_quote_submit_after` on success, or `sales_model_service_quote_submit_failure` on exception (which also nulls `order_id` on items so a retry doesn't violate FKs).
6. Inactivates the quote (`setIsActive(false)`).

Don't bypass `Mage_Sales_Model_Service_Quote` — observers and the place/save commit-callback ordering are public API. To inject extra fields, use `setOrderData([...])` before `submitAll()`, or hook `sales_model_service_quote_submit_before`.

`Mage_Sales_Model_Convert_Quote` (alias `sales/convert_quote`) is the rewrite seam: subclass + `<rewrite>` to extend conversion. Methods: `toOrder`, `addressToOrder`, `addressToOrderAddress`, `paymentToOrderPayment`, `itemToOrderItem`.

## Order states vs statuses

States = closed enum on `Mage_Sales_Model_Order`. Statuses = user-configurable labels assigned to a state (table `sales_order_status` + `sales_order_status_state`).

State constants (`Mage_Sales_Model_Order`):

```php
public const STATE_NEW             = 'new';
public const STATE_PENDING_PAYMENT = 'pending_payment';
public const STATE_PROCESSING      = 'processing';
public const STATE_COMPLETE        = 'complete';
public const STATE_CLOSED          = 'closed';
public const STATE_CANCELED        = 'canceled';
public const STATE_HOLDED          = 'holded';
public const STATE_PAYMENT_REVIEW  = 'payment_review';
```

Default state→status map ships in `app/code/core/Mage/Sales/etc/config.xml` under `<global><sales><order><states>` (each state lists its allowed statuses with one `default="1"`). New shop-defined statuses live in DB, not config; the `<global><sales><order><statuses>` block is documented as *deprecated since 1.4.2* — don't add new statuses there.

State transition rules (`Mage_Sales_Model_Order::_setState`):

- `setState($state, $status, $comment, $isCustomerNotified)` is the public entrypoint and **rejects `complete` and `closed` directly** (`isStateProtected()`); those are driven by invoice/creditmemo registration. Use `_setState(...)` internally only.
- `$status === true` resolves to `getConfig()->getStateDefaultStatus($state)` (`Mage_Sales_Model_Order_Config`).
- `_setState` always appends to the status history (`addStatusHistoryComment`) when `$status` is truthy.
- Don't write `state` directly with `setData('state', ...)` from observers — go through `setState()` so history + `state_default_status` resolution fire.

`Mage_Sales_Model_Order_Status::assignState($state, $isDefault)` is how setup scripts attach a custom status to a state.

`_eventPrefix = 'sales_order'` → events fire as `sales_order_save_before`, `sales_order_save_after`, `sales_order_place_before`, `sales_order_place_after`, `sales_order_state_change_before`, etc. Observe these instead of patching the model.

## Invoice / shipment / creditmemo lifecycle

All three extend `Mage_Sales_Model_Abstract`, share an order, and have their own `register()` that mutates the parent order's totals/state.

| Model | `_eventPrefix` | States | Drives |
|---|---|---|---|
| `Mage_Sales_Model_Order_Invoice` | `sales_order_invoice` | `STATE_OPEN=1`, `STATE_PAID=2`, `STATE_CANCELED=3` | `pay()` / `capture()` move order toward `processing`/`complete`. |
| `Mage_Sales_Model_Order_Shipment` | `sales_order_shipment` | (no STATE_* constants — tracked via qty-shipped on order items) | Generates tracks, deducts `qty_shipped` on order items. |
| `Mage_Sales_Model_Order_Creditmemo` | `sales_order_creditmemo` | `STATE_OPEN=1`, `STATE_REFUNDED=2`, `STATE_CANCELED=3` | `refund()` returns funds via payment method, may move order to `closed`. |

**Who creates whom.** None of invoice/shipment/creditmemo creates the others automatically. The order creates each one via the `Mage_Sales_Model_Service_Order` service or directly:

- Invoice: `$order->prepareInvoice($qtys)` → `$invoice->register()` → `$invoice->capture()` (if online) or `$invoice->pay()` (if offline). Capturing on a payment that's already authorized triggers settlement.
- Shipment: `$order->prepareShipment($qtys)` → `$shipment->register()`. A shipment can only ship items that have been invoiced (or where the payment method allows un-invoiced shipping).
- Creditmemo: requires an existing invoice (online refund) — `$invoice->prepareCreditmemo($data)` → `$creditmemo->register()` → `$creditmemo->refund()`.

The order's `complete` / `closed` transition happens inside `Mage_Sales_Model_Order::_checkState()`, called from these registrations:

- All items invoiced + all items shipped (or virtual) → `STATE_COMPLETE`.
- All paid amount refunded (creditmemo total = invoiced total) → `STATE_CLOSED`.

That's why direct `setState(STATE_COMPLETE)` is blocked.

## Totals collectors

Quote totals are **collectors** (run on `$quote->collectTotals()` per address) registered in `etc/config.xml`. Order/invoice/creditmemo totals are **renderers** for display only; the source-of-truth numbers are computed at quote time and copied during conversion.

Quote total declaration shape (`<global><sales><quote><totals>`):

```xml
<sales>
  <quote>
    <totals>
      <subtotal>
        <class>sales/quote_address_total_subtotal</class>
        <after>nominal</after>
        <before>grand_total</before>
      </subtotal>
      <shipping>
        <class>sales/quote_address_total_shipping</class>
        <after>subtotal,freeshipping,tax_subtotal,msrp</after>
        <before>grand_total</before>
      </shipping>
      <grand_total>
        <class>sales/quote_address_total_grand</class>
        <after>subtotal</after>
      </grand_total>
    </totals>
  </quote>
</sales>
```

**Ordering.** Quote totals use `<before>` / `<after>` (CSV lists of other total codes); the registry topologically sorts on first access. Invoice/creditmemo totals use the same pattern. PDF totals (`<global><pdf><totals>`) use a numeric `<sort_order>` instead.

Other registration points:

- `<global><sales><order_invoice><totals>` — `sales/order_invoice_total_*` (subtotal, discount, shipping, tax, grand_total, cost_total).
- `<global><sales><order_creditmemo><totals>` — `sales/order_creditmemo_total_*`.
- `<global><sales><quote><nominal_totals>` — for recurring-profile lines.

A collector class extends `Mage_Sales_Model_Quote_Address_Total_Abstract` and implements `collect(Mage_Sales_Model_Quote_Address $address)` (must `parent::collect($address)` first to reset accumulators) and `fetch(...)` (for display rows). Collectors run **per address** — multishipping has multiple shipping addresses, so a collector that totals across the whole quote needs to read from `$address->getQuote()`.

Tax (`Mage_Tax`) and weee (`Mage_Weee`) inject themselves into this chain — see `mage-tax`. Catalog rules and sales rules inject `discount` / `freeshipping` collectors — see `mage-promotions`.

## Address conversion

Quote has `billing_address` + `shipping_address` rows in `sales_flat_quote_address` (or one address for virtual quotes). `Mage_Sales_Model_Convert_Quote::addressToOrderAddress` copies them into `sales_flat_order_address` rows tagged `address_type = billing|shipping`.

The order's *primary* totals (subtotal, grand_total, shipping_amount) come from `addressToOrder($shipping|$billing)` — for non-virtual orders this is the shipping address, for virtual it's the billing address. After conversion the quote address rows are still alive (until quote pruning), but mutating them post-submit doesn't affect the order.

Editing an order address post-submit: `Mage_Sales_Model_Order::setBillingAddress` / `setShippingAddress` rewrite by `address_type`, preserving the prior `entity_id`.

## Payment / shipping info storage

Per-order payment is one row in `sales_flat_order_payment`. Per-order shipping info lives on the order itself (`shipping_method`, `shipping_description`).

`additional_information` is a JSON-serialized blob (PHP-serialized via `Mage_Core_Model_Resource_Db_Abstract` serialization) on both `sales_flat_quote_payment.additional_information` and `sales_flat_order_payment.additional_information`. Access via `Mage_Payment_Model_Info`:

```php
$payment->setAdditionalInformation('cc_avs_status', 'Y');
$payment->getAdditionalInformation();           // full array
$payment->getAdditionalInformation('cc_avs_status');  // single key
$payment->unsAdditionalInformation('cc_avs_status');
```

Rules:

- **No objects** — `setAdditionalInformation` throws if `is_object($value)`.
- **Never store CVV / full PAN / sensitive auth data** (PCI). Use last-4 + auth ref only.
- Quote payment `additional_information` is copied by `Convert\Quote::paymentToOrderPayment` — set it during the checkout step you care about; it lands on the order.
- Transactions (`sales_flat_order_payment_transaction`) have their own `additional_information` for raw gateway responses (`Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS`).

## Item pipeline

```
quote_item (sales_flat_quote_item)
   │  via Convert_Quote::itemToOrderItem
   ▼
order_item (sales_flat_order_item)
   │  parent_item_id wired from quote parent
   ├──────────────► invoice_item    (qty_invoiced)
   ├──────────────► shipment_item   (qty_shipped)
   └──────────────► creditmemo_item (qty_refunded)
```

- `quote_item` has `parent_item_id` (composite product children); `Service\Quote::submitOrder` re-resolves this on the order side via `$order->getItemByQuoteItemId(...)`.
- `order_item` carries the cumulative counters: `qty_ordered`, `qty_invoiced`, `qty_shipped`, `qty_refunded`, `qty_canceled`, `qty_backordered`. The "to" methods (`getQtyToInvoice`, `getQtyToShip`, `getQtyToRefund`, `getQtyToCancel`) drive the admin UI.
- Invoice/shipment/creditmemo items reference back via `order_item_id`. Their `register()` increments the order_item counters and saves.
- Composite products: only the parent line is "visible" (`isVisible`); children carry the SKU/qty math. Always iterate `getAllItems()` for math, `getAllVisibleItems()` for display.

## Common pitfalls

- Calling `setState(STATE_COMPLETE)` directly throws — that's `isStateProtected()`. Let `_checkState()` close the order via invoice/shipment/creditmemo registration.
- Adding a totals collector without `<after>grand_total</after>` predecessors silently runs before grand_total and is overwritten.
- Per-address collectors: a "fee" you want once per order needs to write only on the primary address (shipping for physical, billing for virtual) — otherwise it's doubled for multishipping.
- Editing core conversion: rewrite `sales/convert_quote` rather than `sales/service_quote`; the service is thin and observer-rich.
- After a `submit*` failure, item IDs are nulled; don't reuse the order object — re-build from the quote.

## Cross-refs

- `mage-checkout` — onepage / multishipping flow that *calls* `Service\Quote::submitAll`.
- `mage-payment-methods` — `Method_Abstract` `authorize`/`capture`/`void`/`refund` hooks invoked from invoice/creditmemo lifecycle.
- `mage-shipping-carriers` — rate result produces the `shipping_method` + `shipping_amount` consumed by `addressToOrder`.
- `mage-tax` — tax/subtotal/shipping_tax collectors slot into the quote totals chain.
- `mage-promotions` — `salesrule/quote_discount` collector and coupon application.
- `m1-events-observers` — `sales_order_*`, `sales_order_invoice_*`, `sales_order_shipment_*`, `sales_order_creditmemo_*` event names; observe rather than rewrite.
- `m1-db-setup-scripts` — adding a status: `Mage_Sales_Model_Order_Status::assignState` from a `data-upgrade-*.php`.
