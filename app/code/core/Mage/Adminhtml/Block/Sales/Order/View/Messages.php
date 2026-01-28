<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Order view messages
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Messages extends Mage_Adminhtml_Block_Messages
{
    protected function _getOrder()
    {
        return Mage::registry('sales_order');
    }

    protected function _prepareLayout()
    {
        /**
         * Check customer existing
         */
        $customer = Mage::getModel('customer/customer')->load($this->_getOrder()->getCustomerId());

        /**
         * Check Item products existing
         */
        $productIds = [];
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return parent::_prepareLayout();
    }
}
