<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_SalesRule_Model_Quote_Freeshipping
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Quote_Freeshipping extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Discount calculation object
     *
     * @var Mage_SalesRule_Model_Validator
     */
    protected $_calculator;

    public function __construct()
    {
        $this->setCode('discount');
        $this->_calculator = Mage::getSingleton('salesrule/validator');
    }

    /**
     * Collect information about free shipping for all address items
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Freeshipping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());

        $address->setFreeShipping(0);
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }
        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());

        $isAllFree = true;
        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $isAllFree = false;
                $item->setFreeShipping(false);
            } else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }
                $this->_calculator->processFreeShipping($item);
                $isItemFree = (bool)$item->getFreeShipping();
                $isAllFree = $isAllFree && $isItemFree;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $this->_calculator->processFreeShipping($child);
                        /**
                         * Parent free shipping we apply to all children
                         */
                        if ($isItemFree) {
                            $child->setFreeShipping($isItemFree);
                        }
                    }
                }
            }
        }
        if ($isAllFree && !$address->getFreeShipping()) {
            $address->setFreeShipping(true);
        }
        return $this;
    }

    /**
     * Add information about free shipping for all address items to address object
     * By default we not present such information
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Freeshipping
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
