<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
 * @category   Mage
 * @package    Mage_Shipping
 *
 * @method Mage_Sales_Model_Quote_Item[] getAllItems()
 * @method $this setAllItems(array $items)
 *
 * @method Mage_Directory_Model_Currency getBaseCurrency()
 * @method $this setBaseCurrency(Mage_Directory_Model_Currency $value)
 * @method float getBaseSubtotalInclTax()
 * @method $this setBaseSubtotalInclTax(float $value)
 *
 * @method $this setCity(string $value)
 * @method string|array getConditionName()
 * @method $this setConditionName(string|array $value)
 * @method $this setCountryId(string $value)
 *
 * @method string getDestCountryId()
 * @method $this setDestCountryId(string $value)
 * @method int getDestRegionId()
 * @method $this setDestRegionId(int $value)
 * @method string getDestRegionCode()
 * @method $this setDestRegionCode(string $value)
 * @method string getDestPostcode()
 * @method $this setDestPostcode(string $value)
 * @method string getDestCity()
 * @method $this setDestCity(string $value)
 * @method string getDestStreet()
 * @method $this setDestStreet(string $value)
 *
 * @method bool getFreeShipping()
 * @method $this setFreeShipping(bool $flag)
 * @method float getFreeMethodWeight()
 * @method $this setFreeMethodWeight(float $value)
 *
 * @method string getLimitCarrier()
 * @method $this setLimitCarrier(string $value)
 * @method string getLimitMethod()
 * @method $this setLimitMethod(string $value)
 *
 * @method bool getOptionInsurance()
 * @method $this setOptionInsurance(bool $value)
 * @method float getOptionHandling()
 * @method $this setOptionHandling(float $flag)
 * @method float getOrderTotalQty()
 * @method $this setOrderTotalQty(float $value)
 * @method float getOrderSubtotal()
 * @method $this setOrderSubtotal(float $value)
 * @method string getOrigCountryId()
 * @method $this setOrigCountryId(string $value)
 * @method int getOrigRegionId()
 * @method $this setOrigRegionId(int $value)
 * @method string getOrigPostcode()
 * @method $this setOrigPostcode(string $value)
 * @method string getOrigCity()
 * @method $this setOrigCity(string $value)
 *
 * @method float getPackageValue()
 * @method $this setPackageValue(float $value)
 * @method float getPackageValueWithDiscount()
 * @method $this setPackageValueWithDiscount(float $value)
 * @method float getPackagePhysicalValue()
 * @method $this setPackagePhysicalValue(float $value)
 * @method float getPackageQty()
 * @method $this setPackageQty(float $value)
 * @method float getPackageWeight()
 * @method $this setPackageWeight(float $value)
 * @method int getPackageHeight()
 * @method $this setPackageHeight(int $value)
 * @method int getPackageWidth()
 * @method $this setPackageWidth(int $value)
 * @method int getPackageDepth()
 * @method $this setPackageDepth(int $value)
 * @method Mage_Directory_Model_Currency getPackageCurrency()
 * @method $this setPackageCurrency(Mage_Directory_Model_Currency $value)
 * @method $this setPostcode(string $value)
 *
 * @method $this setRegionId(string $value)
 *
 * @method Mage_Core_Model_Store getStore()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 *
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 */
class Mage_Shipping_Model_Rate_Request extends Varien_Object
{
}
