<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Item option resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Item_Option extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote_item_option', 'option_id');
    }
}
