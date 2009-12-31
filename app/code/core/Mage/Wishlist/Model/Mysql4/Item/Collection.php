<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist item collection
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize resource model for collection
     *
     */
    public function _construct()
    {
        $this->_init('wishlist/item');
    }

    /**
     * Add filter by wishlist object
     *
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addWishlistFilter(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        $this->addFieldToFilter('wishlist_id', $wishlist->getId());

        return $this;
    }

    /**
     * Add filter by shared stores
     *
     * @param int|array $store
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addStoreFilter($store)
    {
        $this->addFieldToFilter('store_id', array('in' => $store));

        return $this;
    }

    /**
     * This method will be not supported anymore
     *
     * @deprecated since 1.4.0.0
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function addStoreData()
    {
        return $this;
    }
}
