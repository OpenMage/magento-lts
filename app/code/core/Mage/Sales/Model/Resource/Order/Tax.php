<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order Tax Model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Tax extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_tax', 'tax_id');
    }
}
