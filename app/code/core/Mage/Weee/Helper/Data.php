<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WEEE data helper
 */
class Mage_Weee_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_FPT_ENABLED  = 'tax/weee/enable';

    protected $_storeDisplayConfig = array();

    public function getPriceDisplayType($store = null)
    {
        if (!is_null($store)) {
            if ($store instanceof Mage_Core_Model_Store) {
                $key = $store->getId();
            } else {
                $key = $store;
            }
        } else {
            $key = 'current';
        }

        if (!isset($this->_storeDisplayConfig[$key])) {
            $value = Mage::getStoreConfig('tax/weee/display', $store);
            $this->_storeDisplayConfig[$key] = $value;
        }

        return $this->_storeDisplayConfig[$key];
    }

    public function getListPriceDisplayType($store = null)
    {
        if (!is_null($store)) {
            if ($store instanceof Mage_Core_Model_Store) {
                $key = $store->getId();
            } else {
                $key = $store;
            }
        } else {
            $key = 'current';
        }

        if (!isset($this->_storeDisplayConfig[$key])) {
            $value = Mage::getStoreConfig('tax/weee/display_list', $store);
            $this->_storeDisplayConfig[$key] = $value;
        }

        return $this->_storeDisplayConfig[$key];
    }

    public function getSalesPriceDisplayType($store = null)
    {
        if (!is_null($store)) {
            if ($store instanceof Mage_Core_Model_Store) {
                $key = $store->getId();
            } else {
                $key = $store;
            }
        } else {
            $key = 'current';
        }

        if (!isset($this->_storeDisplayConfig[$key])) {
            $value = Mage::getStoreConfig('tax/weee/display_sales', $store);
            $this->_storeDisplayConfig[$key] = $value;
        }

        return $this->_storeDisplayConfig[$key];
    }

    public function getEmailPriceDisplayType($store = null)
    {
        if (!is_null($store)) {
            if ($store instanceof Mage_Core_Model_Store) {
                $key = $store->getId();
            } else {
                $key = $store;
            }
        } else {
            $key = 'current';
        }

        if (!isset($this->_storeDisplayConfig[$key])) {
            $value = Mage::getStoreConfig('tax/weee/display_email', $store);
            $this->_storeDisplayConfig[$key] = $value;
        }

        return $this->_storeDisplayConfig[$key];
    }

    public function getAmount($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false)
    {
        if ($this->isEnabled()) {
            return Mage::getSingleton('weee/tax')->getWeeeAmount($product, $shipping, $billing, $website, $calculateTaxes);
        }
        return 0;
    }

    public function typeOfDisplay($product, $compareTo = null, $zone = null, $store = null)
    {
        $type = 0;
        switch ($zone) {
            case 'product_view':
            $type = $this->getPriceDisplayType($store);
            break;

            case 'product_list':
            $type = $this->getListPriceDisplayType($store);
            break;

            case 'sales':
            $type = $this->getSalesPriceDisplayType($store);
            break;

            case 'email':
            $type = $this->getEmailPriceDisplayType($store);
            break;

            default:
            if (Mage::registry('current_product')) {
                $type = $this->getPriceDisplayType($store);
            } else {
                $type = $this->getListPriceDisplayType($store);
            }
            break;
        }

        if (is_null($compareTo)) {
            return $type;
        } else {
            if (is_array($compareTo)) {
                return in_array($type, $compareTo);
            } else {
                return $type == $compareTo;
            }
        }
    }

    public function getProductWeeeAttributes($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false)
    {
        return Mage::getSingleton('weee/tax')->getProductWeeeAttributes($product, $shipping, $billing, $website, $calculateTaxes);
    }

    public function getApplied($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item_Abstract) {
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $result = array();
                foreach ($item->getChildren() as $child) {
                    $childData = $this->getApplied($child);
                    if (is_array($childData)) {
                        $result = array_merge($result, $childData);
                    }
                }
                return $result;
            }
        }

        /**
         * if order item data is old enough then weee_tax_applied cab be
         * not valid serialized data
         */
        $data = $item->getWeeeTaxApplied();
        if (empty($data)){
            return array();
        }
        return unserialize($item->getWeeeTaxApplied());
    }

    public function setApplied($item, $value)
    {
        $item->setWeeeTaxApplied(serialize($value));
        return $this;
    }

    public function isDiscounted($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/discount', $store);
    }

    public function isTaxable($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/apply_vat', $store);
    }

    public function includeInSubtotal($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/include_in_subtotal', $store);
    }

    public function getProductWeeeAttributesForDisplay($product)
    {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes($product, null, null, null, $this->typeOfDisplay($product, 1));
        }
        return array();
    }


    public function getAmountForDisplay($product)
    {
        if ($this->isEnabled()) {
            return Mage::getModel('weee/tax')->getWeeeAmount($product, null, null, null, $this->typeOfDisplay($product, 1));
        }
        return 0;
    }

    public function getOriginalAmount($product)
    {
        if ($this->isEnabled()) {
            return Mage::getModel('weee/tax')->getWeeeAmount($product, null, null, null, false, true);
        }
        return 0;
    }

    public function processTierPrices($product, &$tierPrices)
    {
        $weeeAmount = $this->getAmountForDisplay($product);
        foreach ($tierPrices as &$tier) {
            $tier['formated_price_incl_weee'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $tier['website_price'], true)+$weeeAmount));
            $tier['formated_price_incl_weee_only'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $tier['website_price'])+$weeeAmount));
            $tier['formated_weee'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice($weeeAmount));
        }
        return $this;
    }

    /**
     * Check if fixed taxes are used in system
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FPT_ENABLED, $store);
    }
}
