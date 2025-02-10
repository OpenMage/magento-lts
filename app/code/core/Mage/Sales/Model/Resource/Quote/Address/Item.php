<?php
/**
 * Quote address item resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Item extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * Main table and field initialization
     */
    protected function _construct()
    {
        $this->_init('sales/quote_address_item', 'address_item_id');
    }
}
