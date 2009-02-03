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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product type price model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Price
{
    const CACHE_TAG = 'PRODUCT_PRICE';

    static $attributeCache = array();

    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product)
    {
        return $product->getData('price');
    }

    /**
     * Get product final price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $product->getPrice();
        $finalPrice = $this->_applyTierPrice($product, $qty, $finalPrice);
        $finalPrice = $this->_applySpecialPrice($product, $finalPrice);
        $product->setFinalPrice($finalPrice);

        Mage::dispatchEvent('catalog_product_get_final_price', array('product'=>$product));

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);

        return max(0, $finalPrice);
    }

    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty)
    {
        return $this->getFinalPrice($childProductQty, $childProduct);
    }

    /**
     * Apply tier price for product if not return price that was before
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   double $qty
     * @param   double $finalPrice
     * @return  double
     */
    protected function _applyTierPrice($product, $qty, $finalPrice)
    {
        if (is_null($qty)) {
            return $finalPrice;
        }

        $tierPrice  = $product->getTierPrice($qty);
        if (is_numeric($tierPrice)) {
            $finalPrice = min($finalPrice, $tierPrice);
        }
        return $finalPrice;
    }

    /**
     * Get product tier price by qty
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getTierPrice($qty=null, $product)
    {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('tier_price');

        if (is_null($prices)) {
            if ($attribute = $product->getResource()->getAttribute('tier_price')) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData('tier_price');
            }
        }

        if (is_null($prices) || !is_array($prices)) {
            if (!is_null($qty)) {
                return $product->getPrice();
            }
            return array(array(
                'price'         => $product->getPrice(),
                'website_price' => $product->getPrice(),
                'price_qty'     => 1,
                'cust_group'    => $allGroups,
            ));
        }

        $custGroup = $this->_getCustomerGroupId($product);
        if ($qty) {
            $prevQty = 1;
            $prevPrice = $product->getPrice();
            $prevGroup = $allGroups;

            foreach ($prices as $price) {
                if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    // tier not for current customer group nor is for all groups
                    continue;
                }
                if ($qty < $price['price_qty']) {
                    // tier is higher than product qty
                    continue;
                }
                if ($price['price_qty'] < $prevQty) {
                    // higher tier qty already found
                    continue;
                }
                if ($price['price_qty'] == $prevQty && $prevGroup != $allGroups && $price['cust_group'] == $allGroups) {
                    // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                    continue;
                }
                $prevPrice  = $price['website_price'];
                $prevQty    = $price['price_qty'];
                $prevGroup  = $price['cust_group'];
            }
            return $prevPrice;
        } else {
            foreach ($prices as $i=>$price) {
                if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    unset($prices[$i]);
                }
            }
        }

        return ($prices) ? $prices : array();
    }

    protected function _getCustomerGroupId($product)
    {
        if ($product->getCustomerGroupId()) {
            return $product->getCustomerGroupId();
        }
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * Apply special price for product if not return price that was before
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   double $finalPrice
     * @return  double
     */
    protected function _applySpecialPrice($product, $finalPrice)
    {
        $specialPrice = $product->getSpecialPrice();
        if (is_numeric($specialPrice)) {
            $storeDate  = Mage::app()->getLocale()->storeDate($product->getStore());
            $fromDate   = Mage::app()->getLocale()->date($product->getSpecialFromDate(), null, null, false);
            $toDate     = Mage::app()->getLocale()->date($product->getSpecialToDate(), null, null, false);

            if ($product->getSpecialFromDate() && $storeDate->compare($fromDate, Zend_Date::DATES)<0) {
            } elseif ($product->getSpecialToDate() && $storeDate->compare($toDate, Zend_Date::DATES)>0) {
            } else {
               $finalPrice = min($finalPrice, $specialPrice);
            }
        }
        return $finalPrice;
    }

    /**
     * Count how many tier prices we have for the product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  int
     */
    public function getTierPriceCount($product)
    {
        $price = $product->getTierPrice();
        return count($price);
    }

    /**
     * Get formated by currency tier price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  array || double
     */
    public function getFormatedTierPrice($qty=null, $product)
    {
        $price = $product->getTierPrice($qty);
        if (is_array($price)) {
            foreach ($price as $index => $value) {
                $price[$index]['formated_price'] = Mage::app()->getStore()->convertPrice($price[$index]['website_price'], true);
            }
        }
        else {
            $price = Mage::app()->getStore()->formatPrice($price);
        }

        return $price;
    }

    /**
     * Get formated by currency product price
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  array || double
     */
    public function getFormatedPrice($product)
    {
        return Mage::app()->getStore()->formatPrice($product->getFinalPrice());
    }

    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param double $finalPrice
     * @return double
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        if ($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $optionValue = $product->getCustomOption('option_'.$option->getId())->getValue();
                    if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                        || $option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                        foreach(split(',', $optionValue) as $value) {
                            $finalPrice += $this->_getPricingOptionValue(array(
                                'is_percent' => ($option->getValueById($value)->getPriceType() == 'percent')? true:false,
                                'pricing_value' => $option->getValueById($value)->getPrice()
                            ), $basePrice);
                        }
                    } elseif ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        $finalPrice += $this->_getPricingOptionValue(array(
                            'is_percent' => ($option->getValueById($optionValue)->getPriceType() == 'percent')? true:false,
                            'pricing_value' => $option->getValueById($optionValue)->getPrice()
                        ), $basePrice);
                    } else {
                        $finalPrice += $this->_getPricingOptionValue(array(
                            'is_percent' => ($option->getPriceType() == 'percent')? true:false,
                            'pricing_value' => $option->getPrice()
                        ), $basePrice);
                    }
                }
            }
        }

        return $finalPrice;
    }

    /**
     * Get product pricing option value
     *
     * @param array $value
     * @param double $basePrice
     * @return double
     */
    protected function _getPricingOptionValue($value, $basePrice)
    {
        if($value['is_percent']) {
            $ratio = $value['pricing_value']/100;
            $price = $basePrice * $ratio;
        } else {
            $price = $value['pricing_value'];
        }

        return $price;
    }

    public static function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $rulePrice = false, $wId = null, $gId = null, $productId = null)
    {
        if ($wId instanceof Mage_Core_Model_Store) {
            $sId = $wId->getId();
            $wId = $wId->getWebsiteId();
        } else {
            $sId = Mage::app()->getWebsite($wId)->getDefaultGroup()->getDefaultStoreId();
        }
        $storeDate = Mage::app()->getLocale()->storeDate($sId);

        if ($gId instanceof Mage_Customer_Model_Group) {
            $gId = $gId->getId();
        }
        $finalPrice = $basePrice;

        $fromDate   = Mage::app()->getLocale()->date($specialPriceFrom, null, null, false);
        $toDate     = Mage::app()->getLocale()->date($specialPriceTo, null, null, false);

        if ($specialPrice !== null && $specialPrice !== false) {
            if ($specialPriceFrom && $storeDate->compare($fromDate, Zend_Date::DATES)<0) {
            } elseif ($specialPriceTo && $storeDate->compare($toDate, Zend_Date::DATES)>0) {
            } else {
               $finalPrice = min($finalPrice, $specialPrice);
            }
        }

        if ($rulePrice === false) {
            $rulePrice = Mage::getResourceModel('catalogrule/rule')->getRulePrice($storeDate, $wId, $gId, $productId);
        }
        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min($finalPrice, $rulePrice);
        }

        $finalPrice = max($finalPrice, 0);

        return $finalPrice;
    }
}