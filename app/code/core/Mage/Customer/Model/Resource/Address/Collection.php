<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customers collection
 *
 * @category   Mage
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
