<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Order Tax Item Collection
 *
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Sales_Order_Tax_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('tax/sales_order_tax_item');
    }
}
