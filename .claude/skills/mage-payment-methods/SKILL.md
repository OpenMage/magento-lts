---
name: mage-payment-methods
description: Magento 1 payment methods — Mage_Payment_Model_Method_Abstract hooks (authorize/capture/void/refund/cancel/acceptPayment/denyPayment), system.xml registration, additional_information serialization, 3DS Centinel, IPN endpoints. Use when editing Mage_Payment/Paygate/Authorizenet/Paypal/PaypalUk/Centinel, adding a payment method, or implementing IPN handlers.
---

# Magento 1 — Payment Methods

Covers `Mage_Payment`, `Mage_Paygate`, `Mage_Authorizenet`, `Mage_Paypal`, `Mage_PaypalUk`, `Mage_Centinel`. Aliases owned: `payment/*`, `paygate/*`, `authorizenet/*`, `paypal/*`, `paypaluk/*`, `centinel/*`.

A payment method is a `Mage_Payment_Model_Method_Abstract` subclass referenced by alias from `default/payment/<code>/model` in `etc/config.xml`. Capabilities advertise via `protected $_can*` flags; the matching action methods (`authorize`, `capture`, ...) on the abstract throw "X action is not available" when the flag is false, so an override **must** flip the flag *and* implement the action — half-doing it is the classic bug.

## Capability flags → method matrix

Set on the subclass; checked by the abstract before dispatching.

| Flag | Action method | Notes |
|---|---|---|
| `_isGateway` | — | True for any method that calls a remote gateway. |
| `_canOrder` | `order(Varien_Object $payment, $amount)` | Pre-authorize "order" intent (rare; PayPal Express). |
| `_canAuthorize` | `authorize(Varien_Object $payment, $amount)` | Hold funds; set `last_trans_id`, transaction additional_information. |
| `_canCapture` | `capture(Varien_Object $payment, $amount)` | If invoked with an existing auth transaction, settle it; else auth+capture. |
| `_canCapturePartial` | (same `capture`) | Allow partial-amount invoices. |
| `_canCaptureOnce` | — | Disallow further captures after first. |
| `_canRefund` | `refund(Varien_Object $payment, $amount)` | Called from creditmemo create. |
| `_canRefundInvoicePartial` | (same `refund`) | Allow partial refunds per invoice. |
| `_canVoid` | `void(Varien_Object $payment)` | Reverse an unsettled auth. |
| `_canCancelInvoice` | (no separate hook) | TODO in core; today `cancel()` on order catches this. |
| `_canReviewPayment` | `acceptPayment` / `denyPayment(Mage_Payment_Model_Info $payment)` | Fraud-review hand-off (PayPal pending-review). Both must return `bool`. |
| `_canFetchTransactionInfo` | `fetchTransactionInfo($payment, $transactionId)` | Pull live state from gateway for the admin "Fetch" button. |
| `_canUseInternal` | — | Available in admin "Create Order". |
| `_canUseCheckout` | — | Available in frontend onepage. |
| `_canUseForMultishipping` | — | Multi-address checkout. |
| `_canCreateBillingAgreement` | `Mage_Payment_Model_Billing_Agreement_MethodInterface` | Implement the interface, don't just flip the flag. |
| `_canManageRecurringProfiles` | `Mage_Payment_Model_Recurring_Profile_MethodInterface` | Same — interface, not just flag. |
| `_isInitializeNeeded` | `initialize($paymentAction, $stateObject)` | Replaces authorize/capture for redirect-style methods (PayPal Standard). Mutates `$stateObject->setState/setStatus/setIsNotified`. |

`cancel(Varien_Object $payment)` is unconditional (no flag); default returns `$this`. Override to release auth on order cancel.

`Varien_Object $payment` is in practice `Mage_Sales_Model_Order_Payment`. Mutate by setting transaction ids and additional_information; the caller persists.

## Concrete examples

- **Gateway, full set:** `app/code/core/Mage/Paygate/Model/Authorizenet.php` — `_isGateway=true`, all of authorize/capture/void/refund. Note `_isGatewayActionsLockedKey` guards re-entrancy.
- **Redirect, initialize-only:** `app/code/core/Mage/Paypal/Model/Standard.php` — `_isInitializeNeeded=true`, `_canUseInternal=false`, `_canUseForMultishipping=false`. Skips authorize/capture entirely.
- **Offline:** `Mage_Payment_Model_Method_Checkmo`, `_Free`, `_Banktransfer`, `_Cashondelivery`, `_Purchaseorder` — all under `Mage/Payment/Model/Method/`. Most rely only on `assignData` + an info block.

## Payment info storage

Payment data lives on three rows: `sales_flat_quote_payment`, `sales_flat_order_payment`, plus per-transaction `sales_payment_transaction`. All three carry:

