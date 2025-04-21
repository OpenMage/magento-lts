<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote address shipping rate resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Rate extends Mage_Sales_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_address_shipping_rate', 'rate_id');
    }
}
