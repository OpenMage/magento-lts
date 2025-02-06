<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Flat sales order creditmemo item resource
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Creditmemo_Item extends Mage_Sales_Model_Resource_Order_Abstract
{
    /** @var string */
    protected $_eventPrefix    = 'sales_order_creditmemo_item_resource';

    protected function _construct()
    {
        $this->_init('sales/creditmemo_item', 'entity_id');
    }
}
