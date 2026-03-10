<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order shipment collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection extends Mage_Sales_Model_Resource_Order_Shipment_Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_grid_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_shipment_grid_collection';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales/shipment_grid');
    }
}