- `additional_data` (TEXT) — serialized form post (deprecated for new code; legacy gateways still use it).
- `additional_information` (TEXT) — JSON-ish array; the modern store. Use `setAdditionalInformation('key', $value)` / `getAdditionalInformation('key')` / `unsAdditionalInformation('key')` on `Mage_Payment_Model_Info`. Whole-array set with `setAdditionalInformation($array)`.

**Never store CVV.** `Mage_Payment_Model_Info::setCcCid()` exists but `cc_cid` is decrypted on demand from `cc_cid_enc` and is intentionally not persisted across the order lifecycle. PCI: do not write `cc_cid` to `additional_information`, do not log it (`_debugReplacePrivateDataKeys` masks it). PAN must go through the encryption helper (`cc_number_enc`) and ideally be tokenized at the gateway.

## `system.xml` payment-group field shape

Payment methods register under `<sections><payment><groups><your_method>`. Field shape (every method has these; gateways add gateway URL/login/key fields on top):

```xml
<your_method translate="label" module="your_module">
    <label>Your Method</label>
    <frontend_type>text</frontend_type>
    <sort_order>50</sort_order>
    <show_in_default>1</show_in_default>
    <show_in_website>1</show_in_website>
    <show_in_store>0</show_in_store>
    <fields>
        <active translate="label"><label>Enabled</label><frontend_type>select</frontend_type><source_model>adminhtml/system_config_source_yesno</source_model><sort_order>1</sort_order>...</active>
        <title translate="label"><label>Title</label><frontend_type>text</frontend_type><sort_order>2</sort_order>...</title>
        <order_status translate="label"><label>New Order Status</label><frontend_type>select</frontend_type><source_model>adminhtml/system_config_source_order_status_new</source_model>...</order_status>
        <payment_action translate="label"><label>Payment Action</label><frontend_type>select</frontend_type><source_model>your_module/source_paymentAction</source_model>...</payment_action>
        <allowspecific translate="label"><label>Payment from Applicable Countries</label><frontend_type>allowspecific</frontend_type><source_model>adminhtml/system_config_source_payment_allspecificcountries</source_model>...</allowspecific>
        <specificcountry translate="label"><label>Payment from Specific Countries</label><frontend_type>multiselect</frontend_type><source_model>adminhtml/system_config_source_country</source_model><can_be_empty>1</can_be_empty>...</specificcountry>
        <min_order_total><label>Minimum Order Total</label><frontend_type>text</frontend_type>...</min_order_total>
        <max_order_total><label>Maximum Order Total</label><frontend_type>text</frontend_type>...</max_order_total>
        <sort_order translate="label"><label>Sort Order</label><frontend_type>text</frontend_type>...</sort_order>
    </fields>
</your_method>
```

Defaults go in `etc/config.xml` under `<default><payment><your_method>`, including `<model>your_module/method_yourmethod</model>` (the alias resolved for the method instance) and `<group>offline</group>` (or `paypal`, `authorizenet`, etc.). `getConfigData($field)` reads `payment/<code>/<field>` for the current store.

`<allowspecific>=1` activates `<specificcountry>` filtering via `Mage_Payment_Model_Method_Abstract::canUseForCountry()`. `<min_order_total>`/`<max_order_total>` are honored by `isApplicableToQuote(... CHECK_ORDER_TOTAL_MIN_MAX)`. Encrypted credentials use `<backend_model>adminhtml/system_config_backend_encrypted</backend_model>`.

## Block split: Form vs Info

```php
protected $_formBlockType = 'your_module/form_yourmethod';   // checkout / admin order create
protected $_infoBlockType = 'your_module/info_yourmethod';   // admin order view, customer email, PDF
```

- `Block/Form/*` — extends `Mage_Payment_Block_Form` → `payment/form/*.phtml`. Renders the credit card / data entry UI in onepage and admin "Create Order". Posts back through `Mage_Checkout` → `assignData()`.
- `Block/Info/*` — extends `Mage_Payment_Block_Info` → `payment/info/*.phtml` and `app/design/adminhtml/.../template/payment/info/*.phtml`. Read-only summary used in: admin order view, sales emails, PDFs, customer "My Orders". Both an HTML and a `toPdf()` path; check both render correctly.

`assignData($data)` on the method copies submitted form fields onto the info instance — sanitize/validate here. Put non-card fields you need later into `additional_information`, not raw columns. `validate()` runs server-side and should `Mage::throwException()` on bad input.

## 3D Secure (Centinel) hand-off

`Mage_Centinel_Model_Service` is wired in via observers (`centinel/observer`) on `payment_method_is_active` and friends. A 3DS-aware method:

