<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Order Tax Collection
 *
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Sales_Order_Tax[] getItems()
 */
class Mage_Tax_Model_Resource_Sales_Order_Tax_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tax/sales_order_tax');
    }

    /**
     * Retrieve order tax collection by order identifier
     *
     * @param  Varien_Object $order
     * @return $this
     */
    public function loadByOrder($order)
    {
        $orderId = $order->getId();
        $this->getSelect()
            ->where('main_table.order_id = ?', (int) $orderId)
            ->order('process');
        return $this->load();
    }
}
