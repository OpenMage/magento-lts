<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote resource model
 *
 * @category   Mage
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
