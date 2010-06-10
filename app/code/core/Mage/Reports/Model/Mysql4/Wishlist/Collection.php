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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Mysql4_Wishlist_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $wishlistTable;

    public function setWishlistTable($value)
    {
        $this->_wishlistTable = $value;
        return $this;
    }

    public function getWishlistTable()
    {
        return $this->_wishlistTable;
    }

    protected function _construct()
    {
        $this->_init('wishlist/wishlist');

        $this->setWishlistTable(Mage::getSingleton('core/resource')->getTableName('wishlist/wishlist'));
    }

    public function getWishlistCustomerCount()
    {
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->load();

        $customers = $collection->count();

        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()->from(array('wt' => $this->getWishlistTable()))
                    ->where('wt.customer_id=e.entity_id')
                    ->group('wt.wishlist_id');
        $collection->load();
        $count = $collection->count();
        return array(($count*100)/$customers, $count);
    }

    public function getSharedCount()
    {
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()->from(array('wt' => $this->getWishlistTable()))
                    ->where('wt.customer_id=e.entity_id')
                    ->where('wt.shared=1')
                    ->group('wt.wishlist_id');
        $collection->load();
        return $collection->count();
    }

}
