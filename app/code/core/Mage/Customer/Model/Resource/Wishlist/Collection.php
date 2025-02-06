<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customers wishlist collection
 *
 * @category   Mage
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
