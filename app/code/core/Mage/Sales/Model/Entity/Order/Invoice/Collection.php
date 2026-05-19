<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Invoices collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Invoice_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_invoice');
    }

    /**
     * @param  Mage_Sales_Model_Order $order
     * @return $this
     */
    public function setOrderFilter($order)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            $this->addAttributeToFilter('order_id', $order->getId());
        } else {
            $this->addAttributeToFilter('order_id', $order);
        }

        return $this;
    }
}
