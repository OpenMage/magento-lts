<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wislist model collection
 *
 * @package    Mage_Wishlist
 *
 * @method Mage_Wishlist_Model_Wishlist[] getItems()
 */
class Mage_Wishlist_Model_Resource_Wishlist_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }

    /**
     * Filter collection by customer
     *
     * @return $this
     */
    public function filterByCustomer(Mage_Customer_Model_Customer $customer)
    {
        return $this->filterByCustomerId($customer->getId());
    }

    /**
     * Filter collection by customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function filterByCustomerId($customerId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        return $this;
    }

    /**
     * Filter collection by customer ids
     *
     * @return $this
     */
    public function filterByCustomerIds(array $customerIds)
    {
        $this->addFieldToFilter('customer_id', ['in' => $customerIds]);
        return $this;
    }
}
