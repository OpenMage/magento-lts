<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order creditmemo item resource
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Creditmemo_Item extends Mage_Sales_Model_Resource_Order_Abstract
{
    /** @var string */
    protected $_eventPrefix    = 'sales_order_creditmemo_item_resource';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/creditmemo_item', 'entity_id');
    }
}
