<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/**
 * Fields:
 * - orig:
 *   - country_id: UK
 *   - region_id: 1
 *   - postcode: 90034
 * - dest:
 *   - country_id: UK
 *   - region_id: 2
 *   - postcode: 01005
 * - package:
 *   - value: $100
 *   - weight: 1.5 lb
 *   - height: 10"
 *   - width: 10"
 *   - depth: 10"
 * - order:
 *   - total_qty: 10
 *   - subtotal: $100
 * - option
 *   - insurance: true
 *   - handling: $1
 * - table (shiptable)
 *   - condition_name: package_weight
 * - limit
 *   - carrier: ups
 *   - method: 3dp
 * - ups
 *   - pickup: CC
 *   - container: CP
 *   - address: RES
 *
 * @package    Mage_Shipping
 *
 * @method Mage_Sales_Model_Quote_Item[] getAllItems()
 * @method Mage_Directory_Model_Currency getBaseCurrency()
 * @method float                         getBaseSubtotalInclTax()
 * @method array|string                  getConditionName()
 * @method string                        getDestCity()
 * @method string                        getDestCountryId()
 * @method string                        getDestPostcode()
 * @method string                        getDestRegionCode()
 * @method int                           getDestRegionId()
 * @method string                        getDestStreet()
 * @method float                         getFreeMethodWeight()
 * @method bool                          getFreeShipping()
 * @method string                        getLimitCarrier()
 * @method string                        getLimitMethod()
 * @method float                         getOptionHandling()
 * @method bool                          getOptionInsurance()
 * @method float                         getOrderSubtotal()
 * @method float                         getOrderTotalQty()
 * @method string                        getOrigCity()
 * @method string                        getOrigCountryId()
 * @method string                        getOrigPostcode()
 * @method int                           getOrigRegionId()
 * @method Mage_Directory_Model_Currency getPackageCurrency()
 * @method int                           getPackageDepth()
 * @method int                           getPackageHeight()
 * @method float                         getPackagePhysicalValue()
 * @method float                         getPackageQty()
 * @method float                         getPackageValue()
 * @method float                         getPackageValueWithDiscount()
 * @method float                         getPackageWeight()
 * @method int                           getPackageWidth()
 * @method Mage_Core_Model_Store         getStore()
 * @method int                           getStoreId()
 * @method int                           getWebsiteId()
 * @method $this                         setAllItems(array $items)
 * @method $this                         setBaseCurrency(Mage_Directory_Model_Currency $value)
 * @method $this                         setBaseSubtotalInclTax(float $value)
 * @method $this                         setCity(string $value)
 * @method $this                         setConditionName(array|string $value)
 * @method $this                         setCountryId(string $value)
 * @method $this                         setDestCity(string $value)
 * @method $this                         setDestCountryId(string $value)
 * @method $this                         setDestPostcode(string $value)
 * @method $this                         setDestRegionCode(string $value)
 * @method $this                         setDestRegionId(int $value)
 * @method $this                         setDestStreet(string $value)
 * @method $this                         setFreeMethodWeight(float $value)
 * @method $this                         setFreeShipping(bool $flag)
 * @method $this                         setLimitCarrier(string $value)
 * @method $this                         setLimitMethod(string $value)
 * @method $this                         setOptionHandling(float $flag)
 * @method $this                         setOptionInsurance(bool $value)
 * @method $this                         setOrderSubtotal(float $value)
 * @method $this                         setOrderTotalQty(float $value)
 * @method $this                         setOrigCity(string $value)
 * @method $this                         setOrigCountryId(string $value)
 * @method $this                         setOrigPostcode(string $value)
 * @method $this                         setOrigRegionId(int $value)
 * @method $this                         setPackageCurrency(Mage_Directory_Model_Currency $value)
 * @method $this                         setPackageDepth(int $value)
 * @method $this                         setPackageHeight(int $value)
 * @method $this                         setPackagePhysicalValue(float $value)
 * @method $this                         setPackageQty(float $value)
 * @method $this                         setPackageValue(float $value)
 * @method $this                         setPackageValueWithDiscount(float $value)
 * @method $this                         setPackageWeight(float $value)
 *
 * @method $this setPackageWidth(int $value)
 * @method $this setPostcode(string $value)
 * @method $this setRegionId(string $value)
 * @method $this setStoreId(int $value)
 * @method $this setWebsiteId(int $value)
 */
class Mage_Shipping_Model_Rate_Request extends Varien_Object {}
