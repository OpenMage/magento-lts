<?php
/**
 * Quote address shipping rate resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Rate extends Mage_Sales_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_address_shipping_rate', 'rate_id');
    }
}
