<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order shipment collection
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Order_Shipment getItemById(int $value)
 * @method Mage_Sales_Model_Order_Shipment[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Shipment_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_shipment_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    protected function _construct()
    {
        $this->_init('sales/order_shipment');
    }

    /**
     * Used to emulate after load functionality for each item without loading them
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');

        return $this;
    }
}
