<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customers collection
 *
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Address getItemById(int $value)
 * @method Mage_Customer_Model_Address[] getItems()
 */
class Mage_Customer_Model_Resource_Address_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/address');
    }

    /**
     * Set customer filter
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     */
    public function setCustomerFilter($customer)
    {
        if ($customer->getId()) {
            $this->addAttributeToFilter('parent_id', $customer->getId());
        } else {
            $this->addAttributeToFilter('parent_id', '-1');
        }
        return $this;
    }
}