1. Mixes in `Mage_Centinel_Model_MethodInterface` and exposes `getCentinelValidator()`.
2. After card data is collected, `Service::lookup()` calls the gateway → if `shouldAuthenticate()`, redirects the buyer (iframe) to `centinel/index/authenticate` (front) or `*/centinel_index/authenticate` (admin).
3. Buyer returns; `authenticate()` stores CMPI fields (`CMPI_PARES`, `CMPI_ENROLLED`, `CMPI_CAVV`, `CMPI_ECI`, `CMPI_XID`) into the validation-state model.
4. On order place, `validate()` checks the state and `exportCmpiData()` injects CMPI fields onto the `Mage_Payment_Model_Info` so authorize/capture can forward them to the gateway.

CVV never enters Centinel storage; checksum-keyed state lives in `centinel/session`. Failures throw — let them; Mage catches and surfaces the error.

## Webhook / IPN pattern

PayPal IPN is the canonical example. Same shape applies to any async gateway notification.

- **Separate frontend route.** Module declares its own `<routers>` (`paypal` → `frontName=paypal`); the controller (`Mage_Paypal_IpnController::indexAction`) extends `Mage_Core_Controller_Front_Action`. URL: `/paypal/ipn/`.
- **No form-key validation.** IPNs are server-to-server POSTs from the gateway — there is no browser session, no form key. The controller does **not** call `_validateFormKey()`. It rejects non-POST, then hands the body to a model. (Guard against CSRF by verifying the gateway signature, see next point.)
- **Signature/postback verification in the model**, not the controller. `Mage_Paypal_Model_Ipn::_postBack()` re-POSTs the payload to PayPal with `cmd=_notify-validate` and aborts unless PayPal answers `VERIFIED`. `_verifyOrder()` then cross-checks merchant email, currency, and amount against the order before mutating state. **Never trust the request body alone.**
- **Idempotency.** IPNs duplicate. Key on `txn_id` + `payment_status`; check `sales_payment_transaction` before adding a new transaction or invoice.
- **Response codes.** 200 on accept (even for ignored duplicates), 5xx for retryable errors. PayPal will retry on non-2xx for hours.
- **Logging.** Per-method log file via `Mage_Core_Model_Log_Adapter` with `_debugReplacePrivateDataKeys` populated to mask PAN/CVV.

## Adding a new method — checklist

1. `Model/Method/Foo.php` extends `Mage_Payment_Model_Method_Abstract`; set `$_code`, `$_formBlockType`, `$_infoBlockType`, capability flags.
2. Implement only the action methods matching the flags you flipped.
3. `etc/config.xml` → `<global><models>` alias, `<default><payment><foo>` with `<model>`, `<active>0</active>`, `<title>`, `<order_status>`, `<allowspecific>`, `<sort_order>`, `<group>`.
4. `etc/system.xml` → field block per the shape above. ACL node in `etc/adminhtml.xml` mirroring the system-config path.
5. `Block/Form/Foo.php` + `app/design/frontend/base/default/template/payment/form/foo.phtml`.
6. `Block/Info/Foo.php` + frontend and `adminhtml` templates under `template/payment/info/foo.phtml`.
7. Translations in `app/locale/en_US/Mage_YourModule.csv`.
8. If async: register `<routers>`, write a front controller, verify signatures in the model, write an idempotent processor.
9. If 3DS: implement `Mage_Centinel_Model_MethodInterface`, wire `getCentinelValidator()`.

## Pitfalls

- Flipping `_canRefund=true` without implementing `refund()` → "Refund action is not available" at creditmemo time. The default `refund()` on the abstract throws when the flag is *false*, but a subclass `refund()` that doesn't call the gateway just silently succeeds — both directions bite.
- `cancel()` has no capability flag and the default is a no-op. Gateways must override to release the authorization, otherwise the merchant pays an auth-hold fee.
- Storing card data in `additional_information` "for later" — these columns are TEXT and are not encrypted at rest. Use `cc_number_enc` (encrypted) and never persist `cc_cid` at all.
- IPN controller adding `_validateFormKey()` "for safety" — breaks the gateway integration. Authenticate via signature/postback in the model.
- `_isInitializeNeeded` methods (PayPal Standard) that also implement `authorize()`/`capture()` — those hooks are bypassed; `initialize()` runs instead. Don't waste time on the unused branches.
- Forgetting the admin info `.phtml` — the order ships, the customer email renders fine, then admin order view fatals because only the frontend template exists.
- `getConfigData('payment_action')` returns null when the field isn't in `system.xml` — provide a `<source_model>` and a sensible default in `config.xml`.

## Cross-refs

- `mage-sales` — order/quote payment lifecycle, `sales_payment_transaction`, totals.
- `m1-system-config` — section/group/field shape, backend models for encryption, scope rules.
- `m1-controllers-routing` — frontend router config for IPN endpoints, `preDispatch`/`postDispatch`.
- `m1-acl-adminhtml` — admin ACL nodes for new payment-config sections.
- `m1-events-observers` — `payment_method_is_active` (toggling availability), Centinel observers.
