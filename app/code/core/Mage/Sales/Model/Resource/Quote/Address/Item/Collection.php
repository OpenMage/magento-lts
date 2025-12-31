<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote addresses collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote_address_item');
    }

    /**
     * Set parent items
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        /**
         * Assign parent items
         */
        foreach ($this as $item) {
            if ($item->getParentItemId()) {
                $item->setParentItem($this->getItemById($item->getParentItemId()));
            }
        }

        return $this;
    }

    /**
     * Set address filter
     *
     * @param  int   $addressId
     * @return $this
     */
    public function setAddressFilter($addressId)
    {
        if ($addressId) {
            $this->addFieldToFilter('quote_address_id', $addressId);
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }

        return $this;
    }
}
