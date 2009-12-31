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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Mysql4_Wishlist_Product_Collection extends Mage_Wishlist_Model_Mysql4_Product_Collection
{
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }

    public function addWishlistCount()
    {
        $wishlistItemTable = Mage::getSingleton('core/resource')->getTableName('wishlist/item');

        $this->getSelect()
            ->from(array('wi' => $wishlistItemTable), 'count(wishlist_item_id) as wishlists')
            ->where('wi.product_id=e.entity_id')
            ->group('wi.product_id');

        $this->getEntity()->setStore(0);
        return $this;
    }

    public function getCustomerCount()
    {
        $this->getSelect()->reset();
        $this->getSelect()->from("wishlist", array("count(wishlist_id) as wishlist_cnt"))
                    ->group("wishlist.customer_id");
        return $this;//->getItems()->;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);

        $sql = $countSelect->__toString();

        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(*) from ', $sql);

        return $sql;
    }

    public function setOrder($attribute, $dir='desc')
    {
        switch ($attribute)
        {
        case 'wishlists':
            $this->getSelect()->order($attribute . ' ' . $dir);
            break;
        default:
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

}
