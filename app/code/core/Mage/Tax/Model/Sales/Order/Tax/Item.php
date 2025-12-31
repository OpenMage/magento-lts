<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax_Item            _getResource()
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax_Item_Collection getCollection()
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax_Item            getResource()
 */
class Mage_Tax_Model_Sales_Order_Tax_Item extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tax/sales_order_tax_item');
    }
}
