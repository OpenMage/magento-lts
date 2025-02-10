<?php
/**
 * Quote addresses collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Address_Item_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_address_item');
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
