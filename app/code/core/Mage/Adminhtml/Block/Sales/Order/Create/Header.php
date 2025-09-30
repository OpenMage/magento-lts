<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Create order form header
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Header extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected function _toHtml()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            return '<h3 class="icon-head head-sales-order">' . Mage::helper('sales')->__(
                'Edit Order #%s',
                $this->escapeHtml($this->_getSession()->getOrder()->getIncrementId()),
            ) . '</h3>';
        }

        $customerId = $this->getCustomerId();
        $storeId    = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $out .= Mage::helper('sales')->__('Create New Order for %s in %s - %s', $this->getCustomer()->getName(), $this->getStore()->getWebsite()->getName(), $this->getStore()->getName());
        } elseif (!is_null($customerId) && $storeId) {
            $out .= Mage::helper('sales')->__('Create New Order for New Customer in %s - %s', $this->getStore()->getWebsite()->getName(), $this->getStore()->getName());
        } elseif ($customerId) {
            $out .= Mage::helper('sales')->__('Create New Order for %s', $this->getCustomer()->getName());
        } elseif (!is_null($customerId)) {
            $out .= Mage::helper('sales')->__('Create New Order for New Customer');
        } else {
            $out .= Mage::helper('sales')->__('Create New Order');
        }
        $out = $this->escapeHtml($out);
        return '<h3 class="icon-head head-sales-order">' . $out . '</h3>';
    }
}
