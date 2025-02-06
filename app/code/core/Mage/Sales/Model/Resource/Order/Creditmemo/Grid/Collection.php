<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order creditmemo grid collection
 *
 * @category   Mage
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
