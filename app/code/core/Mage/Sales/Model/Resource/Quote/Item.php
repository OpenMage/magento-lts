<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Item extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * Main table and field initialization
     */
    protected function _construct()
    {
        $this->_init('sales/quote_item', 'item_id');
    }
}
