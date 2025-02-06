<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order payment collection
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Order_Payment getItemById(int $value)
 * @method Mage_Sales_Model_Order_Payment[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Payment_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_payment_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_payment_collection';

    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    /**
     * Unserialize additional_information in each item
     *
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }
        return parent::_afterLoad();
    }
}
