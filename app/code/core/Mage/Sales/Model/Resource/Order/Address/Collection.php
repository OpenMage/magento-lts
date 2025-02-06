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
 * @method Mage_Sales_Model_Order_Address getItemById(int $value)
 * @method Mage_Sales_Model_Order_Address[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Address_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_address_collection';

    protected function _construct()
    {
        $this->_init('sales/order_address');
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        Mage::dispatchEvent($this->_eventPrefix . '_load_after', [
            $this->_eventObject => $this,
        ]);

        return $this;
    }
}
