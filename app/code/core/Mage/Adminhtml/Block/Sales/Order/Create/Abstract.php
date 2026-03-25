<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create abstract block
 *
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Block_Sales_Order_Create_Abstract extends Mage_Adminhtml_Block_Widget
{
    /**
     * Retrieve create order model object
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function getCreateOrderModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Retrieve quote session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve customer model object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Retrieve customer identifier
     *
     * @return null|int
     */
    public function getCustomerId()
    {
        return $this->_getSession()->getCustomerId();
    }

    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
    }

    /**
     * Retrieve store identifier
     *
     * @return null|int
     */
    public function getStoreId()
    {
        return $this->_getSession()->getStoreId();
    }

    /**
     * Retrieve formatted price
     *
     * @param  float  $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->getStore()->formatPrice($value);
    }

    public function convertPrice($value, $format = true)
    {
        return $this->getStore()->convertPrice($value, $format);
    }
}
