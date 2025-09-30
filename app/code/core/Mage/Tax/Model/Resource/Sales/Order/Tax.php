<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Sales order tax resource model
 *
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Sales_Order_Tax extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/sales_order_tax', 'tax_id');
    }
}
