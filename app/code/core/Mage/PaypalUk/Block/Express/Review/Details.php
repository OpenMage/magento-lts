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
 * @category    Mage
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PaypalUk Express Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PaypalUk_Block_Express_Review_Details extends Mage_Checkout_Block_Cart_Totals
{
    protected $_address;

    /**
     * Get PayPal Express Review Information
     *
     * @return Mage_Paypal_Model_Express_Review
     */
    /**
     * Get PayPalUk Express Review Information
     *
     * @return Mage_PaypalUk_Model_Express_Review
     */
    public function getReview()
    {
        return Mage::getSingleton('paypaluk/express_review');
    }

    /**
     * Return review shipping address
     *
     * @return Mage_Sales_Model_Order_address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getReview()->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * Return review quote items
     *
     * @return Mage_Sales_Model_Mysql4_Order_Item_Collection
     */
    public function getItems()
    {
        return $this->getReview()->getQuote()->getAllItems();
    }

    /**
     * Return review quote totals
     * @return array
     */
    public function getTotals()
    {
        return $this->getReview()->getQuote()->getTotals();
    }
}
