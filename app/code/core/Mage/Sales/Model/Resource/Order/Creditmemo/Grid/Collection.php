<?php
/**
 * Flat sales order creditmemo grid collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Creditmemo_Grid_Collection extends Mage_Sales_Model_Resource_Order_Creditmemo_Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_grid_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_grid_collection';

    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales/creditmemo_grid');
    }
}
