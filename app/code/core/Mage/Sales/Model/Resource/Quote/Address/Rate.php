<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote address shipping rate resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Rate extends Mage_Sales_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_address_shipping_rate', 'rate_id');
    }
}
