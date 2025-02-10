<?php
/**
 * Customers wishlist collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Wishlist_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Set entity
     */
    protected function _construct()
    {
        $this->setEntity(Mage::getResourceSingleton('customer/wishlist'));
    }
}
