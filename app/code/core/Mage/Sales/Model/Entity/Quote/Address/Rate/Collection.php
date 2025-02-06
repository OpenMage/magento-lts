<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote addresses collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Address_Rate_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_address_rate');
    }

    /**
     * @param int $addressId
     * @return $this
     */
    public function setAddressFilter($addressId)
    {
        $this->addAttributeToFilter('parent_id', $addressId);
        return $this;
    }
}
