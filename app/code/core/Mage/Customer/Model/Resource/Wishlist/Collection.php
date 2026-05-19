<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customers wishlist collection
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Wishlist_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setEntity(Mage::getResourceSingleton('customer/wishlist'));
    }
}
