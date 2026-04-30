---
name: mage-module-shipping-carriers
description: OpenMage shipping carriers — Mage_Shipping_Model_Carrier_Abstract, rate request/result, USA carriers (USPS/UPS/FedEx/DHL), tracking, system.xml allowed countries/methods, free shipping interaction. Use when editing under app/code/core/Mage/Shipping/ or app/code/core/Mage/Usa/, adding a carrier, modifying rate calculation, or wiring tracking info.
---

# mage-module-shipping-carriers

Mage_Shipping owns the rate pipeline; Mage_Usa ships the four real-world carriers (USPS, UPS, FedEx, DHL) on top of `Mage_Usa_Model_Shipping_Carrier_Abstract`. Generic tablerate/flatrate/freeshipping/pickup live under `Mage_Shipping/Model/Carrier/`.

Aliases: `shipping/*` (rate result, rate request, tracking), `usa/*` (USA carriers + helpers).

## Carrier registration

Two-spot wiring per carrier — config alias plus default values:

```xml
<!-- etc/config.xml -->
<global>
  <sales>
    <shipping>
      <carriers>
        <mycarrier><class>Vendor_Module_Model_Carrier_Mycarrier</class></mycarrier>
      </carriers>
    </shipping>
  </sales>
</global>
<default>
  <carriers>
    <mycarrier>
      <active>0</active>
      <model>mycarrier/carrier_mycarrier</model>
      <title>My Carrier</title>
      <sallowspecific>0</sallowspecific>
    </mycarrier>
  </carriers>
</default>
```

`Mage_Shipping_Model_Shipping::collectRates()` iterates `Mage::getStoreConfig('carriers')` and calls each carrier's `collectRates()` — every active carrier code under `<carriers>` is tried unless `$request->getLimitCarrier()` narrows it.

## Carrier_Abstract — required surface

`Mage_Shipping_Model_Carrier_Abstract extends Varien_Object` and `Mage_Shipping_Model_Carrier_Interface` declares:

