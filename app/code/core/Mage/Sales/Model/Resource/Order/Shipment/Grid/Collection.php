<?php
/**
 * Flat sales order shipment collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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

    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales/shipment_grid');
    }
}