- `isTrackingAvailable(): bool` — default `false` on the abstract; `Mage_Usa_Model_Shipping_Carrier_Abstract` overrides to `true`. (Return types shown below are from docblock `@return`; the interface and abstract don't declare native return types for BC.)
- `getAllowedMethods(): array` — `['method_code' => 'Method Title', ...]`. Used in admin dropdowns and free-shipping method config.

You must implement:

- `abstract collectRates(Mage_Shipping_Model_Rate_Request $request): null|bool|Mage_Shipping_Model_Rate_Result` — return `false` to opt out, `Mage_Shipping_Model_Rate_Result_Error` to surface a message, or a populated `Rate_Result`.
- `getAllowedMethods(): array` (from interface).
- `_doShipmentRequest(Varien_Object $request): Varien_Object` — only on the Usa abstract (`abstract protected`); required when `isShippingLabelsAvailable()` returns true.

Property convention: set `protected $_code = 'mycarrier';` so `getConfigData('field')` resolves `carriers/mycarrier/field`.

## The rate pipeline

```
Mage_Shipping_Model_Shipping::collectCarrierRates(Rate_Request)
  └── for each carrier:
        Carrier_Abstract::checkAvailableShipCountries()   // sallowspecific/specificcountry/showmethod
        Carrier_Abstract::proccessAdditionalValidation()  // override for zip/state checks
        composePackagesForCarrier()                       // only if <shipment_requesttype> truthy
        Carrier::collectRates(Rate_Request) → Rate_Result // per-package; carrier-internal _updateFreeMethodQuote() re-quotes free_method at reduced weight
```

`Mage_Shipping_Model_Rate_Request` is a `Varien_Object` carrying `dest_*`, `orig_*`, `package_weight`, `package_qty`, `all_items` (`Mage_Sales_Model_Quote_Item[]`), `free_shipping`, `free_method_weight`, `limit_carrier`, `limit_method`. See its docblock for the full `@method` list.

`Rate_Result` is a flat container of `Mage_Shipping_Model_Rate_Result_Abstract` items — concretely `Rate_Result_Method` (per shipping option) and `Rate_Result_Error` (carrier-level error). Build it like:

```php
$result = Mage::getModel('shipping/rate_result');
$method = Mage::getModel('shipping/rate_result_method');
$method->setCarrier($this->_code);
$method->setCarrierTitle($this->getConfigData('title'));
$method->setMethod('ground');
$method->setMethodTitle('Ground');
$method->setPrice($this->getMethodPrice($cost, 'ground'));   // applies handling + free-shipping-enable
$method->setCost($cost);
$result->append($method);
return $result;
```

Errors:

```php
$error = Mage::getModel('shipping/rate_result_error');
$error->setCarrier($this->_code);
$error->setCarrierTitle($this->getConfigData('title'));
$error->setErrorMessage($this->getConfigData('specificerrmsg'));
return $error;
```

## Package splitting

`Mage_Shipping_Model_Shipping::composePackagesForCarrier($carrier, $request)` splits an order into multiple weight-bounded packages **only if** the carrier sets `<shipment_requesttype>` in config (UPS, FedEx, DHL expose it (default `0`); USPS doesn't). It reads `carriers/<code>/max_package_weight` and returns `[weight => packageCount]`. The shipping model then clones the carrier per package, calls `collectRates()`, and sums prices via `Rate_Result::updateRatePrice($packageCount)`. If any single item exceeds `max_package_weight`, `composePackagesForCarrier()` returns `[]` — the carrier won't quote.

For carriers that handle their own splitting, the abstract provides `getTotalNumOfBoxes($weight)` which divides by `max_package_weight` and stashes `$this->_numBoxes` — `getFinalPriceWithHandlingFee()` always multiplies cost by `_numBoxes`; `handling_action=PERPACKAGE` additionally multiplies the handling fee per package, while `PERORDER` adds the fee once.

## Free-shipping interaction

Two distinct mechanisms — don't conflate:

1. **Per-carrier free-method config** (`free_method`, `free_shipping_enable`, `free_shipping_subtotal` under `carriers/<code>/`). `Carrier_Abstract::getMethodPrice($cost, $method)` returns `'0.00'` when the method matches `free_method` and the cart subtotal exceeds the threshold. Always route prices through `getMethodPrice()` (not `getFinalPriceWithHandlingFee()`) if you want this to apply.
2. **SalesRule "Free Shipping"** action — sets `$request->getFreeShipping() === true` and zeros out per-item weights so `$request->getFreeMethodWeight()` differs from `$request->getPackageWeight()`. `Carrier_Abstract::_updateFreeMethodQuote()` then re-runs `_getQuotes()` at the reduced weight via `_setFreeMethodRequest($freeMethod)` and rewrites the `free_method` row's price.

When implementing a new carrier, override `_setFreeMethodRequest($freeMethod)` if your raw request needs more than just `free_method` swapped in — see USPS for the reference shape.

## system.xml carrier section

Every carrier under `<sections><carriers><groups>` must expose this minimum field set so generic admin behavior (country whitelist, sort order, error message) works:

```xml
<mycarrier translate="label">
  <label>My Carrier</label>
  <sort_order>30</sort_order>
  <show_in_default>1</show_in_default><show_in_website>1</show_in_website><show_in_store>1</show_in_store>
  <fields>
    <active translate="label">
      <label>Enabled</label><frontend_type>select</frontend_type>
      <source_model>adminhtml/system_config_source_yesno</source_model>
      <sort_order>1</sort_order>
      <show_in_default>1</show_in_default><show_in_website>1</show_in_website><show_in_store>0</show_in_store>
    </active>
    <title translate="label"><label>Title</label><sort_order>2</sort_order>...</title>
    <sallowspecific translate="label">
      <label>Ship to Applicable Countries</label><frontend_type>select</frontend_type>
      <source_model>adminhtml/system_config_source_shipping_allspecificcountries</source_model>
      <frontend_class>shipping-applicable-country</frontend_class>
      <sort_order>90</sort_order>...
    </sallowspecific>
    <specificcountry translate="label">
      <label>Ship to Specific Countries</label><frontend_type>multiselect</frontend_type>
      <source_model>adminhtml/system_config_source_country</source_model>
      <can_be_empty>1</can_be_empty>
      <sort_order>91</sort_order>...
    </specificcountry>
    <showmethod translate="label">
      <label>Show Method if Not Applicable</label><frontend_type>select</frontend_type>
      <source_model>adminhtml/system_config_source_yesno</source_model>
      <sort_order>92</sort_order>...
    </showmethod>
    <specificerrmsg translate="label">
      <label>Displayed Error Message</label><frontend_type>textarea</frontend_type>
      <sort_order>80</sort_order>...
    </specificerrmsg>
    <sort_order translate="label"><label>Sort Order</label><sort_order>100</sort_order>...</sort_order>
  </fields>
</mycarrier>
```

`sallowspecific=1` + empty `specificcountry` + `showmethod=0` is the default-deny shape — `checkAvailableShipCountries()` returns `false` and the carrier silently skips. `showmethod=1` flips that to a visible `Rate_Result_Error` carrying `specificerrmsg`.

## USA carriers

`Mage_Usa_Model_Shipping_Carrier_Abstract` adds: tracking on by default, `_getCachedQuotes()` / `_setCachedQuotes()` (per-request rate cache keyed on raw request), `getAllItems()` that flattens bundle children, and the `abstract _doShipmentRequest()` for label generation.

Carrier-specific quirks:

- **USPS** (`Mage_Usa_Model_Shipping_Carrier_Usps`): two API stacks — legacy XML (`Mage/Usa/.../Usps.php` direct calls) and REST (`Usps/Rest/`). Auth and endpoint flip on `<environment>` config (`production` vs `sandbox`). Containers (`VARIABLE`, `FLAT RATE BOX`, etc.) are class constants. `OUNCES_POUND = 16` for weight conversion.
- **UPS**: OAuth (`UpsAuth.php`) for the modern REST API; legacy XML auth still present. Pickup type / container / address (residential vs commercial) UPS reads pickup/container/dest_type from the rate request via `getUpsPickup`/`getUpsContainer`/`getUpsDestType` (set by callers; not declared in the `Rate_Request` `@method` list).
- **FedEx**: WSDL-based SOAP (`Usa/etc/wsdl/FedEx/`). Smartpost vs ground vs express are all separate methods, gated on account fields.
- **DHL**: split into US (`Dhl.php`) and international (`Dhl/International.php` registered as `<dhlint>`). DHL US is largely retired; treat international as primary.

UPS/FedEx/DHL expose `<shipment_requesttype>` (default `0`, admin-toggleable). USPS doesn't, and so never runs `composePackagesForCarrier()`.

## Tracking

```php
public function isTrackingAvailable() { return true; }   // already true on Usa abstract

public function getTrackingInfo($trackingNumber)
{
    $result = $this->getTracking($trackingNumber);   // populates $this->_trackingResult
    if ($result instanceof Mage_Shipping_Model_Tracking_Result) {
        $items = $result->getAllTrackings();
        return $items[0] ?? false;                    // Mage_Shipping_Model_Tracking_Result_Status
    }
    return is_string($result) && $result !== '' ? $result : false;
}
```

`getTracking()` is the per-carrier hook — implement it to build a `Tracking_Result` from carrier API output. The frontend tracking popup (`shipping/tracking/popup`) and admin shipment view both call `getTrackingInfo()`.

## Common pitfalls

- Returning `null` from `collectRates()` instead of `false` — the shipping model treats `null` as a hard error and aborts the carrier loop on multi-package requests.
- Forgetting `setCarrier()`/`setMethod()` on a `Rate_Result_Method` — `Rate_Result::getRatesByCarrier()` and the checkout shipping picker both filter on those.
- Using `getFinalPriceWithHandlingFee()` directly when you want free-shipping awareness — use `getMethodPrice($cost, $methodCode)` instead.
- Adding a method that won't appear in the SalesRule "free shipping" admin dropdown — ensure it's in `getAllowedMethods()`.
- Caching rates without including the destination postcode in the cache key — `_getQuotesCacheKey()` on the Usa abstract implodes request params with the carrier code and CRC32-hashes them; mirror that pattern.

## Cross-refs

- `mage-module-sales` — quote address `collectShippingRates()`, `Mage_Sales_Model_Quote_Address_Rate` persistence, conversion to `sales_order_shipment`.
- `mage-module-promotions` — SalesRule "Free Shipping" action sets `$request->setFreeShipping(true)` and stamps per-item `free_shipping` flags.
- `openmage-system-config` — backend/source/frontend models, scope behavior of the `carriers/*` config tree.
